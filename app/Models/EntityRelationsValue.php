<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class EntityRelationsValue extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'ENTITIES_RELATIONS_VALUES';

    /**
     * Первичный ключ модели
     *
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * Наша модель не имеет временной метки
     *
     * @var bool
     */
    public $timestamps = false;

    public function refferenceRow()
    {
        return $this->hasOne(RefferenceRow::class, 'ENTITY_ID', 'CHILD_ID');
    }

    public function refferenceCell()
    {
        return $this->hasOne(RefferenceCell::class, 'ENTITY_ID', 'CHILD_ID')
            ->whereNotNull('CELL_VALUE_BLOB');
    }

    public function refferenceColumn()
    {
        return $this->hasOne(RefferenceColumn::class, 'ENTITY_ID', 'CHILD_ID');
    }
}
