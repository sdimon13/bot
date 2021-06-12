<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class ObjectsEvent extends Model
{
    use ValidationTrait;

    protected $casts = [
        'contacts' => 'json',
    ];

    protected $unique = [
        ['event_id']
    ];

}
