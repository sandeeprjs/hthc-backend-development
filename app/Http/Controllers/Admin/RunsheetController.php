<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Manifest;
use App\Booking;
use Illuminate\Support\Facades\Log;

class RunsheetController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware(['auth', 'role'])->except('runsheetValidation', 'createRunsheet');
    }
     /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();
        $loggedOffice = $this->loggedInOffice();
        return view('runsheet.create',compact('user','loggedOffice'));
    }

    public function loggedInOffice(){

        $user = auth()->user();
        $loggedOffice = '0';
        if($user->office_type == "BR" || $user->office_type == "HO"){
            if($user->branch){
                 $loggedOffice = $user->branch;
            }
        }
        if($user->office_type == "FR"){
            if($user->franchisee){
                  $loggedOffice = $user->franchisee;
            }
        }

        return $loggedOffice;
    }

    /**
     * to validatation for runsheet
     */
    public function runsheetValidation(Request $request){

        $consg_number = $request->input('manifest_number');
        $user = auth()->user();
        $officeType = $user->office_type;
        $officeId =  $user->office_id;

        $isOutGoingManifest =  true;
        $isBooking = $this->isBooking($consg_number);

        Log::info("Check validation 1", ['isBooking' => $isBooking]);

        if(!$isBooking){
            return response()->json([
                'status' => 0,
                'message' => 'There is no booking entry for this consignment.',

            ], 200);
        }
        Log::info("Check validation 2");

        if($officeType != 'FR'){
            $isOutGoingManifest = $this->isOutGoingManifest(
                consg_number: $consg_number,
                loggedOfficeId: $officeId,
                officeType: $officeType
            );            if(!$isOutGoingManifest){
                return response()->json([
                    'status' => 0,
                    'message' => 'There is no outgoing manifest for this consignment',
                ], 200);
            }
        }
        $isReturn = $this->isConsignmentReturn($consg_number);
        if(!$isReturn){
            $isOutForDelivery = $this->isAlreadyOutForDelivery($consg_number);
            if($isOutForDelivery){
                return response()->json([
                    'status' => 0,
                    'message' => 'This consignment already out for delivery',
                    ], 200);
            }
        }
        $no_of_attempts = $this->getNoOfAttempts($consg_number);

        if($no_of_attempts > 4){
            return response()->json([
                'status' => 0,
                'message' => 'Number of Attempts reached two times',
                ], 200);
        }
        if($officeType == 'BR' || $officeType == 'HO'){
            $manifest = Manifest::where('last_mile_delivery', '=', 1)
            ->where('manifest_type', '=', 'O')
            ->where('status', '=', 'Arrived to Destination Hub')
            ->where('manifest_number', '=', $consg_number)->first();
            if(!$manifest){
                return response()->json([
                    'status' => 0,
                    'message' => 'Not yet reached to Destination Hub',

                ], 200);
            }
        }
        return response()->json([
            'status' => 1,
            'message' => 'proceed',
        ], 200);
    }

    public function getNoOfAttempts($consg_number){
        $bookings = Booking::where('consg_number', '=', $consg_number)
        ->first();
        $no_of_attempts = 0;
        if($bookings){
            if(isset($bookings->delivery->no_of_attempts)){
                $no_of_attempts = $bookings->delivery->no_of_attempts;
            }
            return $no_of_attempts;
        }
        return '0';
    }
    public function isConsignmentReturn($consg_number){
        $manifest = Manifest::where('manifest_number', '=', $consg_number)
        ->where('manifest_type', '=', 'R')
        ->first();

        if($manifest){
            return true;
        }

        return false;
    }

    public function isOutGoingManifest($consg_number, $loggedOfficeId, $officeType)
    {
        Log::info('Validating outgoing manifest', [
            'consg_number' => $consg_number,
            'loggedOfficeId' => $loggedOfficeId,
            'officeType' => $officeType,
        ]);
        // Check if the logged-in office is the sender for outgoing manifests
        $manifest = Manifest::where('manifest_number', '=', $consg_number)
            ->where('manifest_type', '=', 'O')
            ->where('sender_id', '=', $loggedOfficeId)
            ->where('sender_type', '=', $officeType)
            ->first();

        if ($manifest) {
            Log::info('Outgoing manifest found', ['manifest' => $manifest]);
            return true;
        }

        Log::info('Outgoing manifest not found');
        return false;
    }


    public function isBooking($consg_number){
        $booking = Booking::where('consg_number', '=', $consg_number)
        ->first();

        if($booking){
            return true;
        }

        return false;

    }


    public function isAlreadyOutForDelivery($consg_number){
        $manifest = Manifest::where('last_mile_delivery', '=', 1)
        ->where('status', '=', 'Out for Delivery')
        ->where('manifest_number', '=', $consg_number)->first();
        if($manifest){
            return true;
        }
        return false;
    }

    /**
     * runsheet creation
     */
    public function createRunsheet(Request $request){

        $consg_numbers = $request->input('manifest_number');
        $user = auth()->user();
        $officeType = $user->office_type;
        $loggedOfficeId =  $user->office_id;
        $delivery_user_id = $user->id;

        $not_booked = null;
        $not_added_in_sheet = null;
        $no_outgoing = null;
        $added_to_runsheet = null;
        $already_added = null;
        $customer_view =1;
        $isOutGoingManifest =  true;
        if(!$consg_numbers){
            return redirect()->route('runsheet.add');
        }
        foreach($consg_numbers as $consg_number){
            if($isOutGoingManifest){

                Booking::where('consg_number', '=', $consg_number)->update(array('status' => 'Out for Delivery'));

                if($officeType == 'FR'){
                    $manifest = Manifest::where('manifest_type', '=', 'O')
                    ->where('receiver_type', '=', $officeType)
                    ->where('receiver_id', '=', $loggedOfficeId)
                    ->where('manifest_number', '=', $consg_number)->first();
                    $manifest->last_mile_delivery = 1;
                    Manifest::where('id', '=', $manifest->id)->update(array('status' => 'Arrived to Destination Hub'));
                }
                if($officeType == 'BR' || $officeType == 'HO'){
                    $manifest = Manifest::where('last_mile_delivery', '=', 1)
                    ->where('manifest_type', '=', 'O')
                    ->where('status', '=', 'Arrived to Destination Hub')
                    ->where('manifest_number', '=', $consg_number)->first();
                }

                $outForDelivery = Manifest::create([
                        'manifest_type' => 'D',
                        'manifest_number' => $manifest->manifest_number,
                        'origin_branch_id' => $manifest->origin_branch_id,
                        // 'origin_pincode_id' => $data['origin_pincode_id'][$key],
                        'dest_branch_id' => $manifest->dest_branch_id,
                        // 'dest_pincode_id' => $data['dest_pincode_id'][$key],
                        'sender_id' => $manifest->sender_id,
                        'receiver_id' => $manifest->receiver_id,
                        'sender_type' => $manifest->sender_type,
                        'receiver_type' => $manifest->receiver_type,
                        'consg_number_id' => 0,
                        'last_mile_delivery' => $manifest->last_mile_delivery,
                        'delivery_user_id' => $delivery_user_id,
                        'customer_view' => $customer_view,
                        'status' => 'Out for Delivery',
                        'user_id'=>$delivery_user_id,
                        'office_type'=>$officeType,
                        'office_id'=>$loggedOfficeId,
                    // 'remarks' => $data['remarks']
                    'remarks' =>''
                    ]);

                    if($outForDelivery){
                        $added_to_runsheet[] = $consg_number;

                    }
            }
       }

       return redirect()->route('runsheet.add')->with('success', 'Runsheet has been added successfully');

    }
}
