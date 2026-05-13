<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= esc($title ?? 'TechMada RH') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
  <?php $session = session(); $role = $session->get('role') ?? 'employe'; ?>

  <div class="app-wrap">
    <aside class="sidebar">
      <div class="sidebar-brand">
        <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
        <div class="sidebar-brand-name">TechMada RH<span>Espace <?= esc($role) ?></span></div>
      </div>

      <ul class="sidebar-nav">
        <?php if ($role === 'admin'): ?>
          <li><a href="<?= site_url('admin') ?>"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
          <li><a href="<?= site_url('liste-rh') ?>"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
          <li><a href="<?= site_url('admin/employes') ?>"><i class="bi bi-people"></i> Employés</a></li>
          <li><a href="<?= site_url('admin/departements') ?>"><i class="bi bi-building"></i> Départements</a></li>
          <li><a href="<?= site_url('admin/types') ?>"><i class="bi bi-tags"></i> Types de congé</a></li>
        <?php elseif ($role === 'rh'): ?>
          <li><a href="<?= site_url('rh') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
          <li><a href="<?= site_url('rh/demandes') ?>"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
          <li><a href="<?= site_url('soldes') ?>"><i class="bi bi-people"></i> Soldes employés</a></li>
          <li><a href="<?= site_url('historique') ?>"><i class="bi bi-archive"></i> Historique</a></li>
        <?php else: ?>
          <li><a href="<?= site_url('/') ?>"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
          <li><a href="<?= site_url('form-conge') ?>"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
          <li><a href="<?= site_url('mes-conges') ?>"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
          <li><a href="<?= site_url('profil') ?>"><i class="bi bi-person"></i> Mon profil</a></li>
        <?php endif; ?>
      </ul>

      <div class="sidebar-user">
        <div class="s-user-row">
          <div class="avatar av-green"><?= html_entity_decode(substr(($session->get('email') ?? 'U'),0,2)) ?></div>
          <div>
            <div class="user-name"><?= esc($session->get('email') ?? 'Invité') ?></div>
            <div class="user-role"><?= esc(ucfirst($role)) ?></div>
          </div>
          <a href="<?= site_url('logout') ?>" title="Déconnexion" class="logout-link"><i class="bi bi-box-arrow-right"></i></a>
        </div>
      </div>
    </aside>

    <div class="main">
      <header class="topbar">
        <div class="topbar-title"><?= esc($title ?? 'Tableau de bord') ?></div>
        <div class="topbar-actions">
          <div class="topbar-user">Bonjour, <strong><?= esc($session->get('email') ?? 'Invité') ?></strong></div>
        </div>
      </header>

      <main class="content">
        <?php if ($flashSuccess = session()->getFlashdata('success')): ?>
          <div class="app-alert app-alert-success"><?= esc($flashSuccess) ?></div>
        <?php endif; ?>

        <?php if ($flashError = session()->getFlashdata('error')): ?>
          <div class="app-alert app-alert-error"><?= esc($flashError) ?></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
      </main>

      <footer class="footer-app">© <?= date('Y') ?> TechMada RH</footer>
    </div>
  </div>

</body>
</html>
