# Système de Gestion d'École - StudiaGabon

## 📋 Description

Système complet de gestion d'école primaire et préprimaire développé avec Laravel. Ce système permet de gérer tous les aspects d'un complexe scolaire incluant les élèves, enseignants, parents, classes, notes, présences et frais scolaires.

## 🏗️ Architecture de la Base de Données

### Tables Principales

1. **academic_years** - Gestion des années scolaires
   - Nom de l'année (ex: "2024-2025")
   - Dates de début/fin
   - Statut actuel

2. **classes** - Gestion des classes/niveaux
   - Nom (CP1, CE1, CM1, etc.)
   - Niveau (préprimaire/primaire)
   - Capacité d'élèves

3. **subjects** - Gestion des matières
   - Mathématiques, Français, etc.
   - Coefficients et codes

4. **teachers** - Gestion des enseignants
   - Informations personnelles
   - Qualifications et spécialisations
   - Salaire et statut

5. **students** - Gestion des élèves
   - Numéro matricule unique
   - Informations personnelles
   - Statut d'inscription

6. **parents** - Gestion des parents/tuteurs
   - Informations de contact
   - Profession et lieu de travail
   - Autorisations de récupération

7. **enrollments** - Inscriptions d'élèves
   - Liaison élève-classe-année
   - Statut d'inscription

8. **grades** - Gestion des notes
   - Notes par matière et élève
   - Types d'évaluation (devoir, composition)
   - Commentaires des enseignants

9. **attendances** - Gestion des présences
   - Présences quotidiennes
   - Retards et absences justifiées

10. **fees** - Frais scolaires
    - Frais de scolarité, inscription, etc.
    - Fréquence et échéances

11. **payments** - Paiements
    - Historique des paiements
    - Méthodes de paiement
    - Numéros de reçu

12. **student_parent** - Relation élèves-parents
    - Liens familiaux
    - Contacts principaux

## 🚀 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL
- Laravel 11

### Configuration

1. **Cloner le projet**
```bash
git clone [url-du-projet]
cd gestion-ecole
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Configuration de la base de données**
Le fichier `.env` est déjà configuré avec :
```
DB_CONNECTION=mysql
DB_HOST=mysql-studiagabon.alwaysdata.net
DB_PORT=3306
DB_DATABASE=studiagabon_ecole
DB_USERNAME=417589
DB_PASSWORD=studiagabon2k25
```

4. **Exécuter les migrations**
```bash
php artisan migrate
```

5. **Générer la clé d'application**
```bash
php artisan key:generate
```

6. **Lancer le serveur de développement**
```bash
php artisan serve
```

## 📊 Modèles Eloquent

### Modèles créés :
- `AcademicYear` - Années scolaires
- `SchoolClass` - Classes (pointe vers table `classes`)
- `Subject` - Matières
- `Teacher` - Enseignants
- `Student` - Élèves
- `ParentModel` - Parents (pointe vers table `parents`)
- `Enrollment` - Inscriptions
- `Grade` - Notes
- `Attendance` - Présences
- `Fee` - Frais
- `Payment` - Paiements

## 🔗 Relations

### Relations principales configurées :
- **Students ↔ Parents** : Many-to-Many via `student_parent`
- **Students → Classes** : via Enrollments
- **Students → Grades** : One-to-Many
- **Students → Attendances** : One-to-Many
- **Students → Payments** : One-to-Many

## 🎯 Fonctionnalités

### Gestion Académique
- ✅ Années scolaires
- ✅ Classes et niveaux
- ✅ Matières avec coefficients
- ✅ Inscriptions d'élèves

### Gestion du Personnel
- ✅ Enseignants avec qualifications
- ✅ Spécialisations par matière

### Gestion des Élèves
- ✅ Profils complets des élèves
- ✅ Numéros matricule uniques
- ✅ Gestion des parents/tuteurs

### Évaluations
- ✅ Système de notes complet
- ✅ Types d'évaluation (devoirs, compositions)
- ✅ Commentaires des enseignants

### Présences
- ✅ Suivi quotidien des présences
- ✅ Gestion des retards et absences
- ✅ Justifications d'absence

### Finances
- ✅ Gestion des frais scolaires
- ✅ Suivi des paiements
- ✅ Numéros de reçu
- ✅ Multiple méthodes de paiement

## 📱 Prochaines Étapes

### Développement recommandé :
1. **Interface Web** - Créer les vues Blade
2. **Contrôleurs** - Développer la logique métier
3. **API REST** - Pour intégration mobile
4. **Authentification** - Système de rôles (admin, enseignant, parent)
5. **Rapports** - Bulletins de notes, statistiques
6. **Notifications** - SMS/Email pour parents

### Fonctionnalités avancées :
- Dashboard avec statistiques
- Génération automatique de bulletins
- Calendrier scolaire
- Messagerie interne
- Application mobile pour parents

## 🔧 Maintenance

### Commandes utiles :
```bash
# Réinitialiser la base de données
php artisan migrate:fresh

# Créer un seeder pour données de test
php artisan make:seeder SchoolDataSeeder

# Optimiser l'application
php artisan optimize
```

## 📞 Support

Pour toute question ou support technique, contactez l'équipe de développement StudiaGabon.

---

**Version :** 1.0.0  
**Date :** Juillet 2025  
**Framework :** Laravel 11  
**Base de données :** MySQL 