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
- [Composition du langage](#-composition-du-langage)
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
- **Commande sécurisée** : Système de paiement intégré (Cybank)
- **Historique** : Consultez vos commandes précédentes
- **Profil** : Gérez vos informations personnelles et notations
- **Fidélité** : Système de coupons et réductions
- **Notation** : Laissez des avis sur les commandes reçues

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
- **Authentification** : Système de login/signup/inscription sécurisé
- **Récupération de mot de passe** : Système de réinitialisation
- **Gestion des cookies** : Sessions persistantes
- **Validation** : Vérification des formulaires côté client et serveur

---

## 🏗️ Architecture

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
- **Clé API Google Maps** (pour la géolocalisation)

### Étapes

1. **Cloner le repository**
   ```bash
   git clone https://github.com/DiegoDelvig/Yumland.git
   cd Yumland
   ```

2. **Configurer le serveur web**
   - Pointer le répertoire racine vers le dossier du projet
   - Autoriser les fichiers .json en lecture
   - Définir les permissions
   ```bash
   chmod 755 donnees/
   chmod 644 donnees/*.json
   ```

3. **Configurer la clé API Google Maps**
   - Placer la clé API dans le dossier `getapikey/`
   - Ou configurer directement dans le fichier concerné

4. **Accéder l'application**
   ```
   http://localhost/Yumland
   ```

---

## ⚙️ Configuration

### Variables d'environnement
Aucune variable d'environnement requise pour l'installation basique.

### Données de test
Des utilisateurs de test sont disponibles dans `donnees/data.json` :

- **Client** : Peut commander et suivre ses commandes
- **Livreur** : Peut gérer les livraisons
- **Admin** : Accès complet à l'application

### Personnalisation
- **Logo** : `assets/Logo projet.png`
- **Styles** : `css/variables.css` (thème et couleurs)
- **Menus produits** : `donnees/menu.json`
- **Charte graphique** : Dossier `charte/`

---

## 🚀 Utilisation

### Pour les clients

#### Inscription / Connexion
- Cliquez sur "Connexion" en haut à droite
- Créez un compte via le formulaire d'inscription
- Ou connectez-vous avec vos identifiants existants

#### Parcourir le catalogue
- Allez à "La Carte" pour voir tous les produits
- Utilisez les filtres (âge, saveur, spécificités)
- Cliquez sur un produit pour voir les détails

#### Commander
- Ajoutez les articles au panier
- Cliquez sur le 🛒 en haut
- Procédez au paiement via Cybank
- Recevez une confirmation

#### Suivi & Notation
- Allez à "Profil" pour voir vos commandes
- Consultez le statut et les détails
- Laissez une notation après réception

#### Mot de passe oublié
- Cliquez sur "Mot de passe oublié" sur la page de connexion
- Suivez les instructions de réinitialisation

### Pour les livreurs

#### Connexion
- Utilisez vos identifiants livreur
- Accédez à "Livraison"

#### Prendre une commande
- Consultez la liste des commandes à livrer
- Cliquez sur une commande
- Lancez Google Maps pour l'itinéraire

#### Confirmer la livraison
- Cliquez sur "LIVRAISON TERMINÉE"
- Le statut se met à jour automatiquement

### Pour les administrateurs

#### Accès Admin
- Allez à "Admin" (visible si vous êtes administrateur)
- Consultez tous les utilisateurs

#### Gérer les utilisateurs
- Filtrez par type (clients, livreurs, admins)
- Bloquez/déverrouillez des comptes
- Consultez les profils détaillés

#### Gérer les commandes
- Assignez des livreurs
- Suivez l'état des commandes
- Validez les commandes prêtes

---

## 📂 Structure du projet

```plaintext
Yumland/
│
├── 📄 index.php                 # Page d'accueil
├── 📄 menu.php                  # Catalogue de produits
├── 📄 panier.php                # Gestion du panier
├── 📄 login.php                 # Authentification (connexion)
├── 📄 inscription.php           # Inscription utilisateur
├── 📄 logout.php                # Déconnexion
├── 📄 mdp_oublie.php            # Page mot de passe oublié
├── 📄 code_mdp_oublie.php       # Traitement réinitialisation
├── 📄 profil.php                # Profil utilisateur
├── 📄 modification.php          # Modification de profil
├── 📄 notation.php              # Système de notation/avis
├── 📄 confirmation.php          # Confirmation de commande
├── 📄 annulation.php            # Paiement refusé
├── 📄 post-cybank.php           # Intégration paiement Cybank
├── 📄 admin.php                 # Espace administrateur
├── 📄 commandes.php             # Gestion des commandes (admin)
├── 📄 livraisons.php            # Espace livreur
├── 📄 livrer_commande.php       # Traitement livraison
├── 📄 api_bloquer_utilisateur.php # API blocage (admin)
├── 📄 verifier_blocage.php      # Vérification blocage utilisateur
│
├── 📁 css/                      # 🎨 Stylesheets
│   ├── variables.css            # Thème et variables globales
│   ├── client.css               # Styles généraux
│   ├── accueil.css              # Styles de la page d'accueil
│   ├── admin.css                # Styles de l'espace admin
│   └── confirmation.css         # Styles des confirmations
│
├── 📁 js/                       # 🧠 Scripts interactifs
│   ├── charte.js                # Gestion du thème (sombre/clair)
│   ├── admin.js                 # Fonctionnalités admin
│   ├── panier.js                # Logique du panier
│   ├── menu.js                  # Filtres et recherche
│   ├── profil.js                # Édition de profil AJAX
│   └── livraison.js             # Gestion des livraisons
│
├── 📁 donnees/                  # 🗄️ Base de données (fichiers plats)
│   ├── data.json                # Utilisateurs et rôles
│   └── menu.json                # Catalogue de produits
│
├── 📁 assets/                   # 🖼️ Ressources graphiques
│   ├── Logo projet.png          # Logo de l'application
│   └── [images de produits]     # Images des articles (croquettes, etc.)
│
├── 📁 charte/                   # 📋 Charte graphique & guidelines
│   └── [fichiers charte]        # Documentation visuelle
│
├── 📁 getapikey/                # 🔑 Configuration API
│   └── [clés API]               # Clés d'accès externes
│
├── 📁 sujets/                   # 📚 Documentation
│   └── [fichiers sujets]        # Sujets de projet & spécifications
│
├── 📁 Rapports/                 # 📊 Rapports & Documentation
│   └── [fichiers rapports]      # Rapports techniques
│
└── 📄 README.md                 # Ce fichier
```

---

## 📊 Composition du langage

| Langage | Pourcentage |
|---------|------------|
| **PHP** | 67.9% |
| **CSS** | 22.1% |
| **JavaScript** | 9.4% |
| **Other** | 0.6% |

Le projet est principalement basé sur **PHP pour le backend** avec une interface **CSS/JavaScript** côté client pour une expérience utilisateur riche et interactive.

---

## 🛠️ Technologies

### Frontend
- **HTML5** : Structure sémantique
- **CSS3** : Grid, Flexbox, Variables CSS, Mode sombre/clair
- **JavaScript (Vanilla)** : Pas de framework, code natif pur
- **Google Maps API** : Navigation et itinéraires en temps réel

### Backend
- **PHP** : Traitement serveur (67.9% du code)
- **Sessions PHP** : Authentification et gestion utilisateur
- **Cookies** : Persistance des sessions
- **JSON** : Stockage des données (fichiers plats)

### Paiement
- **Cybank** : Intégration de paiement sécurisée

### Architecture
- **MVC-like** : Séparation logique client/serveur
- **REST-like** : Requêtes POST/GET
- **AJAX** : Mise à jour sans rechargement de page
- **Responsive Design** : Adaptation tous appareils

---

## 👥 Contributeurs

- **Diego Delvig** [@DiegoDelvig](https://github.com/DiegoDelvig) - Créateur et mainteneur principal

---

## 📅 Historique

- **Janvier 2026** : Création initiale du projet
- **Mai 2026** : Dernière mise à jour - Amélioration structure et documentation

---

## 📝 Licence

© 2026 Yumland. Tous droits réservés.
