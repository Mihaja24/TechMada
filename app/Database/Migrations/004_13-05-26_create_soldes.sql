-- Migration: Création de la table soldes
-- Date: 13-05-26

CREATE TABLE IF NOT EXISTS soldes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    employe_id INTEGER NOT NULL,
    type_conge_id INTEGER NOT NULL,
    annee INTEGER NOT NULL,
    jours_attribues REAL NOT NULL DEFAULT 0,
    jours_pris REAL NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employe_id) REFERENCES employes(id) ON DELETE CASCADE,
    FOREIGN KEY (type_conge_id) REFERENCES types_conge(id) ON DELETE RESTRICT,
    UNIQUE(employe_id, type_conge_id, annee)
);

-- Index pour améliorer les performances
CREATE INDEX idx_soldes_employe_id ON soldes(employe_id);
CREATE INDEX idx_soldes_type_conge_id ON soldes(type_conge_id);
CREATE INDEX idx_soldes_annee ON soldes(annee);
