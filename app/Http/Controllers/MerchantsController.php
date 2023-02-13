<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Users;
use App\Http\Models\Transactions;
use App\Http\Models\Products;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\UsersStatus;
use App\Http\Utils\UsersType;

class MerchantsController extends Controller
{
     public function index(Request $request)
     {   
         try {
            
            $result = Users::whereStatus(UsersStatus::ACTIVE)
            ->whereType(UsersType::MERCHANT)
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
                'address' => $request->input('address'),
                'image' => $request->input('image'),
                'businessHours' => $request->input('businessHours'),
                'businessType' => $request->input('businessType'),
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
                    'type' => $request->input('type'),
                    'subscriptionPlan' => $request->input('subscriptionPlan'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'address' => $request->input('address'),
                    'image' => $request->input('image'),
                    'businessHours' => $request->input('businessHours'),
                    'businessType' => $request->input('businessType'),
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

     public function getMerhantTransaction(Request $request, $id)
     {   
         try {
             $result = Transactions::where('merchantId', $id)
             ->with(['customer', 'merchant', 'rider','products.product'])
             ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getMerhantProducts(Request $request, $id)
     {   
         try {
             $result = Products::where('merchantId', $id)
             ->with(['merchant'])
             ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getMerchantsBusinessType(Request $request, $id)
     {   
         try {
            $businessTypeCategoryId = $id;
            $result = Users::where('businessType', $businessTypeCategoryId)
            ->whereStatus(UsersStatus::ACTIVE)
            ->whereType(UsersType::MERCHANT)
            ->orderBy('created_at', 'desc')
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
}
