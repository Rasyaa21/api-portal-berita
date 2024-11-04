<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ResetPasswordRequest;
use App\Models\User;
use Ichtrojan\Otp\Models\Otp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public $successCode = 200;
    private $otp;
    public function __construct(){
        $this->otp = new Otp();
    }

    public function resetPassword(ResetPasswordRequest $req){
        $otp2 = $this->otp->validate($req->email, $req->otp);
        if (!$otp2->status){
            return response()->json(['error'=>$otp2] ,401);
        }
        $user = User::where('email', $req->email)->first();
        if(!$user){
            return response()->json([
                'error' => 'email doesnt exist'
            ], 404);
        }
        $user->update([
            'password' => Hash::make($req->password)
        ]);
        $user->tokens()->delete();
        $success['succees']=true;
        return response()->json($success,$this->successCode);
    }
}
