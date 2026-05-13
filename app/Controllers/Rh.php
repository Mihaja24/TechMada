<?php

namespace App\Controllers;

class Rh extends BaseController
{
    public function index()
    {
        return redirect()->to(site_url('rh/demandes'));
    }

    public function demandes()
    {
        $db = \Config\Database::connect();

        $departementId = trim((string) $this->request->getGet('departement_id'));
        $statut = trim((string) $this->request->getGet('statut'));
        $allowedStatuses = ['en_attente', 'approuvee', 'refusee'];

        if ($statut === '' || ! in_array($statut, $allowedStatuses, true)) {
            $statut = 'en_attente';
        }

        $builder = $db->table('conges c');
        $builder->select([
            'c.id',
            'c.date_debut',
            'c.date_fin',
            'c.nb_jours',
            'c.motif',
            'c.statut',
            'c.created_at',
            'e.nom AS employe_nom',
            'e.prenom AS employe_prenom',
            'e.email AS employe_email',
            'd.nom AS departement_nom',
            't.libelle AS type_conge_libelle',
        ]);
        $builder->join('employes e', 'e.id = c.employe_id', 'left');
        $builder->join('departements d', 'd.id = e.departement_id', 'left');
        $builder->join('types_conge t', 't.id = c.type_conge_id', 'left');

        if ($statut !== '') {
            $builder->where('c.statut', $statut);
        }

        if ($departementId !== '' && ctype_digit($departementId)) {
            $builder->where('e.departement_id', (int) $departementId);
        }

        $builder->orderBy('c.date_debut', 'ASC');
        $builder->orderBy('c.created_at', 'ASC');

        $demandes = $builder->get()->getResultArray();

        $departements = $db->table('departements')
            ->select('id, nom')
            ->orderBy('nom', 'ASC')
            ->get()
            ->getResultArray();

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
        $db = \Config\Database::connect();

        $annee = trim((string) $this->request->getGet('annee'));
        if ($annee === '' || ! ctype_digit($annee)) {
            $annee = (string) date('Y');
        }

        $typesConge = $db->table('types_conge')
            ->select('id, libelle, jours_annuels, deductible')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        $rows = $db->table('employes e')
            ->select([
                'e.id AS employe_id',
                'e.nom AS employe_nom',
                'e.prenom AS employe_prenom',
                'e.email AS employe_email',
                'e.role',
                'e.actif',
                'd.nom AS departement_nom',
                's.type_conge_id',
                's.jours_attribues',
                's.jours_pris',
            ])
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->join('soldes s', 's.employe_id = e.id AND s.annee = ' . $db->escape((int) $annee), 'left', false)
            ->orderBy('e.prenom', 'ASC')
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();

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

        $db = \Config\Database::connect();
        $db->transBegin();

        $conge = $db->table('conges')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (! $conge) {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Demande introuvable.');
        }

        if (($conge['statut'] ?? null) !== 'en_attente') {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Cette demande a déjà été traitée.');
        }

        $annee = (int) date('Y', strtotime((string) ($conge['date_debut'] ?? 'now')));
        $typeCongeId = (int) $conge['type_conge_id'];
        $employeId = (int) $conge['employe_id'];
        $nbJours = (float) $conge['nb_jours'];

        $solde = $db->table('soldes')
            ->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->get()
            ->getRowArray();

        if (! $solde) {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Aucun solde trouvé pour cette demande.');
        }

        $joursAttribues = (float) $solde['jours_attribues'];
        $joursPris = (float) $solde['jours_pris'];

        if ($joursPris + $nbJours > $joursAttribues) {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Solde insuffisant pour approuver cette demande.');
        }

        $db->table('soldes')
            ->where('id', $solde['id'])
            ->update([
                'jours_pris' => $joursPris + $nbJours,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        $db->table('conges')
            ->where('id', $id)
            ->update([
                'statut' => 'approuvee',
                'traite_par' => session()->get('user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (! $db->transStatus()) {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'La validation a échoué.');
        }

        $db->transCommit();

        return redirect()->to(site_url('rh/demandes'))->with('success', 'Demande approuvée avec succès.');
    }

    public function refuser(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to(site_url('rh/demandes'))->with('error', 'Méthode non autorisée.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        $conge = $db->table('conges')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (! $conge) {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Demande introuvable.');
        }

        if (($conge['statut'] ?? null) !== 'en_attente') {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Cette demande a déjà été traitée.');
        }

        $commentaireRh = trim((string) $this->request->getPost('commentaire_rh'));

        $db->table('conges')
            ->where('id', $id)
            ->update([
                'statut' => 'refusee',
                'commentaire_rh' => $commentaireRh !== '' ? $commentaireRh : null,
                'traite_par' => session()->get('user_id'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (! $db->transStatus()) {
            $db->transRollback();

            return redirect()->to(site_url('rh/demandes'))->with('error', 'Le refus a échoué.');
        }

        $db->transCommit();

        return redirect()->to(site_url('rh/demandes'))->with('success', 'Demande refusée.');
    }
}