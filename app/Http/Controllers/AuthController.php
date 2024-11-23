<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login() 
    {
        return view('login');
    }

    public function loginSubmit(Request $request)
    {
        $request->validate([
            'text_username' => 'required',
            'text_password' => 'required'
        ]);
        $username = $request->input('text_username');
        $password = $request->input('text_password');

        // check if user exists
        $user = User::where('username', $username)
                      ->where('deleted_at', null)
                      ->first();
        if (!$user) {
            return redirect()->back()->withInput()->with('loginError', 'Username or password invalid!');
        }
        // check if password is correct
        if (!password_verify($password, $user->password)) {
            return redirect()->back()->withInput()->with('loginError', 'Username or password invalid!');
        }
        // update last_login
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();
        // login user
        session([
            'user' => [
                'id' => $user->id,
                'username' => $user->username
            ]
            ]);
        // redirect to home page
        return redirect('/');
    }

    public function logout()
    {
        session()->forget('user');
        return redirect()->to('/login');
    }
}
