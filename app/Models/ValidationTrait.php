<?php

namespace App\Models;

trait ValidationTrait
{
    /**
     * @var array
     */
    protected static $fillableAdditional = [];

    /**
     * Предварительные правила валидации для всех атрибутов
     *
     * @var mixed
     */
    protected $defaultRules = 'scalar';

    /**
     * Названия полей
     *
     * @var array
     */
    protected $names = null;

    /**
     * @see \Illuminate\Database\Eloquent\Model
     */
    public function getTable()
    {
        if (! isset($this->table)) {
            return config('database.connections')[$this->getConnection()->getName()]['schema'].'.'.parent::getTable();
        }

        return $this->table;
    }

    /**
     * Custom - валидация
     *
     * @see Validator::after()
     * @param \Illuminate\Validation\Validator $validator
     */
    public function additionalValidation(\Illuminate\Validation\Validator $validator)
    {

    }

    /**
     * @see Illuminate\Database\Eloquent\Model
     *
     * @param string $key
     * @param string $value
     */
    public function setAttribute($key, $value)
    {
        if (isset($this->trim) && in_array($key, $this->trim) && is_scalar($value) && mb_strlen($value)) {
            $value = trim($value);
        }

        if (isset($this->nullable) && in_array($key, $this->nullable) && $value === '') {
            $value = null;
        }

        if (isset($this->dates) && in_array($key, $this->dates) && $value) {
            if ($value instanceof \Carbon\Carbon) {

                // ...

            } else {
                if (!is_numeric($value)) {
                    $value = strtotime($value);
                }

                $value = \Carbon\Carbon::parse(date($this->getDateFormat(), $value));
            }
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Валидация
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $request
     * @param array $additionalRules
     * @return \App\Extensions\ValidationTrait
     */
    public function scopeValidate(\Illuminate\Database\Eloquent\Builder $query, array $request = [], array $additionalRules = [])
    {
        if (!isset($this->names)) {
            $this->names = false;

            if (\App::getLocale()) {
                $file = $this->getTable();
                $file = explode('.', $file, 2);
                $file = \Arr::last($file);

                $names = trans('model/'.$file.'.fields');
                if (is_array($names)) {
                    $this->names = $names;
                }
            }
        }

        if ($request) {
            $clone = clone $this;
            $fillables = $this->getFillable();

            if (!$fillables && !static::$unguarded) {
                throw new \Illuminate\Database\Eloquent\MassAssignmentException('Fields must be set before validation with data');
            }

            foreach ($request as $name => &$value) {
                if (!in_array($name, $fillables) && !static::$unguarded) {
                    unset($request[$name]);
                    continue;
                }

                $clone[$name] = $value;
                $value = $clone->getAttributes()[$name]; // accessor`ы нам не нужны
            }
            unset($value);
        }

        $request = array_replace($this->attributes, $request);

        // unique маркеры
        $rules = $this->rules ?? [];
        $idReplace = null;
        if (isset($this->primaryKey) && !empty($request[$this->primaryKey])) {
            $idReplace = $request[$this->primaryKey];
        }

        foreach ($rules as &$rule) {
            if (!is_array($rule)) {
                if (is_null($idReplace)) {
                    $rule = str_replace(',%ID%', '', $rule);
                } else {
                    $rule = str_replace('%ID%', $idReplace, $rule);
                }
            } else {
                foreach ($rule as &$subRule) {
                    if (is_null($idReplace)) {
                        $subRule = str_replace(',%ID%', '', $subRule);
                    } else {
                        $subRule = str_replace('%ID%', $idReplace, $subRule);
                    }
                }
            }
        }
        unset($rule);

        $validator = \Validator::make($request, array_fill_keys(array_keys($request), $this->defaultRules));
        if ($this->names) {
            $validator->setAttributeNames($this->names);
        }

        if ($validator->passes() && $additionalRules) {
            $validator = \Validator::make($request, $additionalRules);
            if ($this->names) {
                $validator->setAttributeNames($this->names);
            }
        }

        if ($validator->passes()) {
            $validator = \Validator::make($request, $rules);
            if ($this->names) {
                $validator->setAttributeNames($this->names);
            }
        }

        if ($validator->passes()) {
            $validator->after([$this, 'additionalValidation']);
        }

        if ($validator->fails()) {
            throw new \App\Exceptions\ValidationException($validator);
        }

        return $this;
    }

    /**
     * Временный список атрибутов, доступных к массовому заполнению
     *
     * @return \App\Extensions\ValidationTrait
     */
    public function scopeFields()
    {
        $args = func_get_args();
        array_shift($args);

        if (!isset($args[0])) {
            $args[0] = [];
        }

        if (is_array($args[0])) {
            $args = $args[0];
        }

        $list = array_replace($this->fillable, $args);
        static::$fillableAdditional = &$list;

        return $this;
    }

    /**
     * @see \Illuminate\Database\Eloquent\Model::getFillable()
     *
     * @return array
     */
    public function getFillable()
    {
        if (!static::$fillableAdditional) {
            return parent::getFillable();
        }

        return static::$fillableAdditional;
    }

    /**
     * @see \Illuminate\Database\Eloquent\Model::save()
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        if (!empty($this->calculated)) {
            $this->handleCalculated($this->calculated, $this->getAttributes());
        }

        if ($this->exists && !empty($this->unchangeable)) {
            $this->handleUnchangeable($this->unchangeable, $this->getAttributes());
        }

        if (!empty($this->unique)) {
            $this->handleUnique($this->unique, $this->getAttributes());
        }

        $result = parent::save($options);

        $list = [];
        static::$fillableAdditional = &$list;

        return $result;
    }

    /**
     * @param array $calculated
     * @param array $newAttributes
     * @throws \App\Exceptions\ValidationException
     */
    protected function handleCalculated(array $calculated, array $newAttributes)
    {
        if ($this->isUnguarded()) {
            return;
        }

        foreach ($calculated as $name) {
            if (array_key_exists($name, $newAttributes)) {
                $new = $newAttributes[$name];
                $original = $this->getOriginal($name);

                if (is_double($new) || is_integer($new)) {
                    $new = (string)$new;
                }
                if (is_double($original) || is_integer($original)) {
                    $original = (string)$original;
                }

                if ($new === $original) {
                    continue;
                }

                $fieldName = $name;
                if (isset($this->names[$fieldName])) {
                    $fieldName = $this->names[$fieldName];
                }

                throw new \App\Exceptions\ValidationException([$name => trans('validation.calculated', ['name' => $fieldName])]);
            }
        }
    }

    /**
     * @param array $unchangeable
     * @param array $newAttributes
     * @throws \App\Exceptions\ValidationException
     */
    protected function handleUnchangeable(array $unchangeable, array $newAttributes)
    {
        if ($this->isUnguarded()) {
            return;
        }

        foreach ($unchangeable as $name) {
            if (array_key_exists($name, $newAttributes) && !$this->originalIsEquivalent($name, $newAttributes[$name])) {
                $fieldName = $name;
                if (isset($this->names[$fieldName])) {
                    $fieldName = $this->names[$fieldName];
                }

                throw new \App\Exceptions\ValidationException([$name => trans('validation.unchangeable', ['name' => $fieldName])]);
            }
        }
    }

    /**
     * @param array $uniques
     * @param array $newAttributes
     * @throws \App\Exceptions\ValidationException
     */
    protected function handleUnique(array $uniques, array $newAttributes)
    {
        foreach ($uniques as $unique) {
            $builder = new $this;

            foreach ($unique as $field) {
                if (isset($newAttributes[$field])) {
                    $builder = $builder->where($field, '=', $newAttributes[$field]);
                } else {
                    $builder = $builder->whereNull($field);
                }
            }

            if ($this->primaryKey && isset($newAttributes[$this->primaryKey])) {
                $builder = $builder->where($this->primaryKey, '!=', $newAttributes[$this->primaryKey]);
            }

            if ($builder->first()) {
                $params = ['names' => []];
                foreach ($unique as $field) {
                    if (isset($this->names[$field])) {
                        $params['names'][] = $this->names[$field];
                    } else {
                        $params['names'][] = $field;
                    }
                }
                $params['names'] = implode(', ', $params['names']);

                throw new \App\Exceptions\ValidationException([$field => trans('validation.unique_combine', $params)]);
            }
        }
    }
}
