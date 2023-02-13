<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Models\Products;
use App\Http\Models\Users;
use App\Http\Utils\ProductsStatus;
use App\Http\Utils\UsersType;

class ProductsController extends Controller
{
     public function index(Request $request)
     {   
         try {
            
            $result = Products::whereStatus(ProductsStatus::ACTIVE)
            ->with('category')
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
             $result = Products::create(array_filter([
                'categoryId' => $request->input('categoryId'),
                'merchantId' => $request->input('merchantId'),
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'image' => $request->input('image'),
                'stocks' => $request->input('stocks'),
                'meta' => $request->input('meta'),
                'price' => $request->input('price'),
                'cost' => $request->input('cost'),
                'rating' => $request->input('rating'),
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
             $result = Products::whereId($id)
                 ->update(array_filter([
                    'categoryId' => $request->input('categoryId'),
                    'merchantId' => $request->input('merchantId'),
                    'title' => $request->input('title'),
                    'description' => $request->input('description'),
                    'image' => $request->input('image'),
                    'stocks' => $request->input('stocks'),
                    'rating' => $request->input('rating'),
                    'price' => $request->input('price'),
                    'cost' => $request->input('cost'),
                    'meta' => $request->input('meta'),
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
             $result = Products::whereId($id)
             ->update([
                'status' => ProductsStatus::INACTIVE,
             ]);
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function getProduct(Request $request, $id)
     {   
         try {
             $result = Products::whereId($id)
             ->with('category')
             ->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getProductsByCategory(Request $request, $id)
     {   
         try {
            
            $result = Products::whereStatus(ProductsStatus::ACTIVE)
            ->where('categoryId',$id)
            ->with('category')
            ->with('merchant')
            ->orderBy('created_at', 'desc')
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getProductsByMerchant(Request $request, $id)
     {   
         try {
            $result = Products::whereStatus(ProductsStatus::ACTIVE)
            ->where('merchantId',$id)
            ->with('category')
            ->with('merchant')
            ->orderBy('created_at', 'desc')
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function getNearestMerchantProducts(Request $request)
     /*
    * @param1 : pass current latitude of the driver
    * @param2 : pass current longitude of the driver
    * @param3: pass the radius in meter/kilometer/miles within how much distance you wanted to filter
    */

     {   
         try {
            $latitude =  $request->input('latitude');
            $longitude =  $request->input('longitude');
            $radiusInKm = $request->input('radiusInKm');
            $categoryId = $request->input('categoryId');
            $searchTerm = $request->input('searchTerm');

            $merchant = Users::selectRaw("id, latitude, longitude ,
                        ( 6371 * acos( cos( radians(?) ) *
                        cos( radians( latitude ) )
                        * cos( radians( longitude ) - radians(?)
                        ) + sin(  radians(?) ) *
                        sin( radians( latitude ) ) )
                        ) AS distance", [$latitude, $longitude, $latitude])
            ->having("distance", "<", $radiusInKm)
            ->where('type', UsersType::MERCHANT)
            // ->orderBy("distance",'asc')
            // ->offset(0)
            // ->limit(20)
            ->get();

            // get Id only in an array
            $merchantIds = array_map(function($o) { return $o->id;}, $merchant->all());
            $products = Products::whereIn('merchantId',$merchantIds)
            ->with(['merchant'])
            ->whereStatus(ProductsStatus::ACTIVE)
            ->where('categoryId', $categoryId)
            
            // searchTerm
            ->where(function($query) use ($searchTerm){
                $query->where('title', 'like', '%' . $searchTerm . '%');
                $query->orWhere('description', 'like', '%' . $searchTerm . '%');
                $query->orWhere('meta', 'like', '%' . $searchTerm . '%');
            })

            // ->with('category')
            ->orderBy('created_at', 'desc')
            ->get();

             return response()->json($products);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
    }
