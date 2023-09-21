<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuPaymentInformation extends Model
{
    use HasFactory;

    protected $fillable = ['amount', 'status', 'txnid', 'utr', 'payment_mode', 'payid', 'api_calling_status'];
}
