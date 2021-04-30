<?php

namespace App\Controllers;

class Auth extends BaseController
{
    protected $requestS;
    public function __construct()
    {
        helper('form');
        $this->validation = \Config\Services::validation();
        $this->session = session();
        $this->requestS = service("request");
    }

    public function register()
    {
        if ($this->requestS->getPost()) {
            // lakukan validasi untuk data yang dipost
            $data = $this->requestS->getPost();
            $validate = $this->validation->run($data, 'register');
            $errors = $this->validation->getErrors();

            if (!$errors) {
                $userModel = new \App\Models\UserModel();

                $user = new \App\Entities\User();

                $user->username = $this->requestS->getPost('username');
                $user->password = $this->requestS->getPost('password');
                $user->created_by = 0;
                $user->created_date = date("Y-m-d H:i:s");

                $userModel->save($user);
                return view('login');
            }
            $this->session->setFlashdata('errors', $errors);

        }
        return view('register');
    }

    public function login()
    {
        if ($this->requestS->getPost()) {
            // lakukan validasi untuk data yang dipost
            $data = $this->requestS->getPost();
            $validate = $this->validation->run($data, 'login');
            $errors = $this->validation->getErrors();

            if ($errors) {
                return view('login');
            }
            $userModel = new \App\Models\UserModel();
            $username = $this->requestS->getPost('username');
            $password = $this->requestS->getPost('password');

            $user = $userModel->where('username', $username)->first();

            if ($user) {
                $salt = $user->salt;
                if ($user->password !== md5($salt . $password)) {
                    $this->session->setFlashdata('errors', ['Password salah!']);

                } else {
                    $sessData = [
                        'username' => $user->username,
                        'id' => $user->id,
                        'role' => $user->role,
                        'isLoggedIn' => true,
                    ];

                    $this->session->set($sessData);

                    return redirect()->to(site_url('home/index'));
                }
            } else {

                $this->session->setFlashdata('errors', ['User tidak ditemukan!']);
            }
        }
        return view('login');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(site_url('auth/login'));
    }
}
