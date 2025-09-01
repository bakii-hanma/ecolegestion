# Système de Paramètres de l'Établissement

## 📋 Vue d'ensemble

Ce système permet aux administrateurs et super-administrateurs de configurer les informations de l'établissement scolaire, y compris le logo, le sceau, les informations de contact et autres paramètres système.

## 🚀 Fonctionnalités

### ✅ Paramètres Configurables

- **Informations Générales**
  - Nom de l'établissement
  - Type d'établissement (École Maternelle, Primaire, Collège, Lycée, Université)
  - Niveau (Maternelle, Primaire, Secondaire, Supérieur)
  - Année scolaire

- **Contact et Localisation**
  - Adresse complète
  - Téléphone
  - Email
  - Site web
  - Boîte postale
  - Ville
  - Pays

- **Images et Logos**
  - Logo de l'établissement (JPG, PNG, GIF, max 2MB)
  - Sceau de l'établissement (JPG, PNG, GIF, max 2MB)

- **Direction**
  - Nom du directeur
  - Titre du directeur

- **Paramètres Système**
  - Pays
  - Fuseau horaire
  - Devise (FCFA, EUR, USD)
  - Langue (Français, English)

- **Informations Supplémentaires**
  - Devise de l'établissement
  - Description

## 🔧 Installation et Configuration

### 1. Migration de la Base de Données

```bash
php artisan migrate
```

### 2. Seeder des Paramètres par Défaut

```bash
php artisan db:seed --class=SchoolSettingsSeeder
```

### 3. Service Provider

Le `SchoolServiceProvider` est automatiquement enregistré et partage les paramètres avec toutes les vues.

## 📁 Structure des Fichiers

```
app/
├── Models/
│   └── SchoolSettings.php          # Modèle des paramètres
├── Http/Controllers/Admin/
│   └── SchoolSettingsController.php # Contrôleur d'administration
├── Helpers/
│   └── SchoolHelper.php            # Helper pour accéder aux paramètres
├── Providers/
│   └── SchoolServiceProvider.php   # Service provider
└── Http/Middleware/
    └── CheckRole.php              # Middleware de vérification des rôles

database/
├── migrations/
│   └── create_school_settings_table.php
└── seeders/
    └── SchoolSettingsSeeder.php

resources/views/admin/settings/
├── index.blade.php                 # Formulaire de modification
└── preview.blade.php              # Aperçu des paramètres
```

## 🎯 Utilisation

### Accès à l'Interface d'Administration

1. Connectez-vous en tant qu'admin ou super-admin
2. Dans le menu de navigation, cliquez sur "Paramètres Établissement"
3. Modifiez les paramètres souhaités
4. Cliquez sur "Enregistrer les Paramètres"

### Utilisation dans le Code

#### Via le Helper

```php
use App\Helpers\SchoolHelper;

// Obtenir tous les paramètres
$settings = SchoolHelper::getSettings();

// Obtenir des informations spécifiques
$schoolName = SchoolHelper::getName();
$logo = SchoolHelper::getLogo();
$academicYear = SchoolHelper::getAcademicYear();
$contactInfo = SchoolHelper::getContactInfo();
```

#### Via le Modèle

```php
use App\Models\SchoolSettings;

$settings = SchoolSettings::getSettings();
echo $settings->school_name;
echo $settings->school_phone;
```

#### Dans les Vues Blade

```php
{{ $schoolSettings->school_name }}
{{ $schoolSettings->school_phone }}
{{ $schoolSettings->logo_url }}
```

## 🔐 Sécurité

- **Accès Restreint** : Seuls les utilisateurs avec les rôles `admin` et `superadmin` peuvent accéder aux paramètres
- **Validation** : Toutes les données sont validées avant l'enregistrement
- **Upload Sécurisé** : Les images sont stockées dans le dossier `storage/app/public/school/`
- **Middleware** : Vérification automatique des rôles via le middleware `CheckRole`

## 🎨 Interface Utilisateur

### Formulaire de Modification
- Interface moderne et responsive
- Validation en temps réel
- Prévisualisation des images
- Sections organisées par catégories

### Aperçu des Paramètres
- Visualisation de tous les paramètres
- Aperçu du header du bulletin
- Affichage des logos et sceaux
- Informations organisées par sections

## 🔄 Mise à Jour des Paramètres

### Processus de Mise à Jour

1. **Validation** : Vérification de tous les champs requis
2. **Désactivation** : Les anciens paramètres sont désactivés
3. **Création** : Nouveaux paramètres sont créés avec `is_active = true`
4. **Images** : Gestion automatique des uploads et suppression des anciens fichiers

### Gestion des Images

- **Upload** : Stockage dans `storage/app/public/school/`
- **Suppression** : Suppression automatique des anciennes images
- **Validation** : Formats acceptés : JPG, PNG, GIF, taille max : 2MB
- **Fallback** : Affichage d'icônes si aucune image n'est définie

## 📊 Intégration avec l'Application

### Bulletin de Notes
Les paramètres sont automatiquement utilisés dans :
- Header du bulletin
- Informations de l'établissement
- Logo et sceau
- Titre du directeur

### Autres Modules
- Dashboard principal
- Pages d'accueil
- En-têtes de documents
- Informations de contact

## 🛠️ Maintenance

### Sauvegarde
```bash
php artisan backup:run
```

### Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Optimisation
```bash
php artisan optimize
```

## 📝 Notes Importantes

1. **Un seul jeu de paramètres actif** : Le système ne garde qu'un seul jeu de paramètres actif à la fois
2. **Historique** : Les anciens paramètres sont conservés mais désactivés
3. **Performance** : Les paramètres sont mis en cache pour optimiser les performances
4. **Responsive** : L'interface s'adapte automatiquement aux différentes tailles d'écran

## 🐛 Dépannage

### Problèmes Courants

1. **Images non affichées**
   - Vérifiez que le lien symbolique storage est créé : `php artisan storage:link`
   - Vérifiez les permissions du dossier storage

2. **Accès refusé**
   - Vérifiez que l'utilisateur a le bon rôle (admin ou superadmin)
   - Vérifiez la configuration du middleware CheckRole

3. **Erreurs de validation**
   - Vérifiez que tous les champs requis sont remplis
   - Vérifiez le format et la taille des images

## 🔮 Évolutions Futures

- [ ] Historique des modifications
- [ ] Export/Import des paramètres
- [ ] Paramètres par niveau/cycle
- [ ] Templates de bulletins personnalisables
- [ ] API pour les paramètres
- [ ] Notifications de modification
