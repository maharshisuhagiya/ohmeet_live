<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuPaymentInformationNew extends Model
{
    use HasFactory;

    protected $table = 'gu_payment_information_new';

    protected $fillable = [
        'merchantId',
        'user_id',
        'orderNum',
        'xpayOrderNum',
        'truePayCurrency',
        'truePayAmount',
        'originalCurrency',
        'originalAmount',
        'status',
        'payType',
        'payChannel',
        'createtime',
        'remark',
        'sign',
        'api_calling_status'
    ];
}
