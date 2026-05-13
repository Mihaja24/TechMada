<?php

namespace App\Controllers;

use App\Models\CongeModel;
use App\Models\DepartementModel;
use App\Models\EmployeModel;
use App\Models\TypeCongeModel;

class Rh extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('rh/demandes'));
    }

    public function demandes()
    {
        $congeModel = new CongeModel();
        $departementModel = new DepartementModel();

        $departementId = trim((string) $this->request->getGet('departement_id'));
        $statut = trim((string) $this->request->getGet('statut'));
        $allowedStatuses = ['en_attente', 'approuvee', 'refusee'];

        if ($statut === '' || ! in_array($statut, $allowedStatuses, true)) {
            $statut = 'en_attente';
        }

        $demandes = $congeModel->getDemandesWithDetails($statut, $departementId !== '' && ctype_digit($departementId) ? (int) $departementId : null);
        $departements = $departementModel->getAllOrdered();

        $selectedDepartementNom = 'Tous';
        if ($departementId !== '' && ctype_digit($departementId)) {
            foreach ($departements as $departement) {
                if ((string) $departement['id'] === $departementId) {
                    $selectedDepartementNom = (string) $departement['nom'];
                    break;
                }
            }
        }

        return view('rh/demandes', [
            'title' => 'Espace RH - Demandes',
            'demandes' => $demandes,
            'totalDemandes' => count($demandes),
            'departements' => $departements,
            'selectedDepartementId' => $departementId !== '' && ctype_digit($departementId) ? (int) $departementId : '',
            'selectedDepartementNom' => $selectedDepartementNom,
            'selectedStatut' => $statut,
            'selectedStatutLabel' => [
                'en_attente' => 'En attente',
                'approuvee' => 'Approuvée',
                'refusee' => 'Refusée',
            ][$statut] ?? ucfirst($statut),
            'statusOptions' => [
                'en_attente' => 'En attente',
                'approuvee' => 'Approuvée',
                'refusee' => 'Refusée',
            ],
        ]);
    }

    public function employes()
    {
        $typeCongeModel = new TypeCongeModel();
        $employeModel = new EmployeModel();

        $annee = trim((string) $this->request->getGet('annee'));
        if ($annee === '' || ! ctype_digit($annee)) {
            $annee = (string) date('Y');
        }

        $typesConge = $typeCongeModel->getAllOrdered();
        $rows = $employeModel->getEmployesWithSoldes((int) $annee);

        $employes = [];
        foreach ($rows as $row) {
            $employeId = (int) $row['employe_id'];
            if (! isset($employes[$employeId])) {
                $employes[$employeId] = [
                    'id' => $employeId,
                    'nom_complet' => trim((string) $row['employe_prenom'] . ' ' . (string) $row['employe_nom']),
                    'email' => (string) $row['employe_email'],
                    'role' => (string) $row['role'],
                    'actif' => (int) $row['actif'],
                    'departement_nom' => (string) ($row['departement_nom'] ?? 'Non défini'),
                    'soldes' => [],
                ];
            }

            if (! empty($row['type_conge_id'])) {
                $typeCongeId = (int) $row['type_conge_id'];
                $employes[$employeId]['soldes'][$typeCongeId] = [
                    'jours_attribues' => (float) ($row['jours_attribues'] ?? 0),
                    'jours_pris' => (float) ($row['jours_pris'] ?? 0),
                ];
            }
        }

        return view('rh/employes', [
            'title' => 'Espace RH - Soldes employés',
            'annee' => (int) $annee,
            'typesConge' => $typesConge,
            'employes' => array_values($employes),
            'totalEmployes' => count($employes),
        ]);
    }

    public function approuver(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Méthode non autorisée.');
        }

        $congeModel = new CongeModel();
        $traitePar = session()->get('user_id');
        
        $result = $congeModel->approuver($id, $traitePar);

        if (! $result['success']) {
            return redirect()->to(site_url('rh/demandes'))->with('error', $result['message']);
        }

        return redirect()->to(site_url('rh/demandes'))->with('success', $result['message']);
    }

    public function refuser(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Méthode non autorisée.');
        }

        $congeModel = new CongeModel();
        $traitePar = session()->get('user_id');
        $commentaireRh = trim((string) $this->request->getPost('commentaire_rh'));
        
        $result = $congeModel->refuser($id, $traitePar, $commentaireRh !== '' ? $commentaireRh : null);

        if (! $result['success']) {
            return redirect()->to(site_url('rh/demandes'))->with('error', $result['message']);
        }

        return redirect()->to(site_url('rh/demandes'))->with('success', $result['message']);
    }
}