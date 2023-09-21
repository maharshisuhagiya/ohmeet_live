<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StpPaymentInformation extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'user_id', 'order_number', 'amount', 'payment_status', 'payment_tx_id', 'udf1', 'udf2', 'udf3', 'api_calling_status'];
}
