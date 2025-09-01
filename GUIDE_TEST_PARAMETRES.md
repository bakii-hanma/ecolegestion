# Guide de Test du Système de Paramètres de l'Établissement

## 🚀 Serveur en cours d'exécution

Le serveur Laravel est maintenant actif sur : **http://127.0.0.1:8000**

## 📋 Étapes de Test

### 1. Connexion à l'Application

1. Ouvrez votre navigateur et allez sur `http://127.0.0.1:8000`
2. Connectez-vous avec un compte admin ou superadmin :
   - **Email** : `admin@studia.com` (ou un autre compte admin)
   - **Mot de passe** : `password`

### 2. Accès aux Paramètres de l'Établissement

1. Dans le menu de navigation (sidebar), cliquez sur **"Paramètres Établissement"**
2. Vous devriez voir le formulaire de configuration avec les sections :
   - Informations Générales
   - Contact et Localisation
   - Images et Logos
   - Direction
   - Paramètres Système
   - Informations Supplémentaires

### 3. Test des Fonctionnalités

#### A. Modification des Informations de Base
- Changez le nom de l'établissement
- Modifiez le téléphone
- Ajoutez une adresse email
- Changez l'année scolaire

#### B. Upload d'Images (Optionnel)
- Préparez des images de test (JPG, PNG, GIF, max 2MB)
- Testez l'upload du logo de l'établissement
- Testez l'upload du sceau de l'établissement

#### C. Aperçu des Paramètres
- Cliquez sur le bouton **"Aperçu"** en haut à droite
- Vérifiez que toutes les informations s'affichent correctement
- Vérifiez l'aperçu du header du bulletin

### 4. Test de Sauvegarde

1. Remplissez le formulaire avec de nouvelles informations
2. Cliquez sur **"Enregistrer les Paramètres"**
3. Vérifiez que vous recevez un message de succès
4. Vérifiez que les nouvelles informations apparaissent dans l'aperçu

### 5. Test d'Intégration

#### A. Dans le Bulletin de Notes
1. Allez dans **"Bulletins"** → **"Par Classe"**
2. Sélectionnez une classe
3. Cliquez sur un élève pour voir son bulletin
4. Vérifiez que les paramètres de l'établissement apparaissent dans le header

#### B. Dans le Dashboard
1. Retournez au dashboard principal
2. Vérifiez que le nom de l'établissement apparaît correctement

## 🔍 Points à Vérifier

### ✅ Fonctionnalités de Base
- [ ] Formulaire de modification accessible
- [ ] Validation des champs obligatoires
- [ ] Sauvegarde des paramètres
- [ ] Message de succès après sauvegarde
- [ ] Aperçu des paramètres fonctionnel

### ✅ Gestion des Images
- [ ] Upload de logo fonctionnel
- [ ] Upload de sceau fonctionnel
- [ ] Prévisualisation des images actuelles
- [ ] Suppression automatique des anciennes images

### ✅ Sécurité
- [ ] Accès restreint aux admins/superadmins
- [ ] Validation des types de fichiers
- [ ] Limitation de la taille des fichiers

### ✅ Intégration
- [ ] Paramètres visibles dans les bulletins
- [ ] Paramètres visibles dans le dashboard
- [ ] Variable `$schoolSettings` disponible dans les vues

## 🐛 Problèmes Courants et Solutions

### Problème : "Accès non autorisé"
**Solution** : Vérifiez que vous êtes connecté avec un compte admin ou superadmin

### Problème : Images non affichées
**Solution** : 
1. Vérifiez que le lien symbolique storage existe : `php artisan storage:link`
2. Vérifiez les permissions du dossier `storage/app/public`

### Problème : Erreur de validation
**Solution** : 
1. Vérifiez que tous les champs obligatoires sont remplis
2. Vérifiez le format et la taille des images (max 2MB)

### Problème : Paramètres non mis à jour
**Solution** :
1. Videz le cache : `php artisan cache:clear`
2. Videz le cache des vues : `php artisan view:clear`

## 📊 Données de Test

### Paramètres par Défaut
- **Nom** : Lycée XXXXX
- **Téléphone** : 06037499
- **Boîte postale** : BP: 6
- **Année scolaire** : 2024-2025
- **Pays** : Gabon
- **Ville** : Libreville
- **Devise** : FCFA

### Comptes de Test
- **Admin** : `admin@studia.com` / `password`
- **SuperAdmin** : `superadmin@studia.com` / `password`

## 🎯 Résultat Attendu

Après avoir suivi ce guide, vous devriez avoir :
1. ✅ Un système de paramètres entièrement fonctionnel
2. ✅ Une interface d'administration moderne et intuitive
3. ✅ Une intégration complète avec l'application
4. ✅ Une gestion sécurisée des uploads d'images
5. ✅ Un aperçu en temps réel des modifications

## 📞 Support

Si vous rencontrez des problèmes :
1. Vérifiez les logs Laravel : `storage/logs/laravel.log`
2. Vérifiez la console du navigateur pour les erreurs JavaScript
3. Vérifiez que toutes les migrations ont été exécutées : `php artisan migrate:status`
