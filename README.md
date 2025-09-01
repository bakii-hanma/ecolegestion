# 🏫 Système de Gestion Scolaire - Version Complète

Une application web complète de gestion scolaire développée avec Laravel, offrant toutes les fonctionnalités nécessaires pour gérer un établissement scolaire moderne.

## ✨ Fonctionnalités Principales

### 👥 Gestion des Utilisateurs
- **Système d'authentification** avec rôles multiples (Enseignant, Secrétaire, Administrateur, Super Admin)
- **Interface personnalisée** selon le rôle utilisateur
- **Gestion des permissions** et accès sécurisés

### 🎓 Gestion Académique
- **Élèves** : CRUD complet avec photos, matricules automatiques, informations détaillées
- **Enseignants** : Gestion complète avec affectations aux classes et matières
- **Classes** : Création, affectation des enseignants, gestion des effectifs
- **Matières** : Configuration par niveau avec coefficients
- **Inscriptions** : Processus complet avec validation et reçus PDF

### 📊 Système de Notes et Évaluations
- **Saisie des notes** par matière avec coefficients
- **Note de conduite** (coefficient 0) pour évaluer le comportement
- **Calculs automatiques** : moyennes par matière, moyennes générales
- **Bulletins** : Génération PDF avec code-barres et logo de l'établissement
- **Historique** des notes par trimestre

### 💰 Gestion Financière
- **Frais scolaires** : Configuration par classe et niveau
- **Suivi des paiements** : Statuts, montants, dates
- **Reçus d'inscription** : Génération PDF avec logo de l'établissement
- **Rapports financiers** détaillés

### 📅 Présences et Emplois du Temps
- **Système de présences** par classe et par jour
- **Emplois du temps** : Création et gestion par classe
- **Impression** des emplois du temps

### 📈 Rapports et Statistiques
- **Tableau de bord** avec statistiques en temps réel
- **Rapports de performance** par classe et niveau
- **Statistiques d'inscription** et financières

### 🏢 Administration de l'Établissement
- **Paramètres de l'établissement** : Nom, logo, informations de contact
- **Gestion des utilisateurs** par les administrateurs
- **Configuration système** avancée

## 🛠️ Technologies Utilisées

- **Backend** : Laravel 10+ (PHP 8.2+)
- **Frontend** : Bootstrap 5, JavaScript, CSS3
- **Base de données** : SQLite (développement) / MySQL (production)
- **Génération PDF** : DomPDF, jsPDF
- **Authentification** : Laravel Breeze
- **Interface** : Responsive design, animations CSS

## 🚀 Installation et Configuration

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- Node.js et NPM
- Git

### Installation

1. **Cloner le repository**
```bash
git clone https://github.com/bakii-hanma/ecolegestion.git
cd ecolegestion
```

2. **Installer les dépendances**
```bash
composer install
npm install
```

3. **Configuration de l'environnement**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configuration de la base de données**
```bash
# Modifier .env avec vos paramètres de base de données
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

5. **Migration et seeding**
```bash
php artisan migrate
php artisan db:seed
```

6. **Compilation des assets**
```bash
npm run build
```

7. **Démarrer le serveur**
```bash
php artisan serve
```

### Configuration Initiale

1. **Accéder à l'application** : `http://localhost:8000`
2. **Se connecter** avec les identifiants par défaut :
   - **Super Admin** : `superadmin@studia.com` / `password`
   - **Admin** : `admin@studia.com` / `password`
3. **Configurer les paramètres de l'établissement** via le menu Administration

## 📁 Structure du Projet

```
gestion-ecole/
├── app/
│   ├── Console/Commands/     # Commandes artisan personnalisées
│   ├── Http/Controllers/     # Contrôleurs de l'application
│   │   ├── Admin/           # Contrôleurs d'administration
│   │   └── ...
│   ├── Models/              # Modèles Eloquent
│   └── Providers/           # Fournisseurs de services
├── database/
│   ├── migrations/          # Migrations de base de données
│   └── seeders/             # Seeders pour les données initiales
├── public/
│   ├── css/                 # Styles CSS personnalisés
│   ├── js/                  # Scripts JavaScript
│   └── images/              # Images et assets
├── resources/
│   └── views/               # Templates Blade
│       ├── admin/           # Vues d'administration
│       ├── bulletin/        # Vues des bulletins
│       ├── classes/         # Vues des classes
│       ├── grades/          # Vues des notes
│       └── ...
└── routes/                  # Définition des routes
```

## 👤 Rôles et Permissions

### 🎓 Enseignant
- Gestion des notes de ses classes
- Saisie des présences
- Consultation des emplois du temps
- Accès aux informations des élèves

### 📝 Secrétaire
- Gestion des inscriptions
- Suivi des paiements
- Génération des reçus
- Consultation des rapports

### ⚙️ Administrateur
- Toutes les fonctionnalités des autres rôles
- Gestion des utilisateurs
- Configuration de l'établissement
- Accès aux statistiques complètes

### 🔧 Super Admin
- Toutes les fonctionnalités
- Accès aux paramètres système
- Gestion de la sécurité
- Maintenance du système

## 📄 Génération de Documents

### Bulletins
- **Format A4** optimisé pour l'impression
- **Logo de l'établissement** intégré
- **Code-barres** pour identification
- **Calculs automatiques** des moyennes
- **Export PDF** avec DomPDF

### Reçus d'Inscription
- **Design professionnel** avec logo
- **Informations complètes** de l'élève
- **Détails des paiements**
- **Génération PDF** automatique

### Fiches Élèves
- **Informations détaillées** de l'élève
- **Photo** de l'élève
- **Historique académique**
- **Export PDF** avec jsPDF

## 🎨 Interface Utilisateur

### Design Moderne
- **Interface responsive** pour tous les appareils
- **Couleurs primaires** (bleu) professionnelles
- **Animations fluides** et transitions
- **Navigation intuitive** avec sidebar

### Personnalisation
- **Logo de l'établissement** intégré partout
- **Nom de l'établissement** personnalisable
- **Thème cohérent** dans toute l'application

## 🔒 Sécurité

- **Authentification sécurisée** avec Laravel
- **Protection CSRF** sur tous les formulaires
- **Validation des données** côté serveur
- **Gestion des permissions** par rôle
- **Sessions sécurisées**

## 📊 Base de Données

### Tables Principales
- `users` : Utilisateurs du système
- `students` : Informations des élèves
- `teachers` : Informations des enseignants
- `classes` : Classes et niveaux
- `subjects` : Matières enseignées
- `grades` : Notes des élèves
- `enrollments` : Inscriptions
- `payments` : Paiements
- `school_settings` : Paramètres de l'établissement

## 🚀 Déploiement

### Production
1. **Configuration de l'environnement**
```bash
APP_ENV=production
APP_DEBUG=false
```

2. **Optimisation**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Base de données**
```bash
php artisan migrate --force
```

### Serveur Web
- **Apache** ou **Nginx** recommandé
- **SSL** pour la sécurité
- **PHP-FPM** pour les performances

## 🤝 Contribution

Ce projet est maintenu et développé pour répondre aux besoins spécifiques de gestion scolaire. Pour toute contribution ou suggestion d'amélioration, veuillez créer une issue sur GitHub.

## 📞 Support

Pour toute question ou problème :
- **Issues GitHub** : [https://github.com/bakii-hanma/ecolegestion/issues](https://github.com/bakii-hanma/ecolegestion/issues)
- **Documentation** : Consultez les guides dans le dossier racine

## 📄 Licence

Ce projet est développé pour un usage éducatif et institutionnel.

---

**Version** : 1.0.0  
**Dernière mise à jour** : Septembre 2025  
**Développé avec** ❤️ pour l'éducation
