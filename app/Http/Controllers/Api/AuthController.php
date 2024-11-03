<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\UserDataResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController
{
    public $successCode = 200;


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'message' => 'Account successfully registered',
                'user' => new UserDataResource($user),
                'access_token' => $token,
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $req){
        if (! Auth::attempt(['email' => $req->email, 'password' => $req->password])){
            return response()->json([
                'message' => 'unauthorized'
            ], 401);
        }
        $user = User::where('email', $req->email)->first();
        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'message' => 'login success',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], $this->successCode);
    }

    public function logout(Request $request){
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => 'token successfully deleted'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function detail(){
        try{
            $user = Auth::user();
            return response()->json(['data' => new UserDataResource($user)], $this->successCode);
        } catch (\Exception $e){
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
