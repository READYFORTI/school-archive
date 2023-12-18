<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginPage()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required','exists:users,username'],
            'password' => ['required']
        ]);

        // if (User::query()->where('username',$request->username)->whereNotNull('verified') == 0) {
        //     return back()->withErrors([
        //         'username' => 'You need to verify your account!',
        //     ])->onlyInput('username');
        // }

        if (User::where('username',$request->username)->whereNotNull('role_id')->count() == 0) {
            return back()->withErrors([
                'username' => 'Your account is not yet approved',
            ])->onlyInput('username');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $role = Auth::user()->role->role_name;
            if(in_array($role, [
                'Administrator', 
                'Document Control Custodian', 
                'Process Owner', 
                'Staff',
                'Human Resources',
                'Internal Lead Auditor',
                'Internal Auditor',
                'College Management Team',
                'Quality Assurance Director'
            ])){
                return redirect()->route('user.dashboard');
            }else{
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }
 
        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');

    }

    public function lg(Request $request)
    {
        Auth::logout();
 
        $request->session()->invalidate();
    
        $request->session()->regenerateToken();
    
        return redirect()->route('login-page');
    }

    public function unassigned()
    {
        return view('errors.unassigned');
    }
}
