<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Firebird\Eloquent\Model;

class ObjectUser extends Model
{
    use ValidationTrait;

    protected $unique = [
        ['object_id', 'user_id']
    ];

}
