<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Config\Database;

class CongeController extends BaseController
{
    public function demande()
    {
        $db = Database::connect();
        $session = service('session');
        $employeId = $session->get('employe_id') ?? 1;

        $typesConge = $db->table('types_conge')
            ->orderBy('libelle', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'types_conge' => $typesConge,
            'errors'      => [],
            'old'         => [
                'type_conge_id' => '',
                'date_debut'    => '',
                'date_fin'      => '',
                'motif'         => '',
            ],
            'success'     => null,
        ];

        if ($this->request->getMethod() === 'post') {
            $typeCongeId = $this->request->getPost('type_conge_id');
            $dateDebut = $this->request->getPost('date_debut');
            $dateFin = $this->request->getPost('date_fin');
            $motif = $this->request->getPost('motif');

            $data['old'] = [
                'type_conge_id' => $typeCongeId,
                'date_debut'    => $dateDebut,
                'date_fin'      => $dateFin,
                'motif'         => $motif,
            ];

            if (empty($typeCongeId)) {
                $data['errors']['type_conge_id'] = 'Veuillez choisir le type de congé.';
            }

            if (empty($dateDebut)) {
                $data['errors']['date_debut'] = 'Veuillez renseigner la date de début.';
            }

            if (empty($dateFin)) {
                $data['errors']['date_fin'] = 'Veuillez renseigner la date de fin.';
            }

            if (empty($motif)) {
                $data['errors']['motif'] = 'Veuillez préciser le motif du congé.';
            }

            $debut = null;
            $fin = null;
            if (empty($data['errors']) && $dateDebut && $dateFin) {
                try {
                    $debut = new \DateTime($dateDebut);
                    $fin = new \DateTime($dateFin);
                } catch (\Exception $e) {
                    $data['errors']['date'] = 'Format de date invalide.';
                }

                if ($debut && $fin && $debut > $fin) {
                    $data['errors']['date'] = 'La date de début doit être antérieure ou égale à la date de fin.';
                }
            }

            if (empty($data['errors']) && $debut && $fin) {
                $overlapCount = $db->table('conges')
                    ->where('employe_id', $employeId)
                    ->groupStart()
                        ->where('date_debut <=', $fin->format('Y-m-d'))
                        ->where('date_fin >=', $debut->format('Y-m-d'))
                    ->groupEnd()
                    ->countAllResults();

                if ($overlapCount > 0) {
                    $data['errors']['chevauchement'] = 'Vous avez déjà une demande de congé qui chevauche cette période.';
                }
            }

            if (empty($data['errors']) && $debut && $fin) {
                $interval = date_diff($debut, $fin);
                $nbJours = $interval->days + 1;
                if ($nbJours < 1) {
                    $nbJours = 0;
                }

                $insertData = [
                    'employe_id'     => $employeId,
                    'type_conge_id'  => $typeCongeId,
                    'date_debut'     => $debut->format('Y-m-d'),
                    'date_fin'       => $fin->format('Y-m-d'),
                    'nb_jours'       => $nbJours,
                    'motif'          => $motif,
                    'statut'         => 'en_attente',
                    'commentaire_rh' => null,
                    'traite_par'     => null,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ];

                $db->table('conges')->insert($insertData);

                $data['success'] = 'Votre demande de congé a bien été enregistrée (' . $nbJours . ' jour(s)).';
                $data['old'] = [
                    'type_conge_id' => '',
                    'date_debut'    => '',
                    'date_fin'      => '',
                    'motif'         => '',
                ];
            }
        }

        return view('employee/demande', $data);
    }

    public function mes_demandes()
    {
        $db = Database::connect();
        $session = service('session');
        $employeId = $session->get('employe_id') ?? 1;

        // Pagination
        $perPage = 10;
        $page = $this->request->getGet('page') ?? 1;
        $offset = ($page - 1) * $perPage;

        // Get total count
        $total = $db->table('conges')
            ->where('employe_id', $employeId)
            ->countAllResults();

        // Get demands with pagination
        $conges = $db->table('conges')
            ->select('conges.*, types_conge.libelle as type_conge_libelle')
            ->join('types_conge', 'types_conge.id = conges.type_conge_id')
            ->where('conges.employe_id', $employeId)
            ->orderBy('conges.created_at', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $data = [
            'conges' => $conges,
            'pagination' => [
                'current' => $page,
                'total' => ceil($total / $perPage),
                'per_page' => $perPage,
                'total_items' => $total,
            ],
        ];

        return view('employee/mes_demandes', $data);
    }

    public function annuler($id)
    {
        $db = Database::connect();
        $session = service('session');
        $employeId = $session->get('employe_id') ?? 1;

        if ($this->request->getMethod() === 'post') {
            // Verify the demand belongs to the employee and is in en_attente status
            $conge = $db->table('conges')
                ->where('id', $id)
                ->where('employe_id', $employeId)
                ->where('statut', 'en_attente')
                ->get()
                ->getRowArray();

            if ($conge) {
                $db->table('conges')
                    ->where('id', $id)
                    ->update([
                        'statut' => 'annulee',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $session->setFlashdata('success', 'Votre demande de congé a été annulée.');
            } else {
                $session->setFlashdata('error', 'Impossible d\'annuler cette demande. Elle n\'existe pas, ne vous appartient pas, ou n\'est plus en attente.');
            }
        }

        return redirect()->to('/employee/mes_demandes');
    }
}
