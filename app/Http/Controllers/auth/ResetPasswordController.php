<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Ichtrojan\Otp\Otp;

class ResetPasswordController extends Controller
{
    public $successCode = 200;
    private $otp;
    public function __construct(){
        $this->otp = new Otp();
    }

    public function resetPassword(ResetPasswordRequest $req){
        try{
            $otp2 = (new Otp)->validate($req->email,$req->otp);
        if(! $otp2->status){
            return response()->json(['error'=>$otp2],401);

        }
        $user=User::where('email',$req->email)->first();
        $user->update(
            [
                'password'=>Hash::make($req->password)
            ]
        );
        $user->tokens()->delete();
        $success['succees']=true;
        return response()->json($success,200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ]);
        }

    }
}
