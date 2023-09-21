<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameUpi extends Model
{
    use HasFactory;

    protected $fillable = ['gameupi_token'];
}
