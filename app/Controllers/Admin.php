<?php

namespace App\Controllers;

use App\Models\DepartementModel;
use App\Models\EmployeModel;

class Admin extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('admin/employes'));
    }

    public function employes()
    {
        helper(['form', 'url']);

        $departementModel = new DepartementModel();
        $employeModel = new EmployeModel();
        
        $editId = trim((string) $this->request->getGet('edit'));

        $departements = $departementModel->getAllOrdered();
        $employes = $employeModel->getAllWithDepartement();

        $selectedEmploye = [
            'id' => '',
            'nom' => old('nom') ?? '',
            'prenom' => old('prenom') ?? '',
            'email' => old('email') ?? '',
            'role' => old('role') ?? 'employe',
            'departement_id' => old('departement_id') ?? '',
            'date_embauche' => old('date_embauche') ?? date('Y-m-d'),
            'password' => '',
        ];

        if ($editId !== '' && ctype_digit($editId)) {
            foreach ($employes as $employe) {
                if ((string) $employe['id'] === $editId) {
                    $selectedEmploye = array_merge($selectedEmploye, [
                        'id' => (int) $employe['id'],
                        'nom' => old('nom') ?? (string) $employe['nom'],
                        'prenom' => old('prenom') ?? (string) $employe['prenom'],
                        'email' => old('email') ?? (string) $employe['email'],
                        'role' => old('role') ?? (string) $employe['role'],
                        'departement_id' => old('departement_id') ?? (int) $employe['departement_id'],
                        'date_embauche' => old('date_embauche') ?? (string) $employe['date_embauche'],
                    ]);
                    break;
                }
            }
        }

        $counts = $employeModel->getActiveInactiveCounts();
        $activeCount = $counts['active'];
        $inactiveCount = $counts['inactive'];

        return view('admin/employes', [
            'title' => 'Admin - Employés',
            'employes' => $employes,
            'departements' => $departements,
            'selectedEmploye' => $selectedEmploye,
            'editMode' => $selectedEmploye['id'] !== '',
            'roleOptions' => [
                'admin' => 'Admin',
                'rh' => 'RH',
                'employe' => 'Employé',
            ],
            'activeCount' => $activeCount,
            'inactiveCount' => $inactiveCount,
            'totalCount' => $counts['total'],
        ]);
    }

    public function saveEmploye()
    {
        helper(['form', 'url']);

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(site_url('admin/employes'))->with('error', 'Méthode non autorisée.');
        }

        $id = trim((string) $this->request->getPost('id'));
        $isEdit = $id !== '' && ctype_digit($id);

        $rules = [
            'nom' => 'required|min_length[2]',
            'prenom' => 'required|min_length[2]',
            'email' => 'required|valid_email',
            'role' => 'required|in_list[admin,rh,employe]',
            'departement_id' => 'required|integer',
            'date_embauche' => 'required|valid_date[Y-m-d]',
        ];

        $password = trim((string) $this->request->getPost('password'));
        if (! $isEdit) {
            $rules['password'] = 'required|min_length[8]';
        } elseif ($password !== '') {
            $rules['password'] = 'min_length[8]';
        }

        if (! $this->validate($rules)) {
            return redirect()->to(site_url('admin/employes' . ($isEdit ? '?edit=' . $id : '')))
                ->withInput()
                ->with('error', 'Veuillez corriger les informations du formulaire.');
        }

        $employeModel = new EmployeModel();

        $email = trim((string) $this->request->getPost('email'));
        $departementId = (int) $this->request->getPost('departement_id');
        $dateEmbauche = (string) $this->request->getPost('date_embauche');

        $existingByEmail = $employeModel->findByEmail($email);

        if ($existingByEmail && (! $isEdit || (int) $existingByEmail['id'] !== (int) $id)) {
            return redirect()->to(site_url('admin/employes' . ($isEdit ? '?edit=' . $id : '')))
                ->withInput()
                ->with('error', 'Cette adresse email est déjà utilisée.');
        }

        $data = [
            'nom' => trim((string) $this->request->getPost('nom')),
            'prenom' => trim((string) $this->request->getPost('prenom')),
            'email' => $email,
            'role' => (string) $this->request->getPost('role'),
            'departement_id' => $departementId,
            'date_embauche' => $dateEmbauche,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (! $isEdit) {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
            $data['actif'] = 1;
            $data['created_at'] = date('Y-m-d H:i:s');

            $employeModel->insert($data);

            return redirect()->to(site_url('admin/employes'))->with('success', 'Employé créé avec succès.');
        }

        if ($password !== '') {
            $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $employeModel->update((int) $id, $data);

        return redirect()->to(site_url('admin/employes?edit=' . $id))->with('success', 'Employé mis à jour avec succès.');
    }

    public function deactivate(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(site_url('admin/employes'))->with('error', 'Méthode non autorisée.');
        }

        $employeModel = new EmployeModel();
        $employe = $employeModel->findById($id);

        if (! $employe) {
            return redirect()->to(site_url('admin/employes'))->with('error', 'Employé introuvable.');
        }

        $employeModel->deactivate($id);

        return redirect()->to(site_url('admin/employes'))->with('success', 'Employé désactivé.');
    }
}