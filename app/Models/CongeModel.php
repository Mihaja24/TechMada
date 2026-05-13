<?php

namespace App\Models;

use CodeIgniter\Model;

class CongeModel extends Model
{
    protected $table = 'conges';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_rh',
        'traite_par',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getDemandesWithDetails(?string $statut = null, ?int $departementId = null)
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

        if ($statut !== null && $statut !== '') {
            $builder->where('c.statut', $statut);
        }

        if ($departementId !== null && $departementId !== '') {
            $builder->where('e.departement_id', (int) $departementId);
        }

        $builder->orderBy('c.date_debut', 'ASC');
        $builder->orderBy('c.created_at', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function findById(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function approuver(int $id, int $traitePar)
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        $conge = $this->findById($id);

        if (! $conge) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Demande introuvable.'];
        }

        if (($conge['statut'] ?? null) !== 'en_attente') {
            $db->transRollback();
            return ['success' => false, 'message' => 'Cette demande a déjà été traitée.'];
        }

        $annee = (int) date('Y', strtotime((string) ($conge['date_debut'] ?? 'now')));
        $typeCongeId = (int) $conge['type_conge_id'];
        $employeId = (int) $conge['employe_id'];
        $nbJours = (float) $conge['nb_jours'];

        $soldeModel = new SoldeModel();
        $solde = $soldeModel->findByEmployeTypeAnnee($employeId, $typeCongeId, $annee);

        if (! $solde) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Aucun solde trouvé pour cette demande.'];
        }

        $joursAttribues = (float) $solde['jours_attribues'];
        $joursPris = (float) $solde['jours_pris'];

        if ($joursPris + $nbJours > $joursAttribues) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Solde insuffisant pour approuver cette demande.'];
        }

        $soldeModel->updateJoursPris($solde['id'], $joursPris + $nbJours);

        $this->update($id, [
            'statut' => 'approuvee',
            'traite_par' => $traitePar,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            return ['success' => false, 'message' => 'La validation a échoué.'];
        }

        $db->transCommit();
        return ['success' => true, 'message' => 'Demande approuvée avec succès.'];
    }

    public function refuser(int $id, int $traitePar, ?string $commentaireRh = null)
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        $conge = $this->findById($id);

        if (! $conge) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Demande introuvable.'];
        }

        if (($conge['statut'] ?? null) !== 'en_attente') {
            $db->transRollback();
            return ['success' => false, 'message' => 'Cette demande a déjà été traitée.'];
        }

        $this->update($id, [
            'statut' => 'refusee',
            'commentaire_rh' => $commentaireRh !== '' ? $commentaireRh : null,
            'traite_par' => $traitePar,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        if (! $db->transStatus()) {
            $db->transRollback();
            return ['success' => false, 'message' => 'Le refus a échoué.'];
        }

        $db->transCommit();
        return ['success' => true, 'message' => 'Demande refusée.'];
    }
}
