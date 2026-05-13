<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechMada RH - Connexion</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
</head>
<body>
    <main class="login-page">
        <section class="login-shell">
            <aside class="login-brand">
                <div>
                    <div class="brand-badge">
                        <i class="bi bi-building-check"></i>
                    </div>
                    <p class="brand-kicker">TechMada RH</p>
                    <h1>Gestion des congés</h1>
                    <p class="brand-copy">
                        Accédez à votre espace RH pour consulter vos soldes, déposer une demande et suivre les validations en temps réel.
                    </p>

                    <div class="brand-points">
                        <div class="brand-point">
                            <i class="bi bi-shield-check"></i>
                            <span>Connexion sécurisée</span>
                        </div>
                        <div class="brand-point">
                            <i class="bi bi-clock-history"></i>
                            <span>Suivi instantané des demandes</span>
                        </div>
                        <div class="brand-point">
                            <i class="bi bi-graph-up-arrow"></i>
                            <span>Vision claire des soldes</span>
                        </div>
                    </div>
                </div>

                <div class="demo-box">
                    <p>Comptes de démonstration</p>
                    <ul>
                        <li><strong>Admin</strong> admin@techmada.mg / admin123</li>
                        <li><strong>RH</strong> rh@techmada.mg / rh123</li>
                        <li><strong>Employé</strong> employe@techmada.mg / emp123</li>
                    </ul>
                </div>
            </aside>

            <section class="login-card">
                <div class="card-head">
                    <span class="eyebrow">Espace sécurisé</span>
                    <h2>Connexion</h2>
                    <p>Entrez votre adresse email et votre mot de passe pour continuer.</p>
                </div>

                <?php if (session()->getFlashdata('error')) : ?>
                    <div class="flash flash-error" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span><?= esc(session()->getFlashdata('error')) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')) : ?>
                    <div class="flash flash-success" role="status">
                        <i class="bi bi-check-circle-fill"></i>
                        <span><?= esc(session()->getFlashdata('success')) ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('login') ?>" class="login-form">
                    <?= csrf_field() ?>

                    <label class="field">
                        <span class="field-label">Adresse email</span>
                        <div class="input-wrap">
                            <i class="bi bi-envelope"></i>
                            <input
                                type="email"
                                name="email"
                                value="<?= esc(old('email') ?? '') ?>"
                                placeholder="vous@techmada.mg"
                                autocomplete="email"
                                required
                            >
                        </div>
                    </label>

                    <label class="field">
                        <span class="field-label">Mot de passe</span>
                        <div class="input-wrap">
                            <i class="bi bi-lock"></i>
                            <input
                                type="password"
                                name="password"
                                placeholder="Votre mot de passe"
                                autocomplete="current-password"
                                required
                            >
                        </div>
                    </label>

                    <button type="submit" class="btn-primary">
                        Se connecter
                        <i class="bi bi-arrow-right-short"></i>
                    </button>
                </form>

                <div class="card-foot">
                    <a href="#">Mot de passe oublié ?</a>
                    <span>•</span>
                    <a href="#">Besoin d'un accès ?</a>
                </div>
            </section>
        </section>
    </main>
</body>
</html>