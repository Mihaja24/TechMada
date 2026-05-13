<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<section class="admin-page">
  <div class="page-head">
    <div>
      <p class="eyebrow">Administration</p>
      <h1>Employés</h1>
      <p class="page-subtitle">Créer, modifier et désactiver les comptes employés, avec nom, email, rôle et département.</p>
    </div>
    <div class="page-kpi">
      <span class="kpi-value"><?= esc($totalCount ?? 0) ?></span>
      <span class="kpi-label">employé(s)</span>
    </div>
  </div>

  <div class="admin-stats">
    <div class="stat-card stat-card-green">
      <span class="stat-value"><?= esc($activeCount ?? 0) ?></span>
      <span class="stat-label">Comptes actifs</span>
    </div>
    <div class="stat-card stat-card-red">
      <span class="stat-value"><?= esc($inactiveCount ?? 0) ?></span>
      <span class="stat-label">Comptes désactivés</span>
    </div>
  </div>

  <div class="admin-grid">
    <div class="table-card admin-form-card">
      <div class="card-section-head">
        <div>
          <span class="eyebrow"><?= $editMode ? 'Modifier' : 'Créer' ?></span>
          <h2><?= $editMode ? 'Éditer un employé' : 'Nouvel employé' ?></h2>
        </div>
        <?php if ($editMode): ?>
          <a href="<?= site_url('admin/employes') ?>" class="filter-reset">Annuler</a>
        <?php endif; ?>
      </div>

      <form method="post" action="<?= site_url('admin/employes/save') ?>" class="admin-form">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= esc($selectedEmploye['id'] ?? '') ?>">

        <div class="form-grid">
          <label class="form-field">
            <span>Prénom</span>
            <input type="text" name="prenom" value="<?= esc($selectedEmploye['prenom'] ?? '') ?>" placeholder="Jean" required>
          </label>

          <label class="form-field">
            <span>Nom</span>
            <input type="text" name="nom" value="<?= esc($selectedEmploye['nom'] ?? '') ?>" placeholder="Dupont" required>
          </label>

          <label class="form-field form-field-wide">
            <span>Email</span>
            <input type="email" name="email" value="<?= esc($selectedEmploye['email'] ?? '') ?>" placeholder="prenom.nom@techmada.mg" required>
          </label>

          <label class="form-field">
            <span>Rôle</span>
            <select name="role" required>
              <?php foreach (($roleOptions ?? []) as $value => $label): ?>
                <option value="<?= esc($value) ?>" <?= (string) ($selectedEmploye['role'] ?? 'employe') === (string) $value ? 'selected' : '' ?>>
                  <?= esc($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="form-field">
            <span>Département</span>
            <select name="departement_id" required>
              <option value="">Sélectionner</option>
              <?php foreach (($departements ?? []) as $departement): ?>
                <option value="<?= esc($departement['id']) ?>" <?= (string) ($selectedEmploye['departement_id'] ?? '') === (string) $departement['id'] ? 'selected' : '' ?>>
                  <?= esc($departement['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="form-field">
            <span>Date d’embauche</span>
            <input type="date" name="date_embauche" value="<?= esc($selectedEmploye['date_embauche'] ?? date('Y-m-d')) ?>" required>
          </label>

          <label class="form-field form-field-wide">
            <span><?= $editMode ? 'Nouveau mot de passe (optionnel)' : 'Mot de passe initial' ?></span>
            <input type="password" name="password" value="" placeholder="<?= $editMode ? 'Laisser vide pour conserver' : 'Au moins 8 caractères' ?>" <?= $editMode ? '' : 'required' ?>>
          </label>
        </div>

        <div class="form-actions">
          <button type="submit" class="action-btn action-btn-approve">
            <i class="bi bi-save2"></i>
            <?= $editMode ? 'Mettre à jour' : 'Créer l’employé' ?>
          </button>
        </div>
      </form>
    </div>

    <div class="table-card admin-table-card">
      <div class="card-section-head">
        <div>
          <span class="eyebrow">Liste</span>
          <h2>Comptes existants</h2>
        </div>
      </div>

      <div class="responsive-table">
        <table class="rh-table admin-table">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Email</th>
              <th>Rôle</th>
              <th>Département</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach (($employes ?? []) as $employe): ?>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">
                      <?= esc(mb_strtoupper(mb_substr((string) $employe['prenom'], 0, 1) . mb_substr((string) $employe['nom'], 0, 1))) ?>
                    </div>
                    <div>
                      <strong><?= esc(trim($employe['prenom'] . ' ' . $employe['nom'])) ?></strong>
                      <span><?= esc($employe['date_embauche']) ?></span>
                    </div>
                  </div>
                </td>
                <td><?= esc($employe['email']) ?></td>
                <td><span class="status-badge status-approved"><?= esc(ucfirst($employe['role'])) ?></span></td>
                <td><?= esc($employe['departement_nom'] ?? 'Non défini') ?></td>
                <td>
                  <?php if ((int) $employe['actif'] === 1): ?>
                    <span class="status-badge status-approved">Actif</span>
                  <?php else: ?>
                    <span class="status-badge status-refused">Désactivé</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="table-actions">
                    <a href="<?= site_url('admin/employes?edit=' . $employe['id']) ?>" class="table-action-link">
                      <i class="bi bi-pencil-square"></i>
                      Éditer
                    </a>

                    <?php if ((int) $employe['actif'] === 1): ?>
                      <form method="post" action="<?= site_url('admin/employes/desactiver/' . $employe['id']) ?>" class="inline-action-form" onsubmit="return confirm('Désactiver cet employé ?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="action-btn action-btn-refuse-submit">
                          <i class="bi bi-person-x"></i>
                          Désactiver
                        </button>
                      </form>
                    <?php else: ?>
                      <span class="action-muted">Compte inactif</span>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>

<?= $this->endSection() ?>