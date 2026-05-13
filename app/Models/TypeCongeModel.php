<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeCongeModel extends Model
{
    protected $table = 'types_conge';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'libelle',
        'jours_annuels',
        'deductible',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getAllOrdered()
    {
        return $this->select('id, libelle, jours_annuels, deductible')
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findById(int $id)
    {
        return $this->where('id', $id)->first();
    }
}
