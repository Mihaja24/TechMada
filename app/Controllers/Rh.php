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
}