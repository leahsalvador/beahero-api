<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\RiderWallets;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\RiderWalletsStatus;

class RiderWalletsController extends Controller
{
     public function index(Request $request)
     {   
         try {
            $result = RiderWallets::get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function create(Request $request)
     {
         try {
             $result = RiderWallets::create(array_filter([
                'riderId' => $request->input('riderId'),
                'amount' => $request->input('amount'),
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
             $result = RiderWallets::whereId($id)
                 ->update(array_filter([
                    'riderId' => $request->input('riderId'),
                    'amount' => $request->input('amount'),
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
             $result = RiderWallets::whereId($id)
             ->update([
                'status' => RiderWalletsStatus::INACTIVE,
             ]);
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function getWallet(Request $request, $id)
     {   
         try {
             $result = RiderWallets::whereId($id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getRiderWallet(Request $request, $id)
     {   
         try {
             $result = RiderWallets::where("riderId", $id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

}
