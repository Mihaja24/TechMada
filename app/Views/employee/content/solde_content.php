<div class="page-header">
    <h1>Mon solde de congés</h1>
    <p class="page-subtitle">Année <?= htmlspecialchars($current_year, ENT_QUOTES, 'UTF-8') ?></p>
</div>

<?php if (empty($soldes)): ?>
    <div class="empty-state">
        <p>Aucun solde de congé disponible pour cette année.</p>
    </div>
<?php else: ?>
    <div class="soldes-grid">
        <?php foreach ($soldes as $solde): ?>
            <div class="solde-card">
                <div class="solde-header">
                    <h3><?= htmlspecialchars($solde['type_conge_libelle'], ENT_QUOTES, 'UTF-8') ?></h3>
                </div>
                <div class="solde-body">
                    <div class="solde-row">
                        <span class="solde-label">Jours attribués</span>
                        <span class="solde-value solde-attribues"><?= $solde['jours_attribues'] ?></span>
                    </div>
                    <div class="solde-row">
                        <span class="solde-label">Jours pris</span>
                        <span class="solde-value solde-pris"><?= $solde['jours_pris'] ?></span>
                    </div>
                    <div class="solde-row solde-row-total">
                        <span class="solde-label">Restant</span>
                        <span class="solde-value solde-restant <?= $solde['jours_restants'] > 0 ? 'positive' : ($solde['jours_restants'] < 0 ? 'negative' : 'zero') ?>">
                            <?= $solde['jours_restants'] ?>
                        </span>
                    </div>
                </div>
            </div>
        <?php endforeach ?>
    </div>
<?php endif ?>
