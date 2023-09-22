<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallingHistory extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'opponent_user_id', 'call_duration', 'status', 'coin', 'g_coin', 'sum_of_coin', 'total_coin', 'total_g_coin'];
}
