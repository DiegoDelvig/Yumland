# 🦴 Yumland - Les Croquettes du Chef

> **La gastronomie canine livrée dans sa gamelle**

Plateforme de commande en ligne de croquettes premium pour chiens, avec gestion des clients, livreurs et administrateurs.

---

## 📋 Table des matières

- [À propos](#-à-propos)
- [Caractéristiques](#-caractéristiques)
- [Architecture](#-architecture)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Structure du projet](#-structure-du-projet)
- [Technologies](#-technologies)
- [Contributeurs](#-contributeurs)

---

## 🎯 À propos

**Yumland** est une application web complète de gestion et de vente de croquettes premium pour chiens. Le projet offre une expérience utilisateur complète avec différents rôles d'accès :

- **Clients** : Parcourir le catalogue, commander et suivre leurs commandes
- **Livreurs** : Gérer les livraisons en cours
- **Administrateurs** : Gérer les utilisateurs, les commandes et les menus

Créée en janvier 2026, cette plateforme combine une interface intuitive avec des fonctionnalités avancées pour une gestion optimale du service.

---

## ✨ Caractéristiques

### 🛒 Espace Client
- **Catalogue interactif** : Parcourez les menus filtrables par âge, saveur et spécificités
- **Panier dynamique** : Gérez vos articles en temps réel
- **Commande sécurisée** : Système de paiement intégré
- **Historique** : Consultez vos commandes précédentes
- **Profil** : Gérez vos informations personnelles
- **Fidélité** : Système de coupons et réductions

### 🚀 Espace Livraison
- **Assignation de commandes** : Attribution automatique des livraisons
- **Google Maps intégré** : Navigation vers les adresses de livraison
- **Suivi en temps réel** : État des commandes en cours
- **Confirmation de livraison** : Validation des livraisons complétées

### ⚙️ Espace Administrateur
- **Gestion des utilisateurs** : Vue complète de tous les rôles
- **Filtres avancés** : Clients, livreurs, administrateurs
- **Gestion des commandes** : Suivi et assignation des commandes
- **Contrôle d'accès** : Bloquer/débloquer des comptes utilisateurs
- **Gestion des menus** : Ajouter et modifier les offres

### 🌙 Fonctionnalités Globales
- **Mode sombre/clair** : Thème personnalisable
- **Interface responsive** : Compatible mobile, tablette et desktop
- **Authentification** : Système de login/signup sécurisé
- **Gestion des cookies** : Sessions persistantes
- **Validation** : Vérification des formulaires côté client et serveur

---

## 🏗️ Architecture

### Stack Technologique

Frontend Backend Data ├── HTML5 ├── PHP 8+ ├── JSON ├── CSS3 ├── Sessions └── Cookies └── JavaScript └── Array

Code

### Flux d'utilisateur

[Accueil] → [Catalogue] → [Panier] → [Paiement] → [Confirmation] ↓ [Profil/Commandes] ← [Admin/Livreur]

Code

### Structure des données

Les données sont stockées au format JSON :
- `donnees/data.json` : Informations utilisateurs et rôles
- `donnees/menu.json` : Catalogue de produits et menus

---

## 📦 Installation

### Prérequis
- **PHP** >= 7.4
- **Serveur web** (Apache, Nginx, etc.)
- **Navigateur moderne** (Chrome, Firefox, Safari, Edge)

### Étapes

1. **Cloner le repository**
   ```bash
   git clone https://github.com/DiegoDelvig/Yumland.git
   cd Yumland
Configurer le serveur web

Pointer le répertoire racine vers le dossier du projet
Autoriser les fichiers .json en lecture
Définir les permissions

bash
chmod 755 donnees/
chmod 644 donnees/*.json
Accéder l'application

Code
http://localhost/Yumland
⚙️ Configuration
Variables d'environnement
Aucune variable d'environnement requise pour l'installation basique.

Données de test
Des utilisateurs de test sont disponibles dans donnees/data.json :

Client : Peut commander et suivre ses commandes
Livreur : Peut gérer les livraisons
Admin : Accès complet à l'application
Personnalisation
Logo : assets/Logo projet.png
Styles : css/variables.css (thème et couleurs)
Menus produits : donnees/menu.json
🚀 Utilisation
Pour les clients
Inscription / Connexion

Cliquez sur "Connexion" en haut à droite
Remplissez le formulaire ou créez un compte
Parcourir le catalogue

Allez à "La Carte" pour voir tous les produits
Utilisez les filtres (âge, saveur, spécificités)
Cliquez sur un produit pour voir les détails
Commander

Ajoutez les articles au panier
Cliquez sur le 🛒 en haut
Procédez au paiement
Recevez une confirmation
Suivi

Allez à "Profil" pour voir vos commandes
Consultez le statut et les détails
Pour les livreurs
Connexion

Utilisez vos identifiants livreur
Accédez à "Livraison"
Prendre une commande

Consultez la liste des commandes à livrer
Cliquez sur une commande
Lancez Google Maps pour l'itinéraire
Confirmer la livraison

Cliquez sur "LIVRAISON TERMINÉE"
Le statut se met à jour automatiquement
Pour les administrateurs
Accès Admin

Allez à "Admin" (visible si vous êtes administrateur)
Consultez tous les utilisateurs
Gérer les utilisateurs

Filtrez par type (clients, livreurs, admins)
Bloquez/déverrouillez des comptes
Consultez les profils détaillés
Gérer les commandes

Assignez des livreurs
Suivez l'état des commandes
Validez les commandes prêtes
📂 Structure du projet
Code
Yumland/
├── 📄 index.php              # Page d'accueil
├── 📄 menu.php               # Catalogue de produits
├── 📄 panier.php             # Gestion du panier
├── 📄 login.php              # Authentification
├── 📄 profil.php             # Profil utilisateur
├── 📄 confirmation.php       # Confirmation de commande
├── 📄 annulation.php         # Paiement refusé
├── 📄 admin.php              # Espace administrateur
├── 📄 commandes.php          # Gestion des commandes (admin)
├── 📄 livraisons.php         # Espace livreur
│
├── 📁 css/
│   ├── variables.css         # Thème et variables globales
│   ├── client.css            # Styles généraux
│   ├── accueil.css           # Styles de la page d'accueil
│   ├── admin.css             # Styles de l'espace admin
│   └── confirmation.css      # Styles des confirmations
│
├── 📁 js/
│   ├── charte.js             # Gestion du thème (sombre/clair)
│   ├── admin.js              # Fonctionnalités admin
│   ├── panier.js             # Logique du panier
│   ├── menu.js               # Filtres et recherche
│   ├── profil.js             # Édition de profil AJAX
│   └── livraison.js          # Gestion des livraisons
│
├── 📁 donnees/
│   ├── data.json             # Utilisateurs et rôles
│   └── menu.json             # Catalogue de produits
│
├── 📁 assets/
│   ├── Logo projet.png       # Logo de l'application
│   ├── [images de produits]  # Images des croquettes
│   └── ...
│
└── 📄 README.md              # Ce fichier
🛠️ Technologies
Frontend
HTML5 : Structure sémantique
CSS3 : Grid, Flexbox, Variables CSS
JavaScript (Vanilla) : Pas de framework, code natif
Google Maps API : Navigation et itinéraires
Backend
PHP : Traitement serveur
Sessions PHP : Authentification
Cookies : Persistance des sessions
JSON : Stockage des données
Architecture
MVC-like : Séparation logique
REST-like : Requêtes POST/GET
AJAX : Mise à jour sans rechargement (profil)
🔐 Sécurité
Implémentée
✅ Gestion des sessions via cookies
✅ Vérification des rôles utilisateur
✅ Protection contre les comptes bloqués
✅ Validation des formulaires
✅ Encodage des données sensibles
À améliorer
🔴 Ajouter HTTPS en production
🔴 Implémenter CSRF tokens
🔴 Ajouter rate limiting
🔴 Chiffrer les mots de passe
🔴 Valider entrées côté serveur
📊 Fonctionnalités par page
Page	Clients	Livreurs	Admin
index.php	✅	✅	✅
menu.php	✅	✅	✅
panier.php	✅	❌	❌
profil.php	✅	✅	✅
admin.php	❌	❌	✅
commandes.php	❌	❌	✅
livraisons.php	❌	✅	❌
🐛 Dépannage
La page de connexion s'affiche en boucle
Vérifiez que donnees/data.json existe et est lisible
Assurez-vous que les cookies sont activés
Les images ne s'affichent pas
Vérifiez que le dossier assets/ existe
Vérifiez les chemins dans les fichiers HTML/PHP
Le panier ne se met pas à jour
Vérifiez la console JavaScript (F12)
Assurez-vous que JavaScript est activé
Videz le cache du navigateur
Erreur 500 du serveur
Vérifiez les logs PHP du serveur
Assurez-vous que PHP 7.4+ est installé
Vérifiez les permissions des fichiers
📈 Améliorations futures
 Intégration Stripe/PayPal pour paiements réels
 Système d'avis et notes des produits
 Recommandations personnalisées via IA
 Application mobile native
 Système de notification par email
 Analytics et dashboard produits
 Gestion d'inventaire
 Support multi-langues
👥 Contributeurs
Diego Delvig @DiegoDelvig - Créateur et mainteneur principal
📝 Licence
Ce projet est distribué sous licence non spécifiée. Consultez le repository pour plus de détails.

📞 Support
Pour toute question ou problème :

📧 Ouvrez une issue sur GitHub
💬 Consultez la section discussions
🐛 Signalez les bugs avec détails et reproduction
🎉 Remerciements
Merci à tous les utilisateurs et contributeurs qui aident à améliorer Yumland !

Bienvenue dans l'aventure gastronomique canine ! 🦴✨

Dernière mise à jour : Mai 2026

