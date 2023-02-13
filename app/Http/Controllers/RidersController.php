<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Users;
use App\Http\Models\Transactions;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\UsersStatus;
use App\Http\Utils\UsersIsBusy;
use App\Http\Utils\UsersType;
use App\Http\Utils\TransactionsStatus;
use App\Events\EndPool;
use App\Events\RiderOrderConfirmation;
use App\Events\RiderOrderResponse;
use App\Events\CustomerWaitingResponseToRider;
use App\Events\CustomerRiderCurrentPosition;
use App\Events\RiderRequestAccepted;

class RidersController extends Controller
{
    public function getRiderInfo(Request $request, $id)
    {   
        try {
            $result = Users::whereStatus(UsersStatus::ACTIVE)
            ->whereId($id)
            ->first();
            event(new EndPool($result));
            return response()->json($result);
            } catch (Exception $e) {
        }
    }

    public function getLatestTransaction(Request $request)
    {   
        try {
            $auth = auth()->user();
            $riderId = $auth->id;
            $result = Transactions:: where('riderId', '=', $riderId)
           ->with(['merchant', 'customer', 'products.product', 'rider'])
           ->Where('status', '!=', TransactionsStatus::IDLE)
           ->Where('status', '!=', TransactionsStatus::DELIVERED)
           ->Where('status', '!=', TransactionsStatus::CANCELLED)
           ->Where('status', '!=', TransactionsStatus::REJECTED)
           //Get last record details
           ->orderBy('id', 'desc')->first();

            return response()->json($result);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

     public function index(Request $request)
     {   
         try {
            
            $result = Users::whereStatus(UsersStatus::ACTIVE)
            ->with(['wallet'])
            ->whereType(UsersType::RIDER)
            ->orderBy('created_at', 'desc')
            ->get();

             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
    
     public function getNearbyRiders(Request $request)
     {   
         try {
            
            $result = Users::whereStatus(UsersStatus::ACTIVE)
            ->whereType(UsersType::RIDER)
            ->orderBy('created_at', 'desc')
            ->get();

             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function create(Request $request)
     {
         try {
             $result = Users::create(array_filter([
                'facebookId' => $request->input('facebookId'),
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'lastName' => $request->input('lastName'),
                'firstName' => $request->input('firstName'),
                'middleName' => $request->input('middleName'),
                'phoneNumber' => $request->input('phoneNumber'),
                'password' => Hash::make($request->get('password')),
                'type' => $request->input('type'),
                'subscriptionPlan' => $request->input('subscriptionPlan'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'image' => $request->input('image'),
                'status' => $request->input('status')
            ]));
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function update(Request $request, $id)
     {
         try {
             $result = Users::whereId($id)
                 ->update(array_filter([
                    'facebookId' => $request->input('facebookId'),
                    'name' => $request->input('name'),
                    'email' => $request->input('email'),
                    'lastName' => $request->input('lastName'),
                    'firstName' => $request->input('firstName'),
                    'middleName' => $request->input('middleName'),
                    'phoneNumber' => $request->input('phoneNumber'),
                    'address' => $request->input('address'),
                    'type' => $request->input('type'),
                    'subscriptionPlan' => $request->input('subscriptionPlan'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'image' => $request->input('image'),
                    'status' => $request->input('status')
                 ]));
 
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function delete(Request $request, $id)
     {
         try {
             $result = Users::whereId($id)
             ->update([
                'status' => UsersStatus::INACTIVE,
             ]);
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function getRider(Request $request, $id)
     {   
         try {
             $result = Users::whereId($id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function riderTransactions(Request $request, $id)
     {   
         try {
             $riderId = $id;
             $transaction = Transactions::with(['merchant', 'customer', 'products.product', 'rider'])
             ->where("riderId", $riderId)
             ->orderBy('created_at', 'desc')
             ->get();

             return response()->json($transaction);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function confirmRider(Request $request)
     {   
         try {
             $transactionId = $request->input('transactionId');
             $riderId = $request->input('riderId');
             Transactions::whereId($transactionId)
             ->update(array_filter([
                'riderId' =>$riderId,
             ]));

             $transaction = Transactions::whereId($transactionId)
            //Get last record details
             ->orderBy('id', 'desc')->first();
            //->latest()
            //->first();

             // event listener
             // this will trigger a socket io in mobile app and will ask the rider to confirm or accept the order.
             event(new RiderOrderConfirmation($transaction));
             return response()->json($transaction);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function responseRider(Request $request, $id)
     {   
         try {
             // rider response enum : 0 = declined, 1 = accept
             $riderResponse = $request->input('response');
             $transactionId = $id;

             // if the rider response 1(accept) then update transaction status to 1(Confirmed)
             if($riderResponse) {
                Transactions::whereId($transactionId)
                ->update(array_filter([
                   'status' => TransactionsStatus::CONFIRMED, // status 1 = Confirmed
                ]));
             } 
             
             $transaction = Transactions::whereId($transactionId)
             ->with(['merchant', 'customer', 'product', 'rider'])

            //Get last record details
            ->orderBy('id', 'desc')->first();
            //->latest()
            //->first();

            // event listener
            // will throw the response of rider in mobile app listening to socket
             event(new RiderOrderResponse($transaction));
             
             return response()->json($transaction);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function notifyRiders(Request $request)
     {   
         try {
            $riderIds = $request->input('riderIds');
            $customerId = $request->input('customerId');
            $transactionId = $request->input('transactionId');
            $name = $request->input('name');
            $location = $request->input('location');
            $pickUpDestination = $request->input('pickUpDestination');
            $dropOffDestination = $request->input('dropOffDestination');
            $products = $request->input('products');

            $result = Transactions::whereId($transactionId)
                ->update(array_filter([
                    'pickUpDestination' => $pickUpDestination,
                    'dropOffDestination' => $dropOffDestination,
                ]));

            foreach ($riderIds as $riderId) {
                $data =  new \stdClass();
                $data->transactionId = $transactionId;
                $data->customerId = $customerId;
                $data->riderId = $riderId;
                $data->name = $name;
                $data->pickUpDestination = $pickUpDestination;
                $data->dropOffDestination = $dropOffDestination;
                $data->products = $products;

                event(new RiderOrderConfirmation($data));
            }

         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function acceptDeliveryRiders(Request $request)
     {   
         try {
            $transactionId = $request->input('transactionId');
            $serviceFee = $request->input('serviceFee');
            $auth = auth()->user();

            $transaction = Transactions::whereId($transactionId)
            ->with('rider')
            ->whereStatus(TransactionsStatus::IDLE)
            ->first();

            // check first if the transaction here is not take by any riders yet.
            if($transaction){
                // goes here if the transaction is idle

                // update riderId here to award
                $result = Transactions::whereId($transactionId)
                ->update(array_filter([
                   'riderId' => $auth -> id,
                   'status' => TransactionsStatus::CONFIRMED,
                   'serviceFee' => $serviceFee
                ]));

                // make rider busy
                $updateBusyStatus = Users::whereId($auth -> id)
                ->update(array_filter([
                   'isBusy' => 1 // value 1 represents the rider is now busy
                ]));

                // event listener
                // will notify customer that the rider confirmed.
                event(new CustomerWaitingResponseToRider($transaction));

                return response()->json($transaction);  

            }else {
                // if the transaction is taken by another rider it will
                // return valid notification error in riders mobile app
                // return 403 to user
                return response()->json(false, 403);
            }

         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function updateRiderCurrentPosition(Request $request)
     {   
         try {
            $data =  new \stdClass();
            $data->customerId = $request->input('customerId');
            $data->latitude = $request->input('latitude');
            $data->longitude = $request->input('longitude');
             // event listener
             event(new CustomerRiderCurrentPosition($data));
             return response()->json($data);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function riderAccepted(Request $request, $id)
     {
         try {
             $riderId = $id;
             $transactionId = $request->input('transactionId');
             $serviceFee = $request->input('serviceFee');
            
             // get rider busy status
             $riderBusyStatus = Users::whereId($riderId) -> first() -> isBusy;

             // check if rider is not busy
             if($riderBusyStatus !== UsersIsBusy::TRUE) {

                // update rider isbusy status to true
                Users::whereId($riderId)
                    ->update([  
                    'isBusy' => UsersIsBusy::TRUE,
                ]);

                // update transaction status to be assigned to rider
                $transaction = Transactions::whereId($transactionId)
                    ->update([
                    'riderId' => $riderId,
                    'serviceFee' => $serviceFee,
                    'status' => TransactionsStatus::CONFIRMED
                ]);

                   
                // get transaction status 
                $transaction = Transactions::whereId($transactionId)
                ->with('customer')
                ->first();
                
                // $data =  new \stdClass();
                // $data->riderId = $riderId;
                // $data->transactionId = $transactionId;
                // is rider request accepted?
                event(new RiderRequestAccepted($transaction));
                return response()->json(['message' => 'Successfully assigned to rider'], 200);
             }else {
                return response()->json(['message' => 'Rider already assigned'], 409);
             }
            
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
}
