<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $table = 'user_info';

    protected $fillable = [
        'telephone',
        'full_address'
    ];

    public function user() {
        return $this->belongsTo('App\User');
    }
}
