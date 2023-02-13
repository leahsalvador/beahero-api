<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Users;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\UsersStatus;
use App\Http\Utils\UsersType;

class CorpAdminsController extends Controller
{
     public function index(Request $request)
     {   
         try {
            
            $result = Users::whereStatus(UsersStatus::ACTIVE)
            ->whereType(UsersType::ADMIN)
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
                    'type' => $request->input('type'),
                    'subscriptionPlan' => $request->input('subscriptionPlan'),
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
 
     public function getCustomer(Request $request, $id)
     {   
         try {
             $result = Users::whereId($id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
}
