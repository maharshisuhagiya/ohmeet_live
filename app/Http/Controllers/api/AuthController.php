<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\CustomerDeviceToken;
use App\Models\Agency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Hash;

class AuthController extends BaseController
{
    public function hostRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'photo' => 'required',
            'agency_id' => 'required',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('email', $request->email)->where('role',5)->first();
        if($user)
        {
            return $this->sendError("Your email already exists please try to login", "Email exists", []);
        }

        $agency = Agency::where('id', $request->agency_id)->first();
        if(!$agency)
        {
            return $this->sendError("These agency not exist our records.", "Agency Not Found", []);
        }

        $user = New User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->profile_pic = $request->photo;
        $user->agency_id = $request->agency_id;
        $user->password = Hash::make($request->password);
        $user->role = 5;
        $user->save();

        $data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
        $data['user_status'] = 'new_user';
        $data['user_id'] = $user->id;
        $final_data = array();
        array_push($final_data,$data);
        return $this->sendResponseWithData($final_data, 'User registered successfully.');
    }

    public function hostLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6',
            'token' => 'required',
            'device_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('email', $request->email)->where('role',5)->first();
        if($user)
        {
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }

            if(Hash::check($request->password, $user->password)){

                $device = CustomerDeviceToken::where('user_id', $user->id)->first();
                if($device)
                {
                    $device->device_id = $request->device_id;
                    $device->token = $request->token;
                }
                else{
                    $device = new CustomerDeviceToken();
                    $device->user_id = $user->id;
                    $device->device_id = $request->device_id;
                    $device->token = $request->token;
                }
                $device->save();

                $user->device_id = $request->device_id;
                $user->save();
                
                $data['user'] =  $user;
                $data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
                $data['user_status'] = 'exist_user';
                $data['coin_price'] = '100';
                $data['light_min_value'] = '3';
                $final_data = array();
                array_push($final_data,$data);
    
                return $this->sendResponseWithData($final_data, 'User login successfully.');
            }
        }
        return $this->sendError("These credentials do not match our records.", "Wrong Credentials", []);
    }

    public function hostEdit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            'agency_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id', $request->user_id)->where('role',5)->first();
        if(!$user)
        {
            return $this->sendError("User Not Exist", "Not Found Error", []);
        }

        if($user->estatus != 1){
            return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
        }

        $agency = Agency::where('id', $request->agency_id)->first();
        if(!$agency)
        {
            return $this->sendError("These agency not exist our records.", "Agency Not Found", []);
        }

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->agency_id = $request->agency_id;
        if(isset($request->photo)){
            $user->profile_pic = $request->photo;
        }
        if($request->password){
            $user->password = Hash::make($request->password);
        }
        $user->save();

        $data['user'] = $user;
        $data['user_status'] = 'edit_user';
        $final_data = array();
        array_push($final_data,$data);
        return $this->sendResponseWithData($final_data, 'User Edited successfully.');
    }

    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'email' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }
        $email = $request->email;
        $user = User::where('email',$email)->where('id',$request->user_id)->where('role',3)->first();
        if($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }

            $user = User::find($request->user_id);
            $user->gmail_key = $request->gmail_key;
            $user->save();
            
            $data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
            $data['user_status'] = 'exist_user';    
            $final_data = array();
            array_push($final_data,$data);

            return $this->sendResponseWithData($final_data, 'User login successfully.');
        }else{
            
            $user = New User();
            $user->email = $request->email;
            $user->gmail_key = $request->gmail_key;
            $user->save();
            $data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
            $data['user_status'] = 'new_user';
            $final_data = array();
            array_push($final_data,$data);
            return $this->sendResponseWithData($final_data, 'User registered successfully.');
        }
    }


    public function update_token(Request $request){
        $validator = Validator::make($request->all(), [
            'device_id' => 'required',
            'token' => 'required',
            'device_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }
        //$user = User::where('device_id',$request->device_id)->where('estatus',1)->where('role',3)->first();
        $user = User::where('device_id',$request->device_id)->where('role',3)->first();
        
        if ($user){
            if($user["estatus"] == 2){
            return "Something Wrong";
            }
        }
        if (!$user){
            $user = New User();
            $user->device_id = $request->device_id;
            $user->role = 3;
            //$data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
        }
        
        if(isset($request->user_name) && $request->user_name)
        {
            $user_name = explode(' ', $request->user_name);
            if($user_name)
            {
                $user->first_name = isset($user_name[0]) ? $user_name[0] : NULL;
                $user->last_name = isset($user_name[1]) ? $user_name[1] : NULL;
            }
        }
        if(isset($request->email) && $request->email)
        {
            $user->email = $request->email ?? NULL;
        }
        $user->save();

        $device = CustomerDeviceToken::where('device_id',$request->device_id)->first();
        if ($device){
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        else{
            $device = new CustomerDeviceToken();
            $device->device_id = $request->device_id;
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        $device->save();
        //$this->user_login_log($request,$user->id);
        $user = User::where('id',$user->id)->first();
        $user->setAttribute('is_subscription', $user->tokenExpired());
        
        
        //$user->setAttribute('upi_id', "mab.037322023600049@axisbank");
        //$user->setAttribute('upi_id', "vyapar.168081011760@hdfcbank");
        $user->setAttribute('upi_id', "VYAPAR.167718060955@HDFCBANK");
        //$user->setAttribute('upi_id', "srijanhospitalityser.67087443@hdfcbank");
        
        
        $user->setAttribute('userRateOfMinute', "100");
        $user->setAttribute('key_abc', "653f94e30e154ddeb19a52d682843b8d");
        $user->setAttribute('in_update', true);
        $user->setAttribute('pay_status', true);
        $paymentName = array("online","payment","pay","onlin","pement","paymet","prament");
        $finalPayName = $paymentName[array_rand($paymentName)];
        $user->setAttribute('pay_title', $finalPayName);
        $user->setAttribute('inapp_option', "3");
        $user->setAttribute('payment_option', true);
        $user->setAttribute('key', "rzp_test_0HBq6YCxFrx1O8");
        $user->setAttribute('secret', "8JQxt85GYeQnBSZEfRPjnydG");
        return $this->sendResponseWithData($user,"Device Token updated.");
    }

  
    public function user_login_log(Request $request){

    
        $user = User::where('id',$request->user_id)->where('estatus',1)->first();
        if ($user)
        {
            if($user->latitude == ""){
                $user->country =  isset($request->country)?$request->country:"";
                $user->state =  isset($request->state)?$request->state:"";
                $user->city =  isset($request->city)?$request->city:"";
                $user->latitude =  isset($request->latitude)?$request->latitude:"";
                $user->longitude =  isset($request->longitude)?$request->longitude:"";
            }
            $user->last_login_date = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->save(); 

            $userlogin = New UserLogin();
            $userlogin->user_id =  $user->id;
            $userlogin->country =  isset($request->country)?$request->country:"";
            $userlogin->state =  isset($request->state)?$request->state:"";
            $userlogin->city =  isset($request->city)?$request->city:"";
            $userlogin->latitude =  isset($request->latitude)?$request->latitude:"";
            $userlogin->longitude =  isset($request->longitude)?$request->longitude:"";
            $userlogin->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $userlogin->save();
            return $this->sendResponseSuccess('log create successfully.');
        }
        else{
            return $this->sendError('User Not Found.', "verification Failed", []);
        }
    }
}
