<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyCoinHistory extends Model
{
    use HasFactory;

    protected $table = "agency_coin_histories";

    protected $fillable = ['agency_id', 'coin', 'g_coin'];
}
