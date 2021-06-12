<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class EntityState extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'ENTITY_STATES';

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


    public function entity()
    {
        return $this->hasOne(Entity::class, 'ID', 'ENTITY_ID');
    }

    public function eventClassifier()
    {
        return $this->hasOne(EventClassifier::class, 'ID', 'CLASSIFIER_ID');
    }
}
