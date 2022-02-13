<?php

namespace App\Http\Controllers\Admin\Users;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function index() {
        return view('admin.users.login', [
            'title' => 'Đăng nhập hệ thống'
        ]);
    }

    public function store (Request $request) {
        $this->validate($request, [
            'email' => 'required|email:filter',
            'password' => 'required'
        ]);

        if(Auth::attempt
            (['email' => $request->input('email'), 
            'password' => $request->input('password')], 
            $request->input('remember'))) {

                return redirect()->route('admin');
        }

        session()->flash('error', 'Email hoặc Password không chính xác');

        return redirect()->back();
    }
}