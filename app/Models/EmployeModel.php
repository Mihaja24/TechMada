<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeModel extends Model
{
    protected $table = 'employes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nom',
        'prenom',
        'email',
        'password',
        'role',
        'departement_id',
        'date_embauche',
        'actif',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAllWithDepartement()
    {
        return $this->select([
            'e.id',
            'e.nom',
            'e.prenom',
            'e.email',
            'e.role',
            'e.actif',
            'e.date_embauche',
            'd.nom AS departement_nom',
            'd.id AS departement_id',
        ])
            ->from('employes e')
            ->join('departements d', 'd.id = e.departement_id', 'left')
            ->orderBy('e.actif', 'DESC')
            ->orderBy('e.prenom', 'ASC')
            ->orderBy('e.nom', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function findByEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function findByEmailForLogin(string $email)
    {
        return $this->where('email', $email)
            ->where('actif', 1)
            ->first();
    }

    public function findById(int $id)
    {
        return $this->where('id', $id)->first();
    }

    public function deactivate(int $id)
    {
        return $this->update($id, [
            'actif' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function getActiveInactiveCounts()
    {
        $employes = $this->findAll();
        $activeCount = 0;
        $inactiveCount = 0;

        foreach ($employes as $employe) {
            if ((int) $employe['actif'] === 1) {
                $activeCount++;
            } else {
                $inactiveCount++;
            }
        }

        return [
            'active' => $activeCount,
            'inactive' => $inactiveCount,
            'total' => count($employes),
        ];
    }

    public function getEmployesWithSoldes(int $annee)
    {
        $db = \Config\Database::connect();
        
        return $db->table('employes e')
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
    }
}
