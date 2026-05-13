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
        $builder->where('c.statut', 'en_attente');
        $builder->orderBy('c.date_debut', 'ASC');
        $builder->orderBy('c.created_at', 'ASC');

        $demandes = $builder->get()->getResultArray();

        return view('rh/demandes', [
            'title' => 'Espace RH - Demandes en attente',
            'demandes' => $demandes,
            'totalDemandes' => count($demandes),
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