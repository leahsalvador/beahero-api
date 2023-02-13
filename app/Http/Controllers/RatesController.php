<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Rates;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\RatesStatus;
use App\Http\Utils\RatesType;

class RatesController extends Controller
{
     public function index(Request $request)
     {   
         try {
            
            $result = Rates::orderBy('created_at', 'desc')
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function create(Request $request)
     {
         try {
            $rates = $request->input('rates');

            foreach ($rates as $rate) {
                $result = Rates::updateOrCreate(
                    ['type' => $rate['type']],
                    [
                        'type' => $rate['type'],
                        'amount' => $rate['amount'],
                        'status' => $rate['status']
                    ]
                );
                response()->json($rate);
            }
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function update(Request $request, $id)
     {
         try {
             $result = Rates::whereId($id)
                 ->update(array_filter([
                    'type' => $request->input('type'),
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
             $result = Rates::whereId($id)
             ->update([
                'status' => RatesStatus::INACTIVE,
             ]);
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function getRate(Request $request, $id)
     {   
         try {
             $result = Rates::whereId($id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
}
