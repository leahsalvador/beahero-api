<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Http\Resources\AccountsResource;
use App\Accounts;
use App\Favorites;

class AccountsController extends Controller
{
   
    public function index(Request $request)
    {   
        $email = $request->input('email');
        $password = $request->input('password');
        $result = DB::table('accounts')
         ->where('email','=',$email)
         ->where('password','=',$password)
         ->exists();
         $return = [
            'status' => $result,
        ];
          return response()->json($return);
    }

    public function show(Accounts $accounts) : AccountsResource
    {
        return new AccountsResource($accounts);
    }

    public function create(Request $request)
    {
        $queryString = $request->all();
        try {
            $firstName = $queryString['firstName'];
            $lastName = $queryString['lastName'];
            $email = $queryString['email'];
            $password = $queryString['password'];
            $data[] = [
                'firstName'=>$firstName,
                "lastName"=>$lastName,
                "email"=>$email,
                "password"=>$password,
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ];
            DB::table('accounts')->insert($data);
            return response()->json($data);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function favorite($id)
    {
        // $queryString = $request->all();
        try {
            $result = Accounts::with(['favorite.song'])
            ->where('id','=', $id)
            ->get();

            return response()->json($result[0] -> favorite);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }
}
