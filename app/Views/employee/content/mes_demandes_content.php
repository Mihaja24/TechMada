<div class="page-header">
    <h1>Mes demandes de congé</h1>
    <p class="page-subtitle">Consultez l'historique de vos demandes de congé</p>
</div>

<?php if (empty($conges)): ?>
    <div class="empty-state">
        <p>Aucune demande de congé trouvée.</p>
        <a href="/employee/demande" class="btn-link">Créer une nouvelle demande</a>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="demandes-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Dates</th>
                    <th>Jours</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($conges as $conge): ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($conge['type_conge_libelle'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <?php if (!empty($conge['motif'])): ?>
                                <br><small class="text-muted"><?= htmlspecialchars(substr($conge['motif'], 0, 50), ENT_QUOTES, 'UTF-8') ?><?= strlen($conge['motif']) > 50 ? '...' : '' ?></small>
                            <?php endif ?>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($conge['date_debut'])) ?>
                            <br><small class="text-muted">au <?= date('d/m/Y', strtotime($conge['date_fin'])) ?></small>
                        </td>
                        <td>
                            <span class="jours-badge"><?= $conge['nb_jours'] ?> j</span>
                        </td>
                        <td>
                            <?php
                            $badgeClass = '';
                            $badgeText = '';
                            switch ($conge['statut']) {
                                case 'en_attente':
                                    $badgeClass = 'badge-warning';
                                    $badgeText = 'En attente';
                                    break;
                                case 'approuvee':
                                    $badgeClass = 'badge-success';
                                    $badgeText = 'Approuvée';
                                    break;
                                case 'refusee':
                                    $badgeClass = 'badge-danger';
                                    $badgeText = 'Refusée';
                                    break;
                                case 'annulee':
                                    $badgeClass = 'badge-muted';
                                    $badgeText = 'Annulée';
                                    break;
                                default:
                                    $badgeClass = 'badge-info';
                                    $badgeText = ucfirst(str_replace('_', ' ', $conge['statut']));
                            }
                            ?>
                            <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
                        </td>
                        <td>
                            <?php if ($conge['statut'] === 'en_attente'): ?>
                                <form method="post" action="/employee/annuler/<?= $conge['id'] ?>" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette demande ?');">
                                    <button type="submit" class="btn-cancel">Annuler</button>
                                </form>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['total'] > 1): ?>
        <div class="pagination">
            <?php if ($pagination['current'] > 1): ?>
                <a href="/employee/mes_demandes?page=<?= $pagination['current'] - 1 ?>" class="pagination-link">← Précédent</a>
            <?php endif ?>

            <?php for ($i = 1; $i <= $pagination['total']; $i++): ?>
                <?php if ($i == $pagination['current']): ?>
                    <span class="pagination-current"><?= $i ?></span>
                <?php else: ?>
                    <a href="/employee/mes_demandes?page=<?= $i ?>" class="pagination-link"><?= $i ?></a>
                <?php endif ?>
            <?php endfor ?>

            <?php if ($pagination['current'] < $pagination['total']): ?>
                <a href="/employee/mes_demandes?page=<?= $pagination['current'] + 1 ?>" class="pagination-link">Suivant →</a>
            <?php endif ?>
        </div>
    <?php endif ?>

    <p class="pagination-info">
        Affichage <?= ($pagination['current'] - 1) * $pagination['per_page'] + 1 ?>-<?= min($pagination['current'] * $pagination['per_page'], $pagination['total_items']) ?> sur <?= $pagination['total_items'] ?> demande(s)
    </p>
<?php endif ?>
