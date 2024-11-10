<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Password Reset</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .container { padding: 20px; }
        .otp-code { font-weight: bold; font-size: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Password Reset</h1>
        <p>{{ $body }}</p>
        <p class="otp-code">Your OTP: {{ $otpCode }}</p>
        <p>If you did not request a password reset, please ignore this email.</p>
    </div>
</body>
</html>
