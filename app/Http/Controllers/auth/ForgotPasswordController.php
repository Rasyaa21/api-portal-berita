<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\auth\ForgotPasswordRequest;
use App\Mail\SendResetTokenMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ForgotPasswordController extends Controller
{
    public $successCode = 200;

    public function forgotPassword(ForgotPasswordRequest $req)
    {
        try {
            $input = $req->only('email');
            $user = User::where('email', $input)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            Mail::to($user->email)->send(new SendResetTokenMail($user->email));
            Log::info("Email sent to: " . $user->email);
            return response()->json(['success' => true], $this->successCode);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
