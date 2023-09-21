<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentInformation;
use App\Models\GuPaymentInformation;
use App\Models\GuPaymentInformationNew;
use App\Models\StpPaymentInformation;
use App\Models\GameUpi;

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

    public function createGuPaymentInformation(Request $request)
    {
        $input = $request->all();
        $GuPaymentInformationNew = NULL;
        if($input['status'] == 1 || $input['status'] == 4)
        {
            $data = explode('A00A', $request->orderNum);
            if(count($data) != 2){
                $data = explode('R00M', $request->orderNum);
            }
            $GuPaymentInformationNew = GuPaymentInformationNew::create([
                'merchantId' => $input['merchantId'],
                'user_id' => count($data) == 2 ? $data[1] : NULL,
                'orderNum' => $input['orderNum'],
                'xpayOrderNum' => $input['xpayOrderNum'],
                'truePayCurrency' => $input['truePayCurrency'],
                'truePayAmount' => $input['truePayAmount'],
                'originalCurrency' => $input['originalCurrency'],
                'originalAmount' => $input['originalAmount'],
                'status' => $input['status'],
                'payType' => $input['payType'],
                'payChannel' => $input['payChannel'],
                'createtime' => $input['createtime'],
                'remark' => isset($input['remark']) ? $input['remark'] : NULL,
                'sign' => $input['sign'],
                'api_calling_status' => "false",
            ]);
            if($GuPaymentInformationNew) {
                return $this->sendResponseWithData($GuPaymentInformationNew, "Gu Payment Information Created Successfully");
            }
        }
        else
        {
            return $this->sendResponseWithData($GuPaymentInformationNew, "Gu Payment Information Created Successfully");
        }
        // $input = $request->all();
        // $GuPaymentInformation = GuPaymentInformation::create([
        //     'amount' => $input['amount'],
        //     'status' => $input['status'],
        //     'txnid' => $input['txnid'],
        //     'utr' => $input['utr'],
        //     'payment_mode' => $input['payment_mode'],
        //     'payid' => $input['payid'],
        //     'api_calling_status' => "false",
        // ]);

        // if($GuPaymentInformation) {
        //     return $this->sendResponseWithData($GuPaymentInformation, "Gu Payment Information Created Successfully");
        // }
        // return $this->sendError("Something want wrong", "Something want wrong", []);
    }

    public function getGuPaymentInformation(Request $request)
    {
        $input = $request->all();
        $GuPaymentInformationNew = GuPaymentInformationNew::where([
            'orderNum' => $request['orderNum'],
        ])->first();

        if($GuPaymentInformationNew) {

            $infoData = GuPaymentInformationNew::where([
                'orderNum' => $request['orderNum'],
            ])->first();

            $infoData->api_calling_status = "true";
            $infoData->save();

            return $this->sendResponseWithData($GuPaymentInformationNew, "Gu Payment Information Retrieved Successfully");
        }
        return $this->sendError("Something want wrong", "Something want wrong", []);
        // $input = $request->all();
        // $GuPaymentInformation = GuPaymentInformation::where([
        //     'txnid' => $request['txnid'],
        // ])->first();

        // if($GuPaymentInformation) {

        //     $infoData = GuPaymentInformation::where([
        //         'txnid' => $request['txnid'],
        //     ])->first();

        //     $infoData->api_calling_status = "true";
        //     $infoData->save();

        //     return $this->sendResponseWithData($GuPaymentInformation, "Gu Payment Information Retrieved Successfully");
        // }
        // return $this->sendError("Something want wrong", "Something want wrong", []);
    }

    public function createStpPaymentInformation(Request $request)
    {
        $input = $request->all();

        $payment_status = [
            1 => "Awaiting payment",
            2 => "Paid",
            3 => "Canceled",
            4 => "Refunded",
            5 => "Partially refunded",
            6 => "Incomplete",
            7 => "Failed",
            8 => "Partially paid",
        ];

        $StpPaymentInformation = NULL;
        if($input['payment_status'] != 7)
        {
            $data = explode('-', $request->order_number);
            $StpPaymentInformation = StpPaymentInformation::create([
                'order_id' => $input['order_id'],
                'user_id' => count($data) == 2 ? $data[1] : NULL,
                'order_number' => $input['order_number'],
                'amount' => $input['amount'],
                'payment_status' => $input['payment_status'],
                'payment_tx_id' => $input['payment_tx_id'],
                'udf1' => $input['udf1'],
                'udf2' => $input['udf2'],
                'udf3' => $input['udf3'],
                'api_calling_status' => "false",
            ]);
    
            if($StpPaymentInformation) {
                $StpPaymentInformation['payment_status'] = $payment_status[$StpPaymentInformation['payment_status']];
                return $this->sendResponseWithData($StpPaymentInformation, "Stp Payment Information Created Successfully");
            }
        }
        else
        {
            return $this->sendResponseWithData($StpPaymentInformation, "Stp Payment Information Created Successfully");
        }
        return $this->sendError("Something want wrong", "Something want wrong", []);
    }

    public function getStpPaymentInformation(Request $request)
    {
        $input = $request->all();

        $payment_status = [
            1 => "Awaiting payment",
            2 => "Paid",
            3 => "Canceled",
            4 => "Refunded",
            5 => "Partially refunded",
            6 => "Incomplete",
            7 => "Failed",
            8 => "Partially paid",
        ];

        $StpPaymentInformation = StpPaymentInformation::where([
            'payment_tx_id' => $request['payment_tx_id'],
        ])->first();

        if($StpPaymentInformation) {

            $infoData = StpPaymentInformation::where([
                'payment_tx_id' => $request['payment_tx_id'],
            ])->first();

            $infoData->api_calling_status = "true";
            $infoData->save();

            $StpPaymentInformation['payment_status'] = $payment_status[$StpPaymentInformation['payment_status']];
            return $this->sendResponseWithData($StpPaymentInformation, "Stp Payment Information Retrieved Successfully");
        }
        return $this->sendError("Something want wrong", "Something want wrong", []);
    }

    public function gameupi(Request $request)
    {
        $GameUpi = GameUpi::first();
        if($GameUpi)
        {
            $url = "https://api.gameupi.com/v/qr/collection";
            $ch = curl_init();
            $data = array(
                "token" => $GameUpi->gameupi_token,
                "type" => "dynamic",
                "amount" => $request->amount,
                "email" => $request->email,
                "name" => $request->name,
                "txnid" => $request->txnid,
                "callback" => "https://livhubcalls.in/ohmeet/api/create-gu-payment-information",
            );
            $new_data = json_encode($data);
    
            $array_options = array(
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $new_data,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array('Content-Type:application/json')
            );
    
            curl_setopt_array($ch,$array_options);
            $resp = curl_exec($ch);
            $final_decoded_data = json_decode($resp);
            curl_close($ch);
    
            if($resp){
                return $this->sendResponseWithData($resp, "Gu Payment Information Created Successfully");
            }
        }
        return $this->sendError("Something want wrong", "Something want wrong", []);
    }

    public function paymentHistory(Request $request)
    {
        $pg_data = [];
        $GuPaymentInformationNew = GuPaymentInformationNew::where('user_id', $request->user_id)->get();
        foreach($GuPaymentInformationNew as $gudata)
        {
            $paymentHistory['pg_identify'] = 'gu';
            $paymentHistory['order_num'] = $gudata->orderNum;
            $paymentHistory['amount'] = $gudata->originalAmount;
            $paymentHistory['api_calling_status'] = $gudata->api_calling_status;
            $paymentHistory['created_at'] = $gudata->created_at->format('Y-m-d H:i:s');
            $pg_data[] = $paymentHistory;
        }

        $StpPaymentInformation = StpPaymentInformation::where('user_id', $request->user_id)->get();
        foreach($StpPaymentInformation as $stpdata)
        {
            $paymentHistory['pg_identify'] = 'stp';
            $paymentHistory['order_num'] = $stpdata->order_number;
            $paymentHistory['amount'] = $stpdata->amount;
            $paymentHistory['api_calling_status'] = $stpdata->api_calling_status;
            $paymentHistory['created_at'] = $stpdata->created_at->format('Y-m-d H:i:s');
            $pg_data[] = $paymentHistory;
        }
        return $this->sendResponseWithData($pg_data, "Payment history retreated successfully");
    }
}
