-- Migration: Création de la table types_conge
-- Date: 13-05-26

CREATE TABLE IF NOT EXISTS types_conge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL UNIQUE,
    jours_annuels REAL NOT NULL DEFAULT 20,
    deductible INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_types_conge_libelle ON types_conge(libelle);
