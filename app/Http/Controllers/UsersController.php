<?php

    namespace App\Http\Controllers;

    use App\Http\Models\Users;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Hash;
    use Illuminate\Support\Facades\Validator;
    use JWTAuth;
    use Tymon\JWTAuth\Exceptions\JWTException;

    class UsersController extends Controller
    {
        public function authenticate(Request $request)
        {
            JWTAuth::setToken('foo.bar.baz');
            $credentials = $request->only('email', 'password');

            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }

            $user = auth()->user();
            $data['token'] = auth()->claims([
                'id' => $user->id,
                'name' => $user->name,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'middleName' => $user->middleName,
                'image' => $user->image,
                'type' => $user->type,
                'email' => $user->email,
                'isViewAds' => $user->isViewAds
            ])->attempt($credentials);
            return response()->json($data);
        }

        public function register(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);

            if($validator->fails()){
                    return response()->json($validator->errors()->toJson(), 400);
            }

            $user = Users::create(array_filter(
                [
                    'name' => $request->get('name'),
                    'facebookId' => $request->get('facebookId'),
                    'email' => $request->get('email'),
                    'type' => $request->get('type'),
                    'password' => Hash::make($request->get('password')),
                    'lastName' =>$request->get('lastName'), 
                    'firstName' =>$request->get('firstName'), 
                    'middleName' =>$request->get('middleName'), 
                    'phoneNumber' =>$request->get('phoneNumber'),
                    'subscriptionPlan' =>$request->get('subscriptionPlan'),
                    'isBusy' =>$request->get('isBusy'),
                    'image' =>$request->get('image'),
                    'status'=> 1 
                ]
            ));
          
            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user','token'),201);
        }

        public function loginWithFacebook(Request $request)
        {
            $user = Users::updateOrCreate(
                ['facebookId' => $request->get('facebookId')],
                array_filter([
                    'facebookId' => $request->get('facebookId'),
                    'name' => $request->get('name'),
                    'password' => Hash::make($request->get('password')),
                    'lastName' =>$request->get('lastName'), 
                    'firstName' =>$request->get('firstName'), 
                    'type' => $request->get('type'),
                    'status'=> 1 
                ])
            );

            $customClaims = [ 
                'id' => $user->id,
                'name' => $user->name,
                'firstName' => $user->firstName,
                'lastName' => $user->lastName,
                'middleName' => $user->middleName,
                'image' => $user->image,
                'type' => $user->type,
                'email' => $user->email
            ];
            
            $token = JWTAuth::customClaims($customClaims)->fromUser($user);
            return response()->json(['token' => $token]);
        }

        public function getAuthenticatedUser()
            {
                    try {
                        if (! $user = JWTAuth::parseToken()->authenticate()) {
                                return response()->json(['user_not_found'], 404);
                        }

                    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

                            return response()->json(['token_expired'], $e->getStatusCode(),401);

                    } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

                            return response()->json(['token_invalid'], $e->getStatusCode());

                    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

                            return response()->json(['token_absent'], $e->getStatusCode());

                    }

                    return response()->json(compact('user'));
            }
    }