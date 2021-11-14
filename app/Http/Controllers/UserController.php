<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Ticket;

class UserController extends Controller
{
    // Only the admin is allowed to create an agent or a customer
    // Registration can't be open publicly, it's only available for assistants
    // Another way to do this is to open registration for accounts and give the hand
    // to the admin to activate/validate them.
    public function createAgent(Request $request){
        if(Auth::check() ){
            if(Auth::user()->type == 'admin'){
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string|max:255',
                    'lastname' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255',
                    'password' => 'required|confirmed|string|min:6',
                    'type' => 'required|string'
                ]);
                if ($validator->fails())
                {
                    return response(['errors'=>$validator->errors()->all()], 422);
                }
                $request['password']=Hash::make($request['password']);

                $user = User::create($request->toArray());
                if($user){
                    return response()->json(["message"=>"Création d'un nouveau utilisateur avec succès"],200);
                }else{
                    return response()->json(["message"=>"Erreur inconnue, veuillez réessayer"],500);
                }
            }else{
                return response()->json(["message"=>"Vous n'avez pas le droit de créer un compte"],403);
            }
        }else{
            return response()->json(["message"=>"Vous n'êtes pas connecté"],403);
        }
    }





}
