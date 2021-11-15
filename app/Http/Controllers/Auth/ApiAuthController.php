<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ApiAuthController extends Controller
{
    // * Use once to create Admin User in Postman
    // * Admin User creates accounts for agents "Assistante"
    // * Admin User can be the top manager for all assitants
    // or one of the assistants who has the most experience

    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'type' => 'required|string'
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $request['password']=Hash::make($request['password']);
        $request['remember_token'] = Str::random(10);

        $user = User::create($request->toArray());
        $token = $user->createToken('token')->accessToken;

        $response = ['token' => $token];
        return response($response, 200);
    }

    public function login (Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()){
            return response(['errors'=>$validator->errors()->all()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('token')->accessToken;
                // $user->remember_token = hash('sha256', $token);
                $response = ['token' => $token];
                return response()->json([
                    'status'=>200,
                    'access_token'=>$token,
                    'user'=> [
                        'name'=>$user->name,
                        'lastname'=>$user->lastname,
                        'email'=>$user->email,
                        'type'=>$user->type
                    ]
                    //'token_type'=>'Bearer',
                    // 'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString()

                ],200);
            } else {
                $response = ["message" => "Password mismatch"];
                return response($response, 422);
            }
        } else {
            $response = ["message" =>'User does not exist'];
            return response($response, 422);
        }
    }

    public function logout(Request $request){
        $user = User::where('email', $request->email)->first();
        if(Auth::check() && Auth::user()->email == $user->email){
            $token = Auth::user()->token();
            $token->revoke();
            $response = ['message' => 'Déconnexion avec succès'];
            return response($response, 200);
        }else{
            return response()->json(["message"=>"Il n'existe aucun compte avec cet e-mail"],422);
        }

    }
}
