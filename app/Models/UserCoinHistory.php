<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoinHistory extends Model
{
    use HasFactory;

    protected $table = "user_coin_histories";

    protected $fillable = ['agency_id', 'user_id', 'coin', 'g_coin'];
}
