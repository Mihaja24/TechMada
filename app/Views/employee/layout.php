<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . ' - ' : '' ?>TechMada RH</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/employee-layout.css">
    <?php if (isset($css)): ?>
        <?php foreach ($css as $css_file): ?>
            <link rel="stylesheet" href="<?= htmlspecialchars($css_file, ENT_QUOTES, 'UTF-8') ?>">
        <?php endforeach ?>
    <?php endif ?>
</head>
<body>
<nav class="navbar">
    <div class="navbar-container">
        <div class="navbar-brand">
            <h1>TechMada RH</h1>
        </div>
        <div class="navbar-menu">
            <a href="/employee/demande" class="<?= isset($active) && $active === 'demande' ? 'active' : '' ?>">Nouvelle demande</a>
            <a href="/employee/mes_demandes" class="<?= isset($active) && $active === 'mes_demandes' ? 'active' : '' ?>">Mes demandes</a>
            <a href="/employee/solde" class="<?= isset($active) && $active === 'solde' ? 'active' : '' ?>">Mon solde</a>
            <a href="/employee/profil" class="<?= isset($active) && $active === 'profil' ? 'active' : '' ?>">Mon profil</a>
        </div>
    </div>
</nav>

<main class="main-content">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success"><?= htmlspecialchars(session()->getFlashdata('success'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error"><?= htmlspecialchars(session()->getFlashdata('error'), ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif ?>

    <?= $content ?>
</main>

<footer class="footer">
    <p>&copy; <?= date('Y') ?> TechMada RH - Système de gestion des congés</p>
</footer>
</body>
</html>
