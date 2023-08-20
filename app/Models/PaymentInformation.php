<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentInformation extends Model
{
    use HasFactory;

    protected $fillable = ['apiStatus', 'msg', 'txnStatus', 'txnDetails_amount', 'txnDetails_bankReferenceId', 'txnDetails_merchantTxnId', 'txnDetails_txnMessage', 'txnDetails_utrNo', 'api_calling_status'];
}
