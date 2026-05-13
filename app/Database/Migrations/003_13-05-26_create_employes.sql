-- Migration: Création de la table employes
-- Date: 13-05-26

CREATE TABLE IF NOT EXISTS employes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nom TEXT NOT NULL,
    prenom TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT NOT NULL DEFAULT 'employe',
    departement_id INTEGER NOT NULL,
    date_embauche DATE NOT NULL,
    actif INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (departement_id) REFERENCES departements(id) ON DELETE RESTRICT
);

-- Index pour améliorer les performances
CREATE INDEX idx_employes_email ON employes(email);
CREATE INDEX idx_employes_departement_id ON employes(departement_id);
CREATE INDEX idx_employes_role ON employes(role);
CREATE INDEX idx_employes_actif ON employes(actif);
