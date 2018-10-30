<?php

namespace App\Http\Controllers\Api\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{

    /**
     * Return rules for other methods
     *
     */
    public function rules($method)
    {
        switch($method) {
            case 'login':
                {
                    return [
                        'email' => 'required|string|email|max:255|exists:users',
                        'password' => 'required|string|min:6',
                    ];
                }
            case 'register':
                {
                    return [
                        'name' => 'required|string|max:255',
                        'email' => 'required|string|email|max:255|unique:users',
                        'password' => 'required|string|min:6',
                        'type' => 'numeric'
                    ];
                }
            default:
                break;
        }
    }

    public function login(Request $request)
    {
        // validation data and show error
        $valid_data = $this->validate($request,$this->rules('login'));


        // check login
        if(!auth()->attempt($valid_data)){
            return response()->json(['errors' => 'Username Or password is wrong'],400);
        }

        // return response

        // With this line, the user can not login simultaneously with multiple devices
        auth()->user()->tokens()->delete();
        //___

        $user = auth()->user();
        $oauth_access_token = auth()->user()->createToken($user['email'])->accessToken;

        // merge user array with oauth_access_token
        $user_with_oauth_access_token = array_add($user,'oauth_access_token','Bearer ' . $oauth_access_token);
        return response()->json($user_with_oauth_access_token,200);

    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json(['message' => 'Logout success'],200);
    }

    public function register(Request $request)
    {
        $valid_data = $this->validate($request,$this->rules('register'));

        $valid_data['password'] = bcrypt($valid_data['password']);

        if (User::create($valid_data)){
            return response()->json(['message' => 'User created successfully'],200);
        } else {
            return response()->json(['message' => 'Something is wrong!'],400);
        }
    }

}


//        if(isset($valid_data['type'])){
//            $valid_data['type'] = (int) $valid_data['type'];
//        }