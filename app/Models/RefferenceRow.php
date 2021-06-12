<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class RefferenceRow extends Model
{
    protected $connection = 'firebird';

    /**
     * Таблица, связанная с моделью
     *
     * @var string
     */
    protected $table = 'REFERENCE_ROWS';

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

    public function cells()
    {
        return $this->hasMany(EntityRelationsValue::class, 'OWNER_ID', 'ENTITY_ID')
            ->where('CHILD_DISCRIMINATOR', 'CellEntity');
    }
}
