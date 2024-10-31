<?php

namespace App\Http\Helpers;

use App\Branch;
use App\Consignment;
use App\Country;
use App\Franchisee;
use App\Module;
use GuzzleHttp\Exception\GuzzleException;
use http\Client;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Milon\Barcode\DNS1D;

class AppHelper
{
    public static function countriesOptionList($countryId = null) {
        $countries = Country::select(['id', 'iso', 'name'])->get();

        $countriesList = array();
        foreach ($countries as $country) {
            $selected = '';

            if (!$countryId && $country->iso == 'IN') {
                $selected = 'selected';
            }

            if ($countryId == $country->id) {
                $selected = 'selected';
            }

            $countriesList[] = '<option value="'.$country->id.'"'.$selected.'>'.$country->name.'</option>';
        }

        return $countriesList;
    }


    public function calculateVolumetricWeight($length, $width, $height) {
        $volWeight = ($length * $width * $height) / 5000;
        return $volWeight;
    }

    /**
     * @param $officeType
     * @param $officeId
     * @param $sheetQuantity
     * @return array
     */
    public static function generateBarcode($officeType, $officeId, $sheetQuantity)
    {
        $quantity = $sheetQuantity*48;  //one sheet contains 12*4 = 48 bar-codes.
        if ($officeType == 'FR') {
            $office = Franchisee::select(['id', 'code'])->where('id', '=', $officeId)->first();
        } else {
            $office = Branch::select(['id', 'code'])->where('branch_type', '=', $officeType)->where('id', '=', $officeId)->first();
        }

        $consignment = Consignment::where('office_type', '=', $officeType)->where('office_id', '=', $officeId)->latest('id')->first();
        $batch = Consignment::select(['batch_id'])->latest('id')->first();
        if ($batch) {
            $batchNumber = ++$batch->batch_id;
        } else {
            $batchNumber = 1;
        }
        if ($consignment) {
            $lastConsgNum = $consignment->consg_number;
            $consgNum = preg_split('[-]', $lastConsgNum);
            $number = $consgNum[1]+1;
        } else {
            $number = '1';
        }
        $i = 1;
        $barCodes = array();

        while ($i <= $quantity) {
            $consgNumber = str_pad($number, 8, 0, 0);
            $consignment = new Consignment();
            $consignment->consg_number = $office->code.'-'.$consgNumber;
            $consignment->office_type = $officeType;
            $consignment->office_id = $officeId;
            $consignment->batch_id = $batchNumber;
            $consignment->save();

            $barCodes[] = DNS1D::getBarcodePNG($office->code.'-'.$consgNumber, "C128",1,50,array(1,1,1), true);
            $i++;
            $number++;
        }

        return $barCodes;
    }

    /*
     * Used for bulk booking
     */
    public static function generateSingleConsignment($officeType, $officeId, $batchId = null) {
       
        if ($officeType == 'FR') {
            $office = Franchisee::select(['id', 'code'])->where('id', '=', $officeId)->first();
        } else {
            $office = Branch::select(['id', 'code'])->where('branch_type', '=', $officeType)->where('id', '=', $officeId)->first();
           
        }

        $consignment = Consignment::where('office_type', '=', $officeType)->where('office_id', '=', $officeId)->latest('id')->first();
      
        if (!$batchId) {
            $batch = Consignment::select(['batch_id'])->latest('id')->first();
            if ($batch) {
                $batchId = ++$batch->batch_id;
            } else {
                $batchId = 1;
            }
        }

        if ($consignment) {
            $lastConsgNum = $consignment->consg_number;
            $consgNum = preg_split('[-]', $lastConsgNum);
            $number = $consgNum[1]+1;
        } else {
            $number = '1';
        }

        $consgNumber = str_pad($number, 8, 0, 0);
        $isConsgNumber = Consignment::where('consg_number', '=', $office->code.'-'.$consgNumber)->first();
        $consignment = new Consignment();
       // if(!$isConsgNumber){
            $consignment->consg_number = $office->code.'-'.$consgNumber;
            $consignment->office_type = $officeType;
            $consignment->office_id = $officeId;
            $consignment->batch_id = $batchId;
            $consignment->save();
        //}
        // else{
          
        //     $consignment->consg_number = $office->code.'-'.$consgNumber;
        //     $consignment->office_type = $officeType;
        //     $consignment->office_id = $officeId;
        //     $consignment->batch_id = $batchId;
        // }
        
        return $consignment;
        
    }

    public static function menuItems() {
        $user = Auth::user();
        $roles = $user->roles->pluck('id');

        $modules = Module::whereNull('parent_id')->with(['children' => function ($children) use ($roles){
            $children->whereHas('permissions', function ($q) use ($roles){
                $q->whereIn('role_id', $roles)->where('enabled', 1);
            });
        }])->get()->toArray();
        $enabledModules = collect($modules)->filter(function($module){
            return $module['is_enabled'] == true;
        })->values();

        return $enabledModules;
    }

    public static function sendTrackingMessage($CustomerName, $mobileNumbers, $AWB) {
        $client = new \GuzzleHttp\Client();
        if ($mobileNumbers) {

            $apiKey = urlencode('NTE0NTU0NmIzNzU1NGU1MzQ0NTM3NDY3Nzk1MDU0Nzk=');
            $numbers = array($mobileNumbers);
            $sender = urlencode('hthcin');
            $url = "www.hthc.co.in/track";
            $message = "Dear $CustomerName, your AWB # $AWB is booked and will be delivered by HTHC Courier, to track $url";
            $numbers = implode(',', $numbers);
        
            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        
            // Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
         
        } else {
            throw new \Exception("Mobile number not available", Response::HTTP_PRECONDITION_FAILED);
        }
    }

    public static function sendShipperCopy($CustomerName, $mobileNumbers, $consgNumber) {
        $client = new \GuzzleHttp\Client();
        if ($mobileNumbers) {

            //echo $consgNumber;exit;
            $apiKey = urlencode('NTE0NTU0NmIzNzU1NGU1MzQ0NTM3NDY3Nzk1MDU0Nzk=');
            $numbers = array($mobileNumbers);
            $sender = urlencode('hthcin');
            $url = "hthc.co.in/sp/s-".$consgNumber;
           
            $message = "Dear $CustomerName, Your shipment is booked with HTHC Courier. Please check the shipper copy $url";
            $numbers = implode(',', $numbers);
        
            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        
            // Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
           
        } else {
            throw new \Exception("Mobile number not available", Response::HTTP_PRECONDITION_FAILED);
        }
    }

    public static function sendBulkTrackingMessage($name, $mobileNumbers, $batchId) {
        $client = new \GuzzleHttp\Client();
        if ($mobileNumbers) {
            $username = env('MVAYOO_USERNAME');
            $senderID = env('MVAYOO_SENDERID');
            $url = env('MVAYOO_URL');
            $recipientNo = $mobileNumbers;
            //$encryptedBatchId = $batchId * env('ENCRYPTION_KEY');
            $encryptedBatchId = $batchId * 8;
            $trackingUrl = URL::to('/booking/'.$encryptedBatchId);

            $msgtxt = "Dear $name , We have booked your order and dispatched the consignments. Please see the shipment status $trackingUrl";

            try {
                $client->request('POST', $url,  ['form_params'=> [
                    'user' => $username,
                    'senderID' => $senderID,
                    'receipientno' => $recipientNo,
                    'msgtxt' => $msgtxt
                ]]);

                return true;
            } catch (GuzzleException $e) {
                $message = $e->getMessage();
                throw new \Exception($message, $e->getCode());
            }

        } else {
            throw new \Exception("Mobile number not available", Response::HTTP_PRECONDITION_FAILED);
        }
    }


    public static function sendDeliveryMessage($Name, $mobileNumbers, $consgNumber) {
        // $client = new \GuzzleHttp\Client();
        if ($mobileNumbers) {

            $apiKey = urlencode('NTE0NTU0NmIzNzU1NGU1MzQ0NTM3NDY3Nzk1MDU0Nzk=');
            $numbers = array($mobileNumbers);
            $sender = urlencode('hthcin');
            $message = "Dear $Name. Your consignment AWB # $consgNumber has been successfully delivered by HTHC Courier.";
            $numbers = implode(',', $numbers);
        
            // Prepare data for POST request
            $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
        
            // Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // $username =  env('TEXTLOCAL_USERNAME');
            // $hash =  env('TEXTLOCAL_HASH');
            // $AWB = $consgNumber;
            
            // // Config variables. Consult http://api.textlocal.in/docs for more info.
            // $test = "0";
            // // Data for text message. This is the text message data.
            // $sender = "hthcin"; // This is who the message appears to be from.
            // $message ="Dear $Name . Your consignement # $AWB has been successfully delivered.";
            // $message = urlencode($message);
            // $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$mobileNumbers."&test=".$test;
            // $ch = curl_init('http://api.textlocal.in/send/?');
            // curl_setopt($ch, CURLOPT_POST, true);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $result = curl_exec($ch); // This is the result from the API
            // curl_close($ch);

        } else {
            throw new \Exception("Mobile number not available", Response::HTTP_PRECONDITION_FAILED);
        }
    }

    public static function createBulkBookingsTable() {
        Schema::create('bulk_bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('consg_number');
            $table->string('consg_type');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('gender')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->text('add_line_1')->nullable();
            $table->text('add_line_2')->nullable();
            $table->string('landmark')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->unsignedBigInteger('pincode_id')->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('weight')->nullable();
            $table->string('booked_amount')->nullable();
            $table->string('amount_due')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('payment_id')->nullable();
            $table->boolean('insured')->nullable();
            $table->string('insured_by')->nullable();
            $table->string('declared_consg_value')->nullable();
            $table->boolean('sms_to_sender')->nullable();
            $table->boolean('sms_to_receiver')->nullable();

            $table->string('receiver_name')->nullable();
            $table->text('receiver_add_line_1')->nullable();
            $table->text('receiver_add_line_2')->nullable();
            $table->string('receiver_district')->nullable();
            $table->string('receiver_city')->nullable();
            $table->string('receiver_state')->nullable();
            $table->unsignedBigInteger('receiver_pincode_id')->nullable();
            $table->string('wrong_pincode')->nullable();
            $table->unsignedBigInteger('receiver_country_id')->nullable();
            $table->string('receiver_mobile_number')->nullable();
            $table->string('receiver_phone_number')->nullable();
            $table->string('receiver_email')->nullable();
            $table->boolean('has_error')->nullable();
            $table->timestamps();
            $table->temporary();
        });

        return true;
    }

    public static function generateRandomColors($quantity) {
        $colors = [];
        for ($i=1; $i<=$quantity; $i++) {
            $colors[] = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
        }

        return $colors;
    }

    public static function getStartEndDateFromWeek(array $weekNumbers)
    {
        $startEndDate = [];
        foreach ($weekNumbers as $weekNumber) {
            $dto = new \DateTime();
            $startDate = $dto->setISODate(date('Y'), $weekNumber)->format('d M');
            $endDate = $dto->setISODate(date('Y'), $weekNumber, 7)->format('d M');
            $startEndDate[] = $startDate.' - '.$endDate;
        }

        return $startEndDate;
    }
}
