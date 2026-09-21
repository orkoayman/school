<?php

namespace App\Controllers;

use App\Models\UserModel;

class UserController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'ইউজার ব্যবস্থাপনা',
            'users' => $this->userModel->orderBy('created_at', 'DESC')->findAll(),
        ];

        return view('settings/users', $data, ['saveData' => false]);
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[2]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role'     => 'required|in_list[admin,teacher]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->userModel->insert([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role'),
        ]);

        return redirect()->to('/settings/users')->with('message', 'নতুন ইউজার তৈরি করা হয়েছে।');
    }

    public function toggleStatus(int $id)
    {
        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->back()->with('error', 'ইউজার পাওয়া যায়নি।');
        }

        if ($user['id'] === (int) session('user_id')) {
            return redirect()->back()->with('error', 'নিজের অ্যাকাউন্ট নিষ্ক্রিয় করা যাবে না।');
        }

        $newStatus = $user['status'] === 'active' ? 'inactive' : 'active';
        $this->userModel->update($id, ['status' => $newStatus]);

        return redirect()->to('/settings/users')->with('message', 'ইউজারের অবস্থা পরিবর্তন করা হয়েছে।');
    }
}
