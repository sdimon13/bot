<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    use ValidationTrait;


    protected $rules = [
        'name' => 'required|string|max:255',
        'telegram_id' => 'required|integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'telegram_id', 'type_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $unique = [
        ['telegram_id'], ['email']
    ];

    public function getTypeTitleAttribute()
    {
        $types = [
            1 => 'Пользователь',
            2 => 'Администратор',
        ];

        return $types[$this->type_id];
    }

    public function setEmailAttribute()
    {
        $this->attributes['email'] = $this->name . '@bot.api';
    }

    public function objects()
    {
        return $this->hasMany(ObjectUser::class);
    }
}
