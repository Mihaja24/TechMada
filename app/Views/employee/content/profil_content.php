<div class="page-header">
    <h1>Mon profil</h1>
    <p class="page-subtitle">Gérez vos informations personnelles</p>
</div>

<?php if (!empty($success)): ?>
    <div class="flash flash-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif ?>

<div class="profil-grid">
    <div class="profil-card">
        <div class="profil-header">
            <h2>Informations personnelles</h2>
        </div>
        <div class="profil-body">
            <div class="info-row">
                <span class="info-label">Email</span>
                <span class="info-value"><?= htmlspecialchars($employe['email'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Rôle</span>
                <span class="info-value"><?= htmlspecialchars(ucfirst($employe['role']), ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <div class="info-row">
                <span class="info-label">Date d'embauche</span>
                <span class="info-value"><?= date('d/m/Y', strtotime($employe['date_embauche'])) ?></span>
            </div>
        </div>
    </div>

    <div class="profil-card">
        <div class="profil-header">
            <h2>Modifier mes informations</h2>
        </div>
        <div class="profil-body">
            <form method="post" action="">
                <div class="form-grid-2">
                    <div class="f-group">
                        <label class="f-label" for="nom">Nom</label>
                        <input class="f-input" type="text" id="nom" name="nom" value="<?= htmlspecialchars($employe['nom'], ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['nom'])): ?>
                            <span class="f-error"><?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif ?>
                    </div>
                    <div class="f-group">
                        <label class="f-label" for="prenom">Prénom</label>
                        <input class="f-input" type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($employe['prenom'], ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php if (isset($errors['prenom'])): ?>
                            <span class="f-error"><?= htmlspecialchars($errors['prenom'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif ?>
                    </div>
                </div>

                <div class="form-divider">
                    <span>Changer le mot de passe (optionnel)</span>
                </div>

                <div class="f-group">
                    <label class="f-label" for="current_password">Mot de passe actuel</label>
                    <input class="f-input" type="password" id="current_password" name="current_password">
                    <?php if (isset($errors['current_password'])): ?>
                        <span class="f-error"><?= htmlspecialchars($errors['current_password'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif ?>
                </div>

                <div class="form-grid-2">
                    <div class="f-group">
                        <label class="f-label" for="new_password">Nouveau mot de passe</label>
                        <input class="f-input" type="password" id="new_password" name="new_password" minlength="8">
                        <?php if (isset($errors['new_password'])): ?>
                            <span class="f-error"><?= htmlspecialchars($errors['new_password'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif ?>
                    </div>
                    <div class="f-group">
                        <label class="f-label" for="confirm_password">Confirmer le mot de passe</label>
                        <input class="f-input" type="password" id="confirm_password" name="confirm_password" minlength="8">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <span class="f-error"><?= htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif ?>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
