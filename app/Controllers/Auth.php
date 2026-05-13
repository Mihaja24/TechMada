<?php

namespace App\Controllers;

use App\Models\EmployeModel;

class Auth extends BaseController
{
    public function login()
    {
        helper(['form', 'url']);

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[3]',
            ];

            if (! $this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', 'Veuillez renseigner une adresse email valide et un mot de passe.');
            }

            $email = trim((string) $this->request->getPost('email'));
            $password = (string) $this->request->getPost('password');

            $employeModel = new EmployeModel();
            $user = $employeModel->findByEmailForLogin($email);

            if (! $user) {
                return redirect()->back()->withInput()->with('error', 'Email ou mot de passe invalide.');
            }

            $hash = $user['password_hash'] ?? ($user['password'] ?? null);
            if (! $hash || ! password_verify($password, $hash)) {
                return redirect()->back()->withInput()->with('error', 'Email ou mot de passe invalide.');
            }

            $sess = session();
            $sess->set([
                'user_id'    => $user['id'] ?? null,
                'email'      => $user['email'],
                'role'       => $user['role'] ?? null,
                'isLoggedIn' => true,
            ]);

            return redirect()->to('/')->with('success', 'Connexion réussie.');
        }

        return view('login/login', [
            'title' => 'Connexion',
        ]);
    }

    public function logout()
    {
        $session = session();
        $session->destroy();

        return redirect()->to(site_url('login'))->with('success', 'Vous avez été déconnecté.');
    }
}