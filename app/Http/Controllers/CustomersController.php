<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\UsersStatus;
use App\Http\Models\Transactions;
use App\Http\Utils\TransactionsStatus;
use App\Http\Utils\UsersType;
use App\Events\CustomerOrderConfirmation;

class CustomersController extends Controller
{
     public function index(Request $request)
     {   
         try {
            $result = Users::whereStatus(UsersStatus::ACTIVE)
            ->whereType(UsersType::CUSTOMER)
            ->orderBy('created_at', 'desc')
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function customerTransactions(Request $request, $id)
     {   
         try {
             $customerId = $id;
             $transaction = Transactions::with(['merchant', 'customer', 'products.product', 'rider'])
             ->where("customerId", $customerId)
             ->orderBy('created_at', 'desc')
             ->get();

             return response()->json($transaction);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function create(Request $request)
     {
         try {

            if (Users::where('email', '=', $request->input('email'))->exists()) {
                // user found
                // return 409 meaning that the email already exist
                return response()->json(["error" => "Email Already Exist"], 409);
             }else {
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
                    'image' => $request->input('image'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'isViewAds' => $request->input('isViewAds'),
                    'status' => $request->input('status')
                ]));
                 return response()->json($result);
             }
           
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
                    'isViewAds' => $request->input('isViewAds'),
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
 
     public function getCustomer(Request $request, $id)
     {   
         try {
             $result = Users::whereId($id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getLatestTransaction(Request $request)
     {   
         try {
            $currentUser = auth()->user();
            $result = Transactions::with(['merchant', 'customer', 'products.product', 'rider'])
            ->where('customerId', $currentUser->id)
            // ->orWhere('status', TransactionsStatus::CONFIRMED)

            // Do not return item that is idle or finished,
            // return only those item that are currently in progres
            // ->where('status', '!=', TransactionsStatus::IDLE)
            ->where('status', '!=', TransactionsStatus::DELIVERED)
            ->where('status', '!=', TransactionsStatus::CANCELLED)
            ->where('status', '!=', TransactionsStatus::REJECTED)
             //Get last record details
             ->orderBy('id', 'desc')->first();
            // ->latest()
            // ->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     
     
     public function notifyCustomer(Request $request, $id)
     {   
         try {

            $customerId = $id;
            $riderId = $request->input('riderId');
            $riderName = $request->input('riderName');
            $image = $request->input('image');
            $transactionId = $request->input('transactionId');
            $serviceFee = $request->input('serviceFee');

            $data =  new \stdClass();
            $data->customerId = $customerId;
            $data->riderId = $riderId;
            $data->riderName = $riderName;
            $data->image = $image;
            $data->transactionId = $transactionId;
            $data->serviceFee = $serviceFee;

            event(new CustomerOrderConfirmation($data));

            // return response()->json($data);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
}