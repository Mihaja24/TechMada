<div class="page-header">
    <h1>Nouvelle demande de congé</h1>
    <p class="page-subtitle">Remplissez ce formulaire pour soumettre une nouvelle demande de congé</p>
</div>

<div class="form-card">
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
            <?php if (isset($errors['type_conge_id'])): ?>
                <span class="f-error"><?= htmlspecialchars($errors['type_conge_id'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif ?>
        </div>

        <div class="form-grid-2">
            <div class="f-group">
                <label class="f-label" for="date_debut">Date de début</label>
                <input class="f-input" type="date" id="date_debut" name="date_debut" value="<?= htmlspecialchars($old['date_debut'], ENT_QUOTES, 'UTF-8') ?>" required>
                <?php if (isset($errors['date_debut'])): ?>
                    <span class="f-error"><?= htmlspecialchars($errors['date_debut'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif ?>
            </div>
            <div class="f-group">
                <label class="f-label" for="date_fin">Date de fin</label>
                <input class="f-input" type="date" id="date_fin" name="date_fin" value="<?= htmlspecialchars($old['date_fin'], ENT_QUOTES, 'UTF-8') ?>" required>
                <?php if (isset($errors['date_fin'])): ?>
                    <span class="f-error"><?= htmlspecialchars($errors['date_fin'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif ?>
            </div>
        </div>

        <?php if (isset($errors['date'])): ?>
            <span class="f-error"><?= htmlspecialchars($errors['date'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif ?>

        <?php if (isset($errors['chevauchement'])): ?>
            <span class="f-error"><?= htmlspecialchars($errors['chevauchement'], ENT_QUOTES, 'UTF-8') ?></span>
        <?php endif ?>

        <div class="f-group">
            <label class="f-label" for="motif">Motif</label>
            <textarea class="f-textarea" id="motif" name="motif" required><?= htmlspecialchars($old['motif'], ENT_QUOTES, 'UTF-8') ?></textarea>
            <?php if (isset($errors['motif'])): ?>
                <span class="f-error"><?= htmlspecialchars($errors['motif'], ENT_QUOTES, 'UTF-8') ?></span>
            <?php endif ?>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">Envoyer la demande</button>
            <a href="/employee/mes_demandes" class="btn-secondary">Voir mes demandes</a>
        </div>
    </form>
</div>
