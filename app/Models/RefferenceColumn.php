<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class RefferenceColumn extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'REFERENCE_COLUMNS';

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
