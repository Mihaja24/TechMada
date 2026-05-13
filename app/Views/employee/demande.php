<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Demande de congé</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/employee-demande.css">
</head>
<body>
<section class="auth-page geo-bg">
    <div class="auth-split">

        <div class="auth-left">
            <div>
                <h1 class="auth-left-brand">TechMada RH</h1>
                <span>Demande de congé</span>
                <p class="auth-left-text">Remplissez ce formulaire pour soumettre une nouvelle demande de congé. Votre demande sera enregistrée et traitée par les responsables RH.</p>
            </div>
            <div class="auth-left-text">
                <strong>Astuce :</strong>
                Veillez à vérifier les dates et le type de congé avant de soumettre.
            </div>
            <div class="auth-left-text">
                <a href="/employee/mes_demandes" class="btn-link">→ Voir mes demandes</a>
            </div>
        </div>

        <div class="auth-right">
            <div class="form-section">
                <h2>Nouvelle demande</h2>

                <?php if (!empty($success)): ?>
                    <div class="flash flash-success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif ?>

                <?php if (!empty($errors)): ?>
                    <div class="flash flash-error">
                        <div>
                            <strong>Corrigez les erreurs suivantes :</strong>
                            <ul class="error-list">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    </div>
                <?php endif ?>

                <form method="post" action="">
                    <div class="f-group">
                        <label class="f-label" for="type_conge_id">Type de congé</label>
                        <select class="f-select" id="type_conge_id" name="type_conge_id" required>
                            <option value="">-- Choisir un type --</option>
                            <?php foreach ($types_conge as $type): ?>
                                <option value="<?= htmlspecialchars($type['id'], ENT_QUOTES, 'UTF-8') ?>" <?= $old['type_conge_id'] == $type['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($type['libelle'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="form-grid-2">
                        <div class="f-group">
                            <label class="f-label" for="date_debut">Date de début</label>
                            <input class="f-input" type="date" id="date_debut" name="date_debut" value="<?= htmlspecialchars($old['date_debut'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="f-group">
                            <label class="f-label" for="date_fin">Date de fin</label>
                            <input class="f-input" type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>

                    <div class="f-group">
                        <label class="f-label" for="motif">Motif</label>
                        <textarea class="f-textarea" id="motif" name="motif" required><?= htmlspecialchars($old['motif'], ENT_QUOTES, 'UTF-8') ?></textarea>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Envoyer la demande</button>
                        <a href="/employee/mes_demandes" class="btn-secondary">Voir mes demandes</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</section>
</body>
</html>
