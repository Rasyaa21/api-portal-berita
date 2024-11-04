<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ForgotPasswordRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public $successCode = 200;
    public function forgotPassword(ForgotPasswordRequest $req){
        try{
            $input=$req->only('email');
            $user=User::where('email',$input)->first();
            $user->notify(new ResetPasswordNotification());
            $success['succees']=true;
            return response()->json($success ,$this->successCode);
        } catch(\Exception $e){
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
