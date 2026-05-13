-- Migration: Création de la table conges
-- Date: 13-05-26

CREATE TABLE IF NOT EXISTS conges (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    nb_jours REAL NOT NULL,
    motif TEXT,
    statut TEXT NOT NULL DEFAULT 'en_attente',
    commentaire_rh TEXT,
    traite_par INTEGER,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employe_id) REFERENCES employes(id) ON DELETE CASCADE,
    FOREIGN KEY (type_conge_id) REFERENCES types_conge(id) ON DELETE RESTRICT,
    FOREIGN KEY (traite_par) REFERENCES employes(id) ON DELETE SET NULL
);

-- Index pour améliorer les performances
CREATE INDEX idx_conges_employe_id ON conges(employe_id);
CREATE INDEX idx_conges_type_conge_id ON conges(type_conge_id);
CREATE INDEX idx_conges_date_debut ON conges(date_debut);
CREATE INDEX idx_conges_date_fin ON conges(date_fin);
CREATE INDEX idx_conges_statut ON conges(statut);
CREATE INDEX idx_conges_traite_par ON conges(traite_par);
