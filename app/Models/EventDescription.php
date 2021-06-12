<?php

namespace App\Models;

use Firebird\Eloquent\Model;

class EventDescription extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'EVENT_DESCRIPTOINS';

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
}
