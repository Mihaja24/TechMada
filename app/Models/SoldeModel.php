<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'soldes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'employe_id',
        'type_conge_id',
        'annee',
        'jours_attribues',
        'jours_pris',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function findByEmployeTypeAnnee(int $employeId, int $typeCongeId, int $annee)
    {
        return $this->where('employe_id', $employeId)
            ->where('type_conge_id', $typeCongeId)
            ->where('annee', $annee)
            ->first();
    }

    public function updateJoursPris(int $id, float $joursPris)
    {
        return $this->update($id, [
            'jours_pris' => $joursPris,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function hasSufficientSolde(int $employeId, int $typeCongeId, int $annee, float $nbJours)
    {
        $solde = $this->findByEmployeTypeAnnee($employeId, $typeCongeId, $annee);

        if (! $solde) {
            return false;
        }

        $joursAttribues = (float) $solde['jours_attribues'];
        $joursPris = (float) $solde['jours_pris'];

        return ($joursPris + $nbJours) <= $joursAttribues;
    }
}
