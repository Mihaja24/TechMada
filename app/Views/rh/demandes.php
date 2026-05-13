<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section class="rh-page">
  <div class="page-head">
    <div>
      <p class="eyebrow">Espace RH</p>
      <h1>Demandes</h1>
      <p class="page-subtitle">Vue des demandes RH, triées par date de début et filtrables par département ou statut.</p>
    </div>
    <div class="page-kpi">
      <span class="kpi-value"><?= esc($totalDemandes ?? 0) ?></span>
      <span class="kpi-label">demande(s) à traiter</span>
    </div>
  </div>

  <div class="table-card filters-card">
    <form method="get" action="<?= site_url('rh/demandes') ?>" class="rh-filters">
      <div class="filter-field">
        <label for="departement_id">Département</label>
        <select id="departement_id" name="departement_id">
          <option value="">Tous les départements</option>
          <?php foreach (($departements ?? []) as $departement): ?>
            <option value="<?= esc($departement['id']) ?>" <?= (string) ($selectedDepartementId ?? '') === (string) $departement['id'] ? 'selected' : '' ?>>
              <?= esc($departement['nom']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-field">
        <label for="statut">Statut</label>
        <select id="statut" name="statut">
          <?php foreach (($statusOptions ?? []) as $value => $label): ?>
            <option value="<?= esc($value) ?>" <?= (string) ($selectedStatut ?? '') === (string) $value ? 'selected' : '' ?>>
              <?= esc($label) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="filter-actions">
        <button type="submit" class="action-btn action-btn-approve"><i class="bi bi-funnel"></i> Filtrer</button>
        <a href="<?= site_url('rh/demandes') ?>" class="filter-reset">Réinitialiser</a>
      </div>
    </form>
  </div>

  <?php if (! empty($demandes)): ?>
    <div class="table-card">
      <div class="table-meta">
        <span class="meta-chip meta-chip-soft"><i class="bi bi-funnel"></i> Statut: <?= esc($selectedStatutLabel ?? ucfirst((string) $selectedStatut)) ?></span>
        <span class="meta-chip"><i class="bi bi-building"></i> Département: <?= esc($selectedDepartementNom ?? 'Tous') ?></span>
        <span class="meta-chip"><i class="bi bi-sort-down"></i> Tri: date de début croissante</span>
      </div>

      <div class="responsive-table">
        <table class="rh-table">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Département</th>
              <th>Type</th>
              <th>Période</th>
              <th>Jours</th>
              <th>Motif</th>
              <th>Demandée le</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($demandes as $demande): ?>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">
                      <?= esc(mb_strtoupper(mb_substr((string)($demande['employe_prenom'] ?? 'U'), 0, 1) . mb_substr((string)($demande['employe_nom'] ?? 'R'), 0, 1))) ?>
                    </div>
                    <div>
                      <strong><?= esc(trim(($demande['employe_prenom'] ?? '') . ' ' . ($demande['employe_nom'] ?? ''))) ?></strong>
                      <span><?= esc($demande['employe_email'] ?? '') ?></span>
                    </div>
                  </div>
                </td>
                <td><?= esc($demande['departement_nom'] ?? 'Non défini') ?></td>
                <td><?= esc($demande['type_conge_libelle'] ?? 'Congé') ?></td>
                <td>
                  <div class="period-cell">
                    <strong><?= esc(date('d/m/Y', strtotime((string) $demande['date_debut']))) ?></strong>
                    <span>au <?= esc(date('d/m/Y', strtotime((string) $demande['date_fin']))) ?></span>
                  </div>
                </td>
                <td><?= esc(number_format((float) $demande['nb_jours'], 1, ',', ' ')) ?></td>
                <td class="motif-cell">
                  <?= esc($demande['motif'] ?: 'Aucun motif renseigné') ?>
                </td>
                <td>
                  <?= esc(! empty($demande['created_at']) ? date('d/m/Y H:i', strtotime((string) $demande['created_at'])) : '—') ?>
                </td>
                <td>
                  <?php if (($demande['statut'] ?? '') === 'approuvee'): ?>
                    <span class="status-badge status-approved">Approuvée</span>
                  <?php elseif (($demande['statut'] ?? '') === 'refusee'): ?>
                    <span class="status-badge status-refused">Refusée</span>
                  <?php else: ?>
                    <span class="status-badge status-pending">En attente</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if (($demande['statut'] ?? '') === 'en_attente'): ?>
                    <form method="post" action="<?= site_url('rh/approuver/' . $demande['id']) ?>" class="inline-action-form">
                      <?= csrf_field() ?>
                      <button type="submit" class="action-btn action-btn-approve" onclick="return confirm('Approuver cette demande ?')">
                        <i class="bi bi-check2-circle"></i>
                        Approuver
                      </button>
                    </form>
                    <details class="refusal-panel">
                      <summary class="action-btn action-btn-refuse">
                        <i class="bi bi-x-circle"></i>
                        Refuser
                      </summary>
                      <form method="post" action="<?= site_url('rh/refuser/' . $demande['id']) ?>" class="refusal-form">
                        <?= csrf_field() ?>
                        <label for="commentaire_rh_<?= esc($demande['id']) ?>">Commentaire RH</label>
                        <textarea id="commentaire_rh_<?= esc($demande['id']) ?>" name="commentaire_rh" rows="3" placeholder="Commentaire optionnel"></textarea>
                        <button type="submit" class="action-btn action-btn-refuse-submit" onclick="return confirm('Refuser cette demande ?')">
                          <i class="bi bi-send-x"></i>
                          Confirmer le refus
                        </button>
                      </form>
                    </details>
                  <?php else: ?>
                    <span class="action-muted">Aucune action</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  <?php else: ?>
    <div class="empty-state">
      <div class="empty-icon"><i class="bi bi-inbox"></i></div>
      <h2>Aucune demande en attente</h2>
      <p>Les nouvelles demandes apparaîtront ici dès qu’un employé en soumettra une.</p>
    </div>
  <?php endif; ?>
</section>

<?= $this->endSection() ?>