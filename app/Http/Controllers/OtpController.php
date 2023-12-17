<?php

namespace App\Http\Controllers;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    public function verifyEmail($code) {
        $otp = Otp::query()
        ->where('code',$code)
        ->first();
        if ($otp && $otp->expiration >= time()) {
            User::query()
            ->where('id',$otp->user_id)
            ->update([
                'verified'=>now()
            ]);
            Otp::query()
            ->where('code',$code)
            ->delete();
            return view('result',[
                'status'=>true,
                'message'=>'Email Verified!'
            ]);
        }
        return view('result',[
            'status'=>false,
            'message'=>'Link expired!'
        ]);
    }
}
