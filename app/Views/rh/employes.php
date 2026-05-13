<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section class="rh-page">
  <div class="page-head">
    <div>
      <p class="eyebrow">Espace RH</p>
      <h1>Soldes employés</h1>
      <p class="page-subtitle">Tableau récapitulatif des soldes de chaque employé pour l’année <?= esc($annee ?? date('Y')) ?>.</p>
    </div>
    <div class="page-kpi">
      <span class="kpi-value"><?= esc($totalEmployes ?? 0) ?></span>
      <span class="kpi-label">employé(s) suivis</span>
    </div>
  </div>

  <div class="table-card filters-card">
    <form method="get" action="<?= site_url('rh/employes') ?>" class="rh-filters rh-filters-single">
      <div class="filter-field">
        <label for="annee">Année</label>
        <select id="annee" name="annee">
          <?php for ($year = (int) date('Y'); $year >= (int) date('Y') - 3; $year--): ?>
            <option value="<?= esc($year) ?>" <?= (int) ($annee ?? date('Y')) === $year ? 'selected' : '' ?>>
              <?= esc($year) ?>
            </option>
          <?php endfor; ?>
        </select>
      </div>

      <div class="filter-actions">
        <button type="submit" class="action-btn action-btn-approve"><i class="bi bi-funnel"></i> Afficher</button>
        <a href="<?= site_url('rh/employes') ?>" class="filter-reset">Année courante</a>
      </div>
    </form>
  </div>

  <?php if (! empty($employes)): ?>
    <div class="table-card">
      <div class="table-meta">
        <span class="meta-chip meta-chip-soft"><i class="bi bi-calendar3"></i> Année: <?= esc($annee ?? date('Y')) ?></span>
        <span class="meta-chip"><i class="bi bi-grid-3x3-gap"></i> Colonnes dynamiques par type de congé</span>
      </div>

      <div class="responsive-table">
        <table class="rh-table balances-table">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Département</th>
              <?php foreach (($typesConge ?? []) as $typeConge): ?>
                <th><?= esc($typeConge['libelle']) ?></th>
              <?php endforeach; ?>
              <th>Total attribués</th>
              <th>Total pris</th>
              <th>Disponible</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($employes as $employe): ?>
              <?php
                $totalAttribues = 0.0;
                $totalPris = 0.0;
                $nameParts = preg_split('/\s+/', trim((string) $employe['nom_complet'])) ?: [];
                $firstName = $nameParts[0] ?? 'U';
                $lastName = $nameParts[count($nameParts) - 1] ?? 'U';
                $initials = mb_strtoupper(mb_substr($firstName, 0, 1) . mb_substr($lastName, 0, 1));
              ?>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">
                      <?= esc($initials) ?>
                    </div>
                    <div>
                      <strong><?= esc($employe['nom_complet']) ?></strong>
                      <span><?= esc($employe['email']) ?> · <?= esc(ucfirst($employe['role'])) ?></span>
                    </div>
                  </div>
                </td>
                <td><?= esc($employe['departement_nom']) ?></td>

                <?php foreach (($typesConge ?? []) as $typeConge): ?>
                  <?php
                    $typeId = (int) $typeConge['id'];
                    $solde = $employe['soldes'][$typeId] ?? ['jours_attribues' => 0, 'jours_pris' => 0];
                    $joursAttribues = (float) $solde['jours_attribues'];
                    $joursPris = (float) $solde['jours_pris'];
                    $joursRestants = $joursAttribues - $joursPris;
                    $totalAttribues += $joursAttribues;
                    $totalPris += $joursPris;
                  ?>
                  <td>
                    <div class="balance-cell">
                      <strong><?= esc(number_format($joursRestants, 1, ',', ' ')) ?> j</strong>
                      <span><?= esc(number_format($joursPris, 1, ',', ' ')) ?> / <?= esc(number_format($joursAttribues, 1, ',', ' ')) ?></span>
                    </div>
                  </td>
                <?php endforeach; ?>

                <td>
                  <strong><?= esc(number_format($totalAttribues, 1, ',', ' ')) ?> j</strong>
                </td>
                <td>
                  <strong><?= esc(number_format($totalPris, 1, ',', ' ')) ?> j</strong>
                </td>
                <td>
                  <?php $totalDisponible = $totalAttribues - $totalPris; ?>
                  <span class="status-badge <?= $totalDisponible >= 0 ? 'status-approved' : 'status-refused' ?>">
                    <?= esc(number_format($totalDisponible, 1, ',', ' ')) ?> j
                  </span>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-people"></i></div>
      <h2>Aucun employé trouvé</h2>
      <p>Aucun solde n’est disponible pour l’année sélectionnée.</p>
    </div>
  <?php endif; ?>
</section>

<?= $this->endSection() ?>