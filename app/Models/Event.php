<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class Event extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'EVENTS';

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


    public function object()
    {
        return $this->hasOne(GuardObject::class, 'ENTITY_ID', 'RESPONSIBILITY_REF');
    }

    public function entityStates()
    {
        return $this->hasMany(EntityState::class, 'EVENT_ID', 'ID');
    }

    public function device()
    {
        return $this->hasOne(Device::class, 'ENTITY_ID', 'HW_SOURCE_OBJECT');
    }

    public function zone()
    {
        return $this->hasOne(Guarzone::class, 'ENTITY_ID', 'SOURCE_OBJECT');
    }

    public function description()
    {
        return $this->hasOne(EventDescription::class, 'ID', 'DESCRIPTION_REF');
    }

    /*public function users()
    {
        $database = $this->getConnection()->getDatabaseName();
        return $this->belongsToMany(
            User::class,
            "$database.event_user",
            'event_id',
            'user_id',
            'ID',
            'id'
        );
    }*/
}
