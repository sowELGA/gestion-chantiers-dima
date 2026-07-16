# 🏗️ Système de Gestion et Suivi des Chantiers — Dima Groupe

> Projet de fin d'études — Licence 3 Génie Logiciel  
> **Université Dakar Bourguiba (UDB)**  
> **Auteur :** Algassimou Sow  
> **Entreprise partenaire :** Dima Groupe — Dakar, Sénégal

---

## 📋 Présentation

Application web de gestion et de suivi des chantiers de construction, développée pour **Dima Groupe**, société de promotion immobilière haut de gamme basée à Dakar.

Le système couvre l'ensemble du cycle de vie d'un chantier : planification, pointage du personnel, approvisionnement en matériaux, suivi budgétaire et génération de documents PDF (fiches de paie, bons d'entrée).

---

## ✨ Fonctionnalités principales

### 🏢 Direction

- Tableau de bord avec KPI (chantiers actifs, budget, personnel, demandes)
- Gestion complète des chantiers (CRUD, transitions de statut)
- Gestion des utilisateurs (chefs de projet, pointeurs)
- Gestion du personnel et des postes
- Configuration des taux salariaux par poste et par chantier
- Validation / rejet des demandes d'approvisionnement
- Visualisation temps réel des pointages hebdomadaires
- Calcul des salaires hebdomadaires
- Génération des fiches de paie en PDF
- Suivi des dépenses par chantier avec filtres par date
- Historique complet des approvisionnements

### 👷 Chef de projet

- Tableau de bord personnel (tâches en retard, fiches à valider)
- Planification des phases et des tâches par chantier
- Suivi de l'avancement des tâches (slider interactif)
- Validation ou rejet des fiches de pointage hebdomadaires avec motif obligatoire
- Création de demandes d'approvisionnement
- Suivi de ses demandes avec filtres par date et statut

### 📋 Pointeur

- Tableau de bord simplifié (fiche du jour, récap semaine, livraisons)
- Saisie des présences journalières (Présent / Absent / Congé / Maladie) et heures supplémentaires
- Récapitulatif hebdomadaire en lecture seule (modifiable uniquement si rejeté par le chef de projet)
- Soumission de la fiche hebdomadaire au chef de projet
- Correction des pointages jour par jour en cas de rejet
- Réception des livraisons et génération des bons d'entrée PDF
- Historique des bons d'entrée avec filtres par date

---

## 🛠️ Stack technique

| Couche           | Technologie                          |
| ---------------- | ------------------------------------ |
| Backend          | Laravel 12 (PHP 8.2)                 |
| Frontend         | Blade + Tailwind CSS 3 + Alpine.js 3 |
| Base de données  | MySQL                                |
| PDF              | DomPDF (barryvdh/laravel-dompdf)     |
| Build            | Vite                                 |
| Diagrammes Gantt | Frappe Gantt                         |

---

## 🗄️ Structure de la base de données

Le système repose sur **14 tables** :

```
users                   → Comptes utilisateurs (direction, chef_projet, pointeur)
postes                  → Postes de travail (Maçon, Grutier, Ferrailleur...)
chantiers               → Chantiers avec budget, statut, chef de projet, pointeur
phases_chantier         → Phases d'un chantier (Fondations, Gros œuvre...)
taches                  → Tâches liées à une phase (avec avancement, dépendances)
personnel               → Ouvriers affectés à un chantier
taux_salaires           → Taux journalier et heure supplémentaire par poste/chantier
pointages               → Présences journalières des ouvriers
recaps_hebdomadaires    → Récapitulatifs hebdomadaires avec calcul de salaire
approvisionnements      → Demandes de matériaux/matériels
rapports_entrees        → Bons d'entrée (réceptions de livraisons)
depenses_chantiers      → Dépenses enregistrées par chantier
rapports_chantiers      → Rapports de chantier
notifications           → Notifications utilisateurs
```

---

## 🚦 Statuts des chantiers

```
EN_ATTENTE  →  EN_COURS  →  SUSPENDU  →  EN_COURS (retour possible)
                         →  LIVRE (final, modifiable via formulaire)
```

| Statut     | Pointage          | Planification |
| ---------- | ----------------- | ------------- |
| EN_ATTENTE | ❌ Non disponible | ✅ Possible   |
| EN_COURS   | ✅ Actif          | ✅ Possible   |
| SUSPENDU   | 🔒 Lecture seule  | ✅ Possible   |
| LIVRE      | ❌ Non disponible | ❌ Bloqué     |

---

## 🔄 Cycle du pointage

```
Pointeur saisit les présences du jour
        ↓
Récap hebdomadaire mis à jour automatiquement (lecture seule)
        ↓
Pointeur soumet la fiche au chef de projet
        ↓
Chef de projet valide ou rejette (avec motif obligatoire)
        ↓
Si rejeté → Pointeur corrige jour par jour et resoumet
        ↓
Si validé → Direction calcule les salaires et génère la fiche de paie PDF
```

---

## 📦 Installation

### Prérequis

- PHP >= 8.2
- Composer
- Node.js >= 18
- MySQL >= 8.0

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/votre-username/gestion-suivi-chantiers.git
cd gestion-suivi-chantiers

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances JS
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
# DB_DATABASE=gestion_chantiers
# DB_USERNAME=votre_user
# DB_PASSWORD=votre_password

# 6. Créer les tables et insérer les données de test
php artisan migrate:fresh --seed

# 7. Compiler les assets
npm run build

# 8. Lancer le serveur
php artisan serve
```

L'application sera accessible sur `http://127.0.0.1:8000`

---

## 👤 Comptes de test

| Email                      | Mot de passe | Rôle                                        |
| -------------------------- | ------------ | ------------------------------------------- |
| direction@dimagroupe.com   | password123  | Direction                                   |
| chefprojet1@dimagroupe.com | password123  | Chef de projet (Liberté 1 + Villa Almadies) |
| chefprojet2@dimagroupe.com | password123  | Chef de projet (3M + Plateau)               |
| pointeur1@dimagroupe.com   | password123  | Pointeur (Résidence Liberté 1)              |
| pointeur2@dimagroupe.com   | password123  | Pointeur (Résidence 3M)                     |
| nouveau@dimagroupe.com     | Dima@1234    | Chef de projet (1ère connexion)             |

> ℹ️ Le compte `nouveau@dimagroupe.com` simule un utilisateur qui doit changer son mot de passe dès la première connexion.

---

## 📁 Structure du projet

```
gestion-suivi-chantiers/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 15 controllers (Auth, Chantier, Tache, Pointage...)
│   │   ├── Middleware/         # CheckRole, CheckPremiereConnexion
│   │   └── Requests/           # Form Requests par module
│   ├── Models/                 # 14 models Eloquent
│   └── Services/               # Logique métier (ChantierService, PointageService...)
├── database/
│   ├── migrations/             # 14 tables + migrations alter
│   └── seeders/                # Données de test complètes
├── resources/
│   └── views/
│       ├── layouts/            # Layouts direction, chef_projet, pointeur
│       ├── direction/          # Vues direction (chantiers, users, salaires...)
│       ├── chef_projet/        # Vues chef de projet (phases, taches, pointage...)
│       ├── pointeur/           # Vues pointeur (fiche, récap, réceptions...)
│       ├── auth/               # Login, changement de mot de passe
│       └── pdf/                # Templates PDF (fiche de paie, bon d'entrée)
├── routes/
│   └── web.php                 # Routes groupées par rôle
└── public/
    └── images/                 # Logo Dima Groupe
```

---

## 🔐 Sécurité et accès

- **Authentification** par email/mot de passe (session Laravel)
- **Middleware `CheckRole`** : chaque groupe de routes est protégé par rôle (`direction`, `chef_projet`, `pointeur`)
- **Middleware `CheckPremiereConnexion`** : redirige vers le changement de mot de passe obligatoire à la 1ère connexion
- Vérification des autorisations dans chaque controller (`abort_if`)

---

## 📄 Génération de PDF

Deux types de documents PDF sont générés via **DomPDF** :

| Document          | Déclencheur                              | Format      |
| ----------------- | ---------------------------------------- | ----------- |
| **Fiche de paie** | Direction après calcul des salaires      | A4 Paysage  |
| **Bon d'entrée**  | Pointeur après réception d'une livraison | A4 Portrait |

Les PDF incluent : logo Dima Groupe, informations du chantier, tableau détaillé, zone de signatures.

---

## 🎨 Charte graphique

| Couleur        | Usage                | Valeur    |
| -------------- | -------------------- | --------- |
| Bleu marine    | Sidebar, fond sombre | `#0F172A` |
| Vert principal | Actions, accents     | `#1C9F93` |
| Or             | Indicateurs budget   | `#D4AF37` |
| Fond clair     | Arrière-plan pages   | `#F8FAFC` |

---

## 🔧 Variables d'environnement importantes

```env
APP_NAME="Gestion Chantiers - Dima Groupe"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_chantiers
DB_USERNAME=root
DB_PASSWORD=

# Locale pour les dates en français
APP_LOCALE=fr
APP_FAKER_LOCALE=fr_FR
```

---

## 🧪 Données de test incluses

Les seeders créent automatiquement :

- **4 chantiers** avec statuts variés (en cours, en attente, livré)
- **18 ouvriers** répartis sur 2 chantiers actifs
- **13 postes** avec taux salariaux configurés
- **Phases et tâches** avec différents avancements
- **Pointages** semaine passée (validée CP) + semaine en cours
- **7 demandes d'approvisionnement** avec tous les statuts possibles
- **Dépenses** réparties sur le chantier Liberté 1

---

## 📊 Modules fonctionnels

| Module             | Direction                | Chef de projet             | Pointeur               |
| ------------------ | ------------------------ | -------------------------- | ---------------------- |
| Chantiers          | ✅ CRUD complet          | 👁️ Lecture + planification | —                      |
| Utilisateurs       | ✅ CRUD complet          | —                          | —                      |
| Personnel          | ✅ CRUD + toggle statut  | —                          | —                      |
| Postes             | ✅ CRUD                  | —                          | —                      |
| Taux salariaux     | ✅ Par chantier/poste    | —                          | —                      |
| Phases & Tâches    | 👁️ Lecture               | ✅ Planification complète  | —                      |
| Pointage           | 👁️ Temps réel + calcul   | ✅ Validation/rejet        | ✅ Saisie + soumission |
| Fiches de paie     | ✅ Calcul + PDF          | —                          | —                      |
| Approvisionnements | ✅ Validation + commande | ✅ Création + suivi        | ✅ Réception + bon PDF |
| Dépenses           | ✅ CRUD + filtres        | —                          | —                      |
| Tableaux de bord   | ✅ KPI global            | ✅ KPI personnel           | ✅ Chantier + jour     |

---

## 🚀 Commandes utiles

```bash
# Réinitialiser la base et recharger les données de test
php artisan migrate:fresh --seed

# Vider les caches
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear

# Mode développement (serveur + Vite en parallèle)
composer run dev

# Lancer les tests
php artisan test
```

---

## 📝 Contexte académique

Ce projet a été réalisé dans le cadre du **Projet de Fin d'Études (PFE)** de la Licence 3 Génie Logiciel à l'**Université Dakar Bourguiba**.

Il constitue la partie pratique du mémoire intitulé :  
**« Conception et Réalisation d'un Système Web de Suivi et de Gestion des Chantiers de Construction »**

---

## 📞 Contact

**Algassimou Sow**  
Étudiant L3 Génie Logiciel — Université Dakar Bourguiba  
📧 algassimousow799@email.com

---

_© 2026 Dima Groupe — Système de gestion des chantiers v1.0_
