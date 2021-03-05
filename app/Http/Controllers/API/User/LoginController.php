<?php

namespace App\Http\Controllers\API\User;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\LoginResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return User::GetError(config('constants.messages.user.invalid'));

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;
        if($user != null){
            //get User Permission and save permission in token
//            dd($token->scopes);
            $token->save();
            $user->authorization = $tokenResult->accessToken;
            return new LoginResource($user);
        }else{
            return User::GetError("No User found.");
        }

    }
}
