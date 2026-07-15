# 🏗️ Système de Gestion et de Suivi de Chantiers

> Un système web complet conçu pour digitaliser, suivre et automatiser la gestion quotidienne des chantiers de construction. Développé dans le cadre d'un projet de mémoire de Licence 3 en Génie Logiciel.

---

## 📌 Présentation du Projet

Ce projet vise à résoudre les défis opérationnels des entreprises de construction en centralisant la gestion des ressources, le suivi des tâches et l'administration du personnel sur site. Il propose une interface intuitive pour les chefs de projet et les administrateurs afin d'optimiser la productivité et de fiabiliser le suivi financier.

### Fonctionnalités Clés

- **Tableau de bord de suivi :** Vue globale de l'avancement des chantiers en temps réel.
- **Gestion du cycle de vie des tâches :** Attribution et contrôle qualité des tâches avec un flux de validation rigoureux (_En attente_, _En cours_, _Sous revue_, _Validé_).
- **Pointage & Présence :** Système d'émargement quotidien automatisé pour les ouvriers du chantier.
- **Calcul de la Paie :** Génération automatique des salaires hebdomadaires et mensuels basée sur les heures de présence et le calcul des heures supplémentaires.
- **Gestion des Approvisionnements :**
    - Demende d'approvisionnement et enregistrement des bons de commande et des livraisons sur chantier.
- **Gestion des Rôles et Droits :** Système d'authentification et de permissions sécurisé (Administrateur, Chef de chantier, Ouvrier).

---

## 🛠️ Technologies Utilisées

- **Backend :** [PHP 8.2](https://www.php.net/) & [Laravel 10.x / 11.x](https://laravel.com/)
- **Frontend :** HTML5, CSS3 ([Tailwind CSS](https://tailwindcss.com/)), JavaScript
- **Base de données :** [MySQL](https://www.mysql.com/) (Conception relationnelle rigoureuse)
- **Conception & Modélisation :** UML (Diagrammes de cas d'utilisation, de séquence et de classes)

---

## ⚙️ Installation et Configuration en Local

Suivez ces étapes pour cloner le projet et l'exécuter sur votre machine locale :

### Prérequis

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Un serveur local (ex: XAMPP, Laragon, ou l'utilisation de `php artisan serve`)

### Étapes d'installation

1. **Cloner le dépôt GitHub :**
    ```bash
    git clone [https://github.com/sowELGA/nom-du-depot.git](https://github.com/sowELGA/nom-du-depot.git)
    cd nom-du-depot
    ```
