<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentInformation;

class PaymentController extends BaseController
{
    public function createPaymentInformation(Request $request)
    {
        $input = $request->all();
        $PaymentInformation = PaymentInformation::create([
            'apiStatus' => $input['apiStatus'],
            'msg' => $input['msg'],
            'txnStatus' => $input['txnStatus'],
            'txnDetails_amount' => $request['txnDetails']['amount'],
            'txnDetails_bankReferenceId' => $request['txnDetails']['bankReferenceId'],
            'txnDetails_merchantTxnId' => $request['txnDetails']['merchantTxnId'],
            'txnDetails_txnMessage' => $request['txnDetails']['txnMessage'],
            'txnDetails_utrNo' => $request['txnDetails']['utrNo'],
            'api_calling_status' => "false",
        ]);

        if($PaymentInformation) {
            return $this->sendResponseWithData($PaymentInformation, "Payment Information Created Successfully");
        }
        return $this->sendError("Something want wrong", "Something want wrong", []);
    }

    public function getPaymentInformation(Request $request)
    {
        $input = $request->all();
        $PaymentInformation = PaymentInformation::where([
            'txnDetails_merchantTxnId' => $request['merchantTxnId'],
        ])->first();

        if($PaymentInformation) {

            $infoData = PaymentInformation::where([
                'txnDetails_merchantTxnId' => $request['merchantTxnId'],
            ])->first();

            $infoData->api_calling_status = "true";
            $infoData->save();

            return $this->sendResponseWithData($PaymentInformation, "Payment Information Retrieved Successfully");
        }
        return $this->sendError("Something want wrong", "Something want wrong", []);
    }
}
