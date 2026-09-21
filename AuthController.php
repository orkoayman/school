<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function login()
    {
        $email    = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');

        $userModel = new UserModel();
        $user      = $userModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'ইমেইল বা পাসওয়ার্ড ভুল।');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->with('error', 'আপনার অ্যাকাউন্ট নিষ্ক্রিয় করা আছে। অ্যাডমিনের সাথে যোগাযোগ করুন।');
        }

        session()->set([
            'user_id'     => $user['id'],
            'user_name'   => $user['name'],
            'role'        => $user['role'],
            'isLoggedIn'  => true,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login')->with('message', 'সফলভাবে লগআউট হয়েছে।');
    }
}
