<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\Categories;
use App\Http\Utils\CategoriesStatus;

class CategoriesController extends Controller
{
     public function index(Request $request)
     {   
         try {
            
            $result = Categories::whereStatus(CategoriesStatus::ACTIVE)
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
             $result = Categories::create(array_filter([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'userId' => $request->input('userId'),
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
             $result = Categories::whereId($id)
                 ->update(array_filter([
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'userId' => $request->input('userId'),
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
             $result = Categories::whereId($id)
             ->update([
                'status' => CategoriesStatus::INACTIVE,
             ]);
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function getCategory(Request $request, $id)
     {   
         try {
             $result = Categories::whereId($id)->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getCategoryByMerchant(Request $request, $id)
     {   
         try {
            
            $result = Categories::where('userId',$id)
            ->orderBy('created_at', 'desc')
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
}
