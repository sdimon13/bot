<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class GuardObject extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'GUARDOBJECTS';

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

    public function rows()
    {
        return $this->hasMany(EntityRelationsValue::class, 'OWNER_ID', 'ENTITY_ID')
            ->where('CHILD_DISCRIMINATOR', 'RowEntity');
    }

    public function entity()
    {
        return $this->hasOne(Entity::class, 'ID', 'ENTITY_ID');
    }
}
