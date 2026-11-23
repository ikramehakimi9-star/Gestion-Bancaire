# Gestion Bancaire – Application Laravel

Mini-projet 2025/2026 – Université Mohammed V de Rabat, École Supérieure de Technologie de Salé.

Application web de gestion des clients et de leurs comptes bancaires, avec virements sécurisés entre comptes.

Réalisé par : Hakimi Ikram et Lakouismi Kawtar

---

## Fonctionnalités

### Gestion des clients
- Ajouter, modifier, supprimer et afficher les clients
- Liste triée par nom, avec pagination
- Recherche par nom, prénom, CIN, email ou téléphone
- La suppression d'un client supprime ses comptes (cascade)

### Gestion des comptes bancaires
- Ajouter, modifier, supprimer et afficher les comptes
- Un client peut avoir plusieurs comptes, un compte appartient à un seul client
- RIB affiché par blocs de 4 chiffres, solde coloré selon sa valeur
- Recherche par RIB (avec ou sans espaces) ou par titulaire
- Fiche compte avec l'historique des virements émis et reçus

### Virements
- Virement entre deux comptes distincts
- Vérification du solde de l'émetteur et message d'erreur en cas de solde insuffisant
- Mise à jour des deux soldes dans une **transaction SQL** (verrouillage `lockForUpdate`, rollback en cas d'erreur)
- Aperçu du solde après virement, fenêtre de confirmation, protection contre le double clic
- Numéro de référence pour chaque virement
- Historique paginé des virements

### Tableau de bord
- Vue d'ensemble : statistiques et comptes les mieux dotés

### Expérience utilisateur
- Messages de succès précis, résumé des erreurs de validation en haut des formulaires
- États vides explicites, titres de pages, indicateur de chargement
- Retour à la liste filtrée (recherche et page) après modification ou suppression

---

## Technologies

| Élément | Outil |
|---|---|
| Framework | Laravel |
| Langage | PHP 8.x |
| Base de données | MySQL |
| ORM | Eloquent |
| Vues | Blade |
| Interface | Bootstrap 5.3 |
| Tests | PHPUnit (`php artisan test`) |

---

## Prérequis

- PHP 8.2 ou supérieur (extensions `pdo_mysql`, `mbstring`, `openssl`)
- Composer
- MySQL / MariaDB (XAMPP, WAMP ou autre)

---

## Installation

```bash
# 0. Récupérer le projet
git clone https://github.com/ikramehakimi9-star/Gestion-Bancaire.git
cd Gestion-Bancaire

# 1. Installer les dépendances
composer install

# 2. Créer le fichier d'environnement
copy .env.example .env          # Windows
# cp .env.example .env          # Linux / macOS

# 3. Générer la clé de l'application
php artisan key:generate
```

4. Créer une base MySQL vide nommée `gestion_bancaire`, puis vérifier dans `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_bancaire
DB_USERNAME=root
DB_PASSWORD=
```

```bash
# 5. Créer les tables et charger les données de démonstration
php artisan migrate --seed

# 6. Lancer le serveur
php artisan serve
```

Ouvrir ensuite **http://127.0.0.1:8000** dans le navigateur.

> **Alternative :** le dossier `database/` contient un export `gestion_bancaire.sql` qui peut être importé dans phpMyAdmin à la place de `migrate --seed`.

---

## Tests automatisés

Créer une base de test vide nommée `gestion_bancaire_test`, puis :

```bash
php artisan test
```

La suite contient **37 tests** couvrant notamment :
- les virements (succès, solde insuffisant, comptes identiques, validation, **rollback de la transaction**)
- le CRUD des clients et des comptes
- les relations Eloquent
- la recherche, les états vides, les messages et le retour aux listes filtrées

---

## Architecture (MVC)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── ClientWebController.php    # CRUD clients + recherche
│   │   ├── CompteWebController.php    # CRUD comptes + recherche
│   │   ├── VirementController.php     # virements (transaction SQL)
│   │   └── DashboardController.php    # tableau de bord
│   └── Requests/                      # validation (Form Requests)
├── Models/
│   ├── Client.php                     # hasMany(Compte)
│   ├── Compte.php                     # belongsTo(Client), accesseurs de format
│   └── Virement.php                   # emetteur / destinataire
database/
├── migrations/                        # tables clients, comptes, virements
├── factories/                         # Factory (données de test)
└── seeders/
resources/views/
├── layouts/app.blade.php              # gabarit commun
├── clients/  comptes/  virements/     # vues Blade
└── dashboard.blade.php
routes/web.php                         # routes
tests/Feature/                         # tests automatisés
```

### Modèle de données

| Table | Champs principaux |
|---|---|
| `clients` | id, nom, prenom, cin, telephone, email, adresse |
| `comptes` | id, rib, solde, client_id (clé étrangère) |
| `virements` | id, compte_emetteur_id, compte_destinataire_id, montant, created_at |

Relations : `Client hasMany Compte`, `Compte belongsTo Client`, `Compte hasMany Virement` (émis et reçus).

---

## Design patterns

| Type | Pattern | Utilisation dans le projet |
|---|---|---|
| Création | Factory | `ClientFactory`, `CompteFactory` pour les tests et les seeders |
| Création | Singleton | Conteneur de services de Laravel (services résolus une seule fois) |
| Structure | MVC | Séparation modèles, vues Blade et contrôleurs |
| Comportement | Observer | Événements Eloquent, utilisés dans le test du rollback |

---

## Sécurité

- Jetons **CSRF** (`@csrf`) dans tous les formulaires
- **Validation** des champs obligatoires et des types via les Form Requests, côté serveur
- Protection contre l'**injection SQL** grâce à Eloquent et aux requêtes préparées
- Échappement automatique des sorties Blade (protection XSS)
- Transaction SQL avec verrouillage des comptes pour éviter les virements concurrents incohérents

---

