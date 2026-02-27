# SPÉCIFICATION FONCTIONNELLE
# Pépi+ – Application Web de Gestion de Stock Pépinière

---

| Champ               | Valeur                                         |
|---------------------|------------------------------------------------|
| **Projet**          | Pépi+ – Gestion de Stock Pépinière             |
| **Version**         | V1.0                                           |
| **Date de rendu**   | 27/03/2026                                     |
| **Date d'évaluation** | Avril 2026                                   |
| **Auteur**          | CASTELLS Cyprien                               |
| **Établissement**   | Pôle Sup Saint-Denis – BTS SIO SLAM 2025/2026  |
| **Statut**          | Validé                                         |

---

## Table des matières

1. [Introduction](#1-introduction)
2. [Présentation générale du projet](#2-présentation-générale-du-projet)
3. [Acteurs et Cas d'Utilisation](#3-acteurs-et-cas-dutilisation)
4. [Exigences fonctionnelles](#4-exigences-fonctionnelles)
5. [Maquettes et Interface Homme-Machine (IHM)](#5-maquettes-et-interface-homme-machine-ihm)
6. [Règles de gestion](#6-règles-de-gestion)
7. [Exigences non fonctionnelles](#7-exigences-non-fonctionnelles)
8. [Modèle de données](#8-modèle-de-données)
9. [Interfaces externes et intégrations](#9-interfaces-externes-et-intégrations)
10. [Glossaire et traçabilité](#10-glossaire-et-traçabilité)
11. [Gestion et évolution du document](#11-gestion-et-évolution-du-document)
12. [Synthèse](#12-synthèse)

---

## 1. Introduction

### 1.1 Qu'est-ce qu'une Spécification Fonctionnelle ?

La Spécification Fonctionnelle (SF) est un document technique qui traduit les besoins exprimés dans le Cahier des Charges (CdC) en descriptions précises de ce que doit faire le système. Elle répond à la question **« QUOI faire ? »**, et non « COMMENT le faire ? » (qui relève des spécifications techniques).

Elle sert de référence tout au long du projet pour guider le développement, les tests et la recette.

### 1.2 Objectifs de ce document

- Formaliser les besoins exprimés dans le CdC fourni par le Pôle Sup Saint-Denis
- Décrire précisément les fonctionnalités attendues de l'application Pépi+
- Servir de base aux développements, aux tests et à l'évaluation en Avril 2026
- Assurer la traçabilité entre les besoins du CdC et les fonctionnalités réalisées

---

## 2. Présentation générale du projet

### 2.1 Contexte

La société **Pépi+** est un pépiniériste dont le modèle économique repose à la fois sur la **production de plants en interne** et sur la **revente de plants fournis par des partenaires fournisseurs**.

Les pépiniéristes ont des **obligations sanitaires réglementaires**, notamment en matière de **traçabilité des produits vendus** : chaque plant vendu doit pouvoir être tracé par sa pépinière de provenance et sa saison de production.

Aujourd'hui, la gestion des stocks, des commandes et des partenaires est réalisée **sans outil informatique dédié**. Le personnel de Pépi+ **n'a aucune compétence en informatique**, ce qui implique une application simple, ergonomique et opérationnelle sans formation complexe.

Pépi+ souhaite se moderniser en dotant ses équipes et ses partenaires d'une **application web de gestion des stocks**, développée dans le cadre du BTS SIO SLAM 2025/2026.

### 2.2 Identification des parties prenantes

| Partie prenante | Rôle |
|---|---|
| **Pépi+** (commanditaire) | Société cliente – définit les besoins du CdC |
| **Collaborateurs Pépi+** | Personnel du service commercial – utilisateurs principaux |
| **Partenaires fournisseurs** | Fournisseurs externes avec accès restreint à l'application |
| **Administrateur** | Gestionnaire des accès et du paramétrage de l'application |
| **CASTELLS Cyprien** | Développeur – conception, réalisation et formation |
| **Professeur évaluateur** | Pôle Sup Saint-Denis – évalue la conformité au CdC en Avril 2026 |

### 2.3 Périmètre du projet

**INCLUS :**
- Application web interne pour les collaborateurs et administrateurs
- Application web partenaire (espace restreint pour les fournisseurs)
- Gestion du stock réel (production interne) et virtuel (partenaires)
- Traçabilité obligatoire des plants (provenance + saison)
- Gestion des commandes avec réservation automatique des stocks
- Tableau de bord de pilotage de l'activité
- Gestion du référentiel : plants (nom latin / commun), conditionnements, saisons, partenaires
- Gestion des accès et des utilisateurs (collaborateurs + partenaires)

**EXCLU :**
- Application mobile native (iOS / Android)
- Module de facturation ou de paiement
- Intégration avec un ERP ou logiciel comptable externe
- Gestion des livraisons physiques
- Vente directe aux particuliers (e-commerce public)

---

## 3. Acteurs et Cas d'Utilisation

### 3.1 Identification des acteurs

**Acteurs primaires :**

- **Visiteur (non connecté)** : accès uniquement à la page de connexion
- **Collaborateur (COLLABORATOR)** : personnel du service commercial de Pépi+ — gestion des stocks, commandes, tableau de bord et référentiel
- **Administrateur (ADMIN)** : tous les droits du collaborateur + gestion des utilisateurs, partenaires
- **Partenaire (PARTNER)** : fournisseur externe — gestion de son propre stock virtuel et consultation de ses réservations

**Acteurs secondaires :**

- **Serveur SMTP** : envoi des emails (réinitialisation de mot de passe)

### 3.2 Diagramme des cas d'utilisation

```
Visiteur
└── UC-01 : Se connecter / Se déconnecter

Collaborateur (hérite de Visiteur)
├── UC-02 : Consulter le tableau de bord
├── UC-03 : Visualiser le stock global (réel + virtuel)
├── UC-04 : Rechercher et filtrer les plants
├── UC-05 : Créer une commande
├── UC-06 : Modifier une commande
├── UC-07 : Supprimer / Annuler une commande
└── UC-08 : Consulter l'historique des commandes
├── UC-09 : Gérer le référentiel de plants (CRUD)
├── UC-10 : Gérer les conditionnements (CRUD)
└── UC-11 : Gérer les saisons (CRUD)

Administrateur (hérite de Collaborateur)
├── UC-12 : Gérer les collaborateurs (CRUD)
├── UC-13 : Gérer les partenaires fournisseurs (CRUD)


Partenaire (hérite de Visiteur)
├── UC-14 : Gérer son stock pour une saison donnée
└── UC-15 : Consulter les réservations effectuées sur son stock
```

### 3.3 Fiches de cas d'utilisation

---

#### UC-01 : Se connecter

| Champ | Détail |
|---|---|
| **Acteur principal** | Tous les utilisateurs |
| **Pré-conditions** | L'utilisateur dispose d'un compte actif |
| **Déclencheur** | L'utilisateur soumet le formulaire de connexion |
| **Scénario nominal** | 1. Saisie de l'email et du mot de passe – 2. Vérification des identifiants – 3. Identification du rôle – 4. Redirection vers l'interface adaptée au rôle |
| **Scénarios alternatifs** | A1 : Identifiants incorrects → message d'erreur. A2 : Compte inactif → accès refusé |
| **Post-conditions** | L'utilisateur est authentifié et accède à son espace |
| **Règles de gestion** | RG-01 |

---

#### UC-03 : Visualiser le stock global

| Champ | Détail |
|---|---|
| **Acteur principal** | Collaborateur, Administrateur |
| **Pré-conditions** | L'utilisateur est authentifié |
| **Déclencheur** | L'utilisateur accède à la page stock |
| **Scénario nominal** | 1. Chargement du stock réel et virtuel – 2. Affichage dans un tableau avec filtres – 3. Pour chaque plant : nom latin, nom commun, conditionnement, saison, quantité disponible, origine (interne / partenaire) |
| **Post-conditions** | L'utilisateur dispose d'une vue complète et à jour des stocks |
| **Règles de gestion** | RG-02, RG-03 |

---

#### UC-05 : Créer une commande

| Champ | Détail |
|---|---|
| **Acteur principal** | Collaborateur, Administrateur |
| **Pré-conditions** | L'utilisateur est authentifié. Le stock contient des plants disponibles |
| **Déclencheur** | L'utilisateur clique sur « Nouvelle commande » |
| **Scénario nominal** | 1. Sélection des plants et des quantités – 2. Vérification de la disponibilité en stock (réel ou virtuel) – 3. Réservation des plants et décrémentation du stock – 4. Association à un numéro de commande – 5. Enregistrement et affichage du récapitulatif |
| **Scénarios alternatifs** | A1 : Plant indisponible → message d'alerte, impossibilité de valider. A2 : Quantité demandée supérieure au stock → message d'erreur |
| **Post-conditions** | La commande est enregistrée. Les stocks sont mis à jour. La traçabilité (provenance + saison) est associée à chaque plant réservé |
| **Règles de gestion** | RG-04, RG-05, RG-06 |

---

#### UC-06 : Modifier une commande

| Champ | Détail |
|---|---|
| **Acteur principal** | Collaborateur, Administrateur |
| **Pré-conditions** | La commande existe et l'utilisateur est authentifié |
| **Déclencheur** | L'utilisateur modifie une commande existante (problème sur les stocks saisis) |
| **Scénario nominal** | 1. Sélection de la commande – 2. Modification des plants ou des quantités – 3. Recalcul et mise à jour des réservations – 4. Impact automatique sur les stocks (libération et re-réservation) |
| **Post-conditions** | La commande est mise à jour. Les stocks reflètent la modification |
| **Règles de gestion** | RG-05, RG-06, RG-07 |

---

#### UC-07 : Supprimer / Annuler une commande

| Champ | Détail |
|---|---|
| **Acteur principal** | Collaborateur, Administrateur |
| **Pré-conditions** | La commande existe |
| **Déclencheur** | L'utilisateur supprime ou annule une commande |
| **Scénario nominal** | 1. Confirmation de la suppression – 2. Libération des stocks réservés – 3. Mise à jour des quantités disponibles |
| **Post-conditions** | Les plants réservés sont restitués aux stocks appropriés |
| **Règles de gestion** | RG-07 |

---

#### UC-09 : Gérer les collaborateurs

| Champ | Détail |
|---|---|
| **Acteur principal** | Administrateur |
| **Pré-conditions** | L'utilisateur est authentifié avec le rôle ADMIN |
| **Déclencheur** | L'administrateur accède au module de gestion des utilisateurs |
| **Scénario nominal** | 1. Consultation de la liste des collaborateurs – 2. Création / modification / désactivation d'un compte – 3. Attribution du rôle (ADMIN ou COLLABORATOR) – 4. Gestion des problèmes d'accès |
| **Post-conditions** | Les modifications sont persistées en base |
| **Règles de gestion** | RG-08, RG-09 |

---

#### UC-14 : Gérer son stock (Partenaire)

| Champ | Détail |
|---|---|
| **Acteur principal** | Partenaire |
| **Pré-conditions** | L'utilisateur est authentifié avec le rôle PARTNER |
| **Déclencheur** | Le partenaire accède à son espace de gestion de stock |
| **Scénario nominal** | 1. Visualisation du stock du partenaire par plant et par saison – 2. Mise à jour des quantités disponibles – 3. Enregistrement |
| **Post-conditions** | Le stock virtuel du partenaire est mis à jour dans le stock global de Pépi+ |
| **Règles de gestion** | RG-10, RG-14 |

---

#### UC-15 : Consulter ses réservations (Partenaire)

| Champ | Détail |
|---|---|
| **Acteur principal** | Partenaire |
| **Pré-conditions** | L'utilisateur est authentifié avec le rôle PARTNER |
| **Déclencheur** | Le partenaire accède à la section « Mes réservations » |
| **Scénario nominal** | 1. Affichage des réservations effectuées sur son stock – 2. Détail par commande : plant, saison, quantité réservée |
| **Post-conditions** | Le partenaire dispose d'une vue en lecture seule de l'utilisation de son stock |
| **Règles de gestion** | RG-10 |

---

## 4. Exigences fonctionnelles

### 4.1 Structure et numérotation

Format : `EF-[MODULE]-[NUMÉRO]`

Modules : **AUTH** (authentification), **DASH** (tableau de bord), **STOCK** (stocks), **CMD** (commandes), **REF** (référentiels), **ADMIN** (administration), **PART** (partenaire)

### 4.2 Tableau des exigences fonctionnelles

| ID | Description | Priorité | Complexité | Module | Acteurs |
|---|---|---|---|---|---|
| EF-AUTH-01 | Connexion sécurisée par email / mot de passe | MUST | Faible | AUTH | Tous |
| EF-AUTH-02 | Réinitialisation du mot de passe par email | MUST | Moyenne | AUTH | Tous |
| EF-AUTH-03 | Gestion des rôles : ADMIN, COLLABORATOR, PARTNER | MUST | Moyenne | AUTH | ADMIN |
| EF-AUTH-04 | Déconnexion et expiration de session | MUST | Faible | AUTH | Tous |
| EF-DASH-01 | Tableau de bord avec vue d'ensemble de l'activité | MUST | Moyenne | DASH | Collaborateur, Admin |
| EF-DASH-02 | Alertes visuelles pour les stocks faibles en temps réel | MUST | Moyenne | DASH | Collaborateur, Admin |
| EF-DASH-03 | Affichage des dernières commandes | SHOULD | Faible | DASH | Collaborateur, Admin |
| EF-STOCK-01 | Visualisation du stock global (réel + virtuel) de manière ergonomique | MUST | Haute | STOCK | Collaborateur, Admin |
| EF-STOCK-02 | Recherche de plants par nom latin et nom commun (les deux champs sont obligatoires) | MUST | Moyenne | STOCK | Collaborateur, Admin |
| EF-STOCK-03 | Filtres avancés : saison, conditionnement, origine, disponibilité | MUST | Moyenne | STOCK | Collaborateur, Admin |
| EF-STOCK-04 | Traçabilité : affichage provenance + saison pour chaque plant | MUST | Haute | STOCK | Collaborateur, Admin |
| EF-STOCK-05 | Distinction visuelle stock réel / stock virtuel | MUST | Faible | STOCK | Collaborateur, Admin |
| EF-STOCK-06 | Gestion du stock réel par saison (CRUD) | MUST | Haute | STOCK | Collaborateur, Admin |
| EF-CMD-01 | Création d'une commande avec sélection de plants et quantités | MUST | Haute | CMD | Collaborateur, Admin |
| EF-CMD-02 | Réservation automatique des plants à la validation de la commande | MUST | Haute | CMD | Collaborateur, Admin |
| EF-CMD-03 | Décrémentation automatique du stock à la réservation | MUST | Haute | CMD | Collaborateur, Admin |
| EF-CMD-04 | Modification d'une commande avec recalcul automatique des stocks | MUST | Haute | CMD | Collaborateur, Admin |
| EF-CMD-05 | Suppression / annulation avec restitution automatique des stocks réservés | MUST | Moyenne | CMD | Collaborateur, Admin |
| EF-CMD-06 | Historique et suivi des statuts de commande | MUST | Moyenne | CMD | Collaborateur, Admin |
| EF-CMD-07 | Traçabilité sanitaire : provenance + saison pour tous les plants d'une commande | MUST | Haute | CMD | Collaborateur, Admin |
| EF-REF-01 | Gestion du référentiel de plants : nom latin + nom commun (CRUD) | MUST | Faible | REF | Admin |
| EF-REF-02 | Gestion des conditionnements (ex. GF400, racine nue…) (CRUD) | MUST | Faible | REF | Admin |
| EF-REF-03 | Gestion des saisons (CRUD) | MUST | Faible | REF | Admin |
| EF-ADMIN-01 | Gestion des comptes collaborateurs (CRUD + activation/désactivation) | MUST | Moyenne | ADMIN | Admin |
| EF-ADMIN-02 | Gestion des partenaires : nom, coordonnées, accès applicatif (CRUD) | MUST | Moyenne | ADMIN | Admin |
| EF-PART-01 | Espace partenaire : gestion de son stock par plant et par saison | MUST | Haute | PART | Partenaire |
| EF-PART-02 | Espace partenaire : consultation en lecture seule des réservations sur son stock | MUST | Moyenne | PART | Partenaire |

> **MoSCoW :** **MUST** = obligatoire | **SHOULD** = importante non bloquante | **COULD** = souhaitée si temps disponible

---

## 5. Maquettes et Interface Homme-Machine (IHM)

### 5.1 Charte graphique

| Élément | Spécification |
|---|---|
| **Couleur de fond global** | `bg-menthe-clair` (couleur personnalisée TailwindCSS – fond général de l'application) |
| **Fond des pages** | `bg-gray-100` (gris clair pour le corps des pages) |
| **Fond des tableaux / cartes** | `bg-white` avec `shadow-sm`, `border border-gray-200` |
| **Fond des en-têtes de tableau** | `bg-gray-50` |
| **Texte titres** | `text-gray-900`, `font-bold`, taille `text-2xl` |
| **Texte secondaire** | `text-gray-500`, taille `text-sm` |
| **Badge Stock Réel (Pépi+)** | `bg-blue-100 text-blue-800` – libellé : *Stock Réel (Pépi+)* |
| **Badge Stock Virtuel (Partenaire)** | `bg-purple-100 text-purple-800` – affiche le nom du partenaire |
| **Badge statut Réservation** | `bg-yellow-100 text-yellow-800` |
| **Badge statut Annulée** | `bg-red-100 text-red-800` |
| **Badge statut En cours / Livrée** | `bg-green-100 text-green-800` |
| **Quantité critique (< 10)** | `text-red-600 font-bold` – seuil fixé à 10 dans le `DashboardController` |
| **Quantité normale** | `text-gray-900 font-bold` |
| **Flash succès** | `bg-green-50 border-green-200 text-green-800` |
| **Flash erreur** | `bg-red-50 border-red-200 text-red-800` |
| **Icônes actions – voir** | `text-green-600 hover:text-green-900` |
| **Icônes actions – modifier** | `text-blue-600 hover:text-blue-900` (aussi `text-indigo-600` pour commandes) |
| **Bouton panier** | `text-blue-600 bg-blue-50 rounded-lg` |
| **Typographie titre** | Google Fonts : **Montserrat** (weight 500, 600, 700) |
| **Typographie corps** | Google Fonts : **Open Sans** (weight 300, 400, 600) |
| **Framework CSS** | TailwindCSS 4.1 + Preline UI (composants JS via CDN) + Alpine.js |
| **Moteur de templates** | Twig (rendu serveur Symfony) |
| **Icônes** | Twig UX Icons (`bi:check-lg`, `iconoir:warning-circle`, `mingcute:eye-line`, `cuida:edit-outline`, `iconoir:cart`) |
| **Navigation** | Sidebar fixe à gauche (`lg:ml-72`), masquée si non connecté |
| **Responsive** | `lg:ml-72` pour la sidebar – breakpoints Tailwind standard |

### 5.2 Structure de navigation

```
Page de connexion (public)
└── Réinitialisation du mot de passe (public)

Espace Collaborateur / Administrateur
├── Tableau de bord
├── Stock
│   ├── Vue globale (réel + virtuel)
│   └── Fiche plant (traçabilité : provenance + saison)
├── Commandes
│   ├── Liste des commandes
│   ├── Nouvelle commande
│   └── Détail / Modification d'une commande
└── Administration (ADMIN uniquement)
    ├── Gestion des collaborateurs
    ├── Gestion des partenaires
    ├── Référentiel des plants
    ├── Conditionnements
    └── Saisons

Espace Partenaire
├── Mon stock (par saison)
└── Mes réservations
```

### 5.3 Description fonctionnelle des pages principales

Les pages de l'application sont générées par le moteur de templates **Twig** côté serveur. Aucun wireframe Figma n'a été produit pour ce projet ; les interfaces sont décrites fonctionnellement ci-dessous à partir des controllers et templates réalisés.

**Page de connexion** (`/login` → `SecurityController`)
Formulaire avec champ email et mot de passe. En cas d'erreur, le dernier email saisi est conservé et un message d'erreur est affiché. Lien « Mot de passe oublié » disponible.

**Tableau de bord** (`/dashboard` → `DashboardController`)
Deux vues distinctes selon le rôle :
- *Collaborateur / Admin* : compteur total de commandes, total partenaires, liste des stocks sous le seuil d'alerte (seuil = 10), liste des 5 dernières commandes.
- *Partenaire* : liste de ses propres stocks, alertes sur ses stocks critiques, commandes récentes contenant ses produits.

**Vue stock global** (`/stock/global` → `StockController`)
Tableau paginé (7 éléments/page) avec filtres avancés (`StockFilterType`). Colonnes réelles : **Plant (Nom Latin / Nom Commun)**, **Origine**, **Saison**, **Conditionnement**, **Quantité**, **Actions**. L'origine affiche un badge `bg-blue-100` (*Stock Réel – Pépi+*) ou `bg-purple-100` (nom du partenaire). La quantité s'affiche en `text-red-600` si inférieure à 10. Actions : éditer (stock réel uniquement), ajouter au panier avec saisie de quantité (stock virtuel uniquement), voir la fiche. Route `/stock/gestion` pour la vue stock interne seul (`partner IS NULL`).

**Gestion des commandes** (`/order` → `OrderController`)
Tableau paginé (10/page) avec filtres (`OrderFilterType`). Colonnes réelles : **Numéro de commande**, **Statut**, **Créé le**, **Mis à jour le**, **Actions**. Badges de statut : `bg-yellow-100` (Réservation), `bg-red-100` (Annulée), `bg-green-100` (En cours / Livrée). Action modifier uniquement visible si statut = `Réservation`. Actions disponibles sur fiche : livrer (`/order/{id}/deliver`) et annuler (`/order/{id}/cancel`).

**Panier** (`/cart` → `CartController`)
Ajout de stocks au panier (`/add/{id}`), modification des quantités (`/update/{id}` avec ajustement automatique au stock disponible), suppression (`/remove/{id}`), validation (`/validate`). À la validation : création de la commande, décrémentation immédiate du stock, enregistrement de l'historique de statut.

**Espace partenaire**
- *Mon stock* (`/partner/my-stock/liste`) : liste paginée (8/page) du stock du partenaire connecté, avec recherche par nom latin / commun.
- *Mes réservations* (`/partner/my-reservations/liste`) : liste des `OrderLine` portant sur son stock.
- *Nouveau stock* (`/partner/my-stock/new`) : formulaire de création d'une entrée de stock virtuel.

**Administration**
- Utilisateurs (`/user`) : CRUD complet avec réinitialisation de mot de passe par l'admin (mot de passe provisoire `Password123!`, flag `must_change_password` activé).
- Partenaires (`/partner/gestion/new`, `/partner/edit/{id}`) : CRUD.
- Référentiel plants (`/plant`) : CRUD avec pagination 9/page.
- Conditionnements (`/packaging`) : CRUD avec pagination 10/page.
- Saisons (`/season`) : CRUD avec pagination 10/page. Route dédiée partenaire : `/season/my-stock/new`.

---

## 6. Règles de gestion

| ID | Règle de gestion | Exigences liées |
|---|---|---|
| RG-01 | La session utilisateur expire après 30 minutes d'inactivité | EF-AUTH-04 |
| RG-02 | Une alerte de stock critique est déclenchée lorsque la quantité d'un stock passe sous **10 unités** (seuil fixé dans `DashboardController` et `StockRepository::findLowStockAlert(10)`). La quantité s'affiche en rouge dans les tableaux | EF-DASH-02, EF-STOCK-01 |
| RG-02b | Le stock global (`/stock/global`) affiche tous les stocks (réels + virtuels) dans un seul tableau. Le stock interne uniquement est accessible via `/stock/gestion` | EF-STOCK-01, EF-STOCK-05 |
| RG-03 | Le stock réel et le stock virtuel sont distingués dans l'affichage (origine visible pour chaque plant) | EF-STOCK-05 |
| RG-04 | Les plants réservés dans une commande peuvent provenir du stock réel ou d'un stock virtuel partenaire | EF-CMD-01, EF-CMD-02 |
| RG-05 | La réservation est automatique dès la validation du panier : la commande est créée avec le statut `Réservation` et le stock est décrémenté immédiatement | EF-CMD-02, EF-CMD-03 |
| RG-05b | Cycle de vie d'une commande : `Réservation` → `En cours` → `Livrée` (irréversible) ou `Annulée` (impossible si déjà `Livrée`) | EF-CMD-06 |
| RG-05c | Lors du passage au statut `Livrée`, les plants réservés sur le stock virtuel d'un partenaire sont basculés dans le stock réel interne de Pépi+ (création ou mise à jour d'un stock avec `partner = NULL`) | EF-CMD-02, EF-STOCK-06 |
| RG-06 | Pour tous les plants d'une commande, la traçabilité sanitaire (pépinière de provenance + saison) doit être disponible | EF-CMD-07 |
| RG-07 | La modification ou la suppression d'une commande impacte automatiquement les stocks (libération et/ou re-réservation) | EF-CMD-04, EF-CMD-05 |
| RG-08 | L'administrateur gère les entrées/sorties des collaborateurs et les problèmes d'accès | EF-ADMIN-01 |
| RG-09 | L'adresse email est l'identifiant unique de chaque utilisateur dans le système | EF-AUTH-01, EF-ADMIN-01 |
| RG-10 | Un partenaire ne peut accéder qu'à son propre stock et à ses propres réservations, pas à ceux des autres partenaires | EF-PART-01, EF-PART-02 |
| RG-11 | Chaque plant doit obligatoirement avoir un nom latin ET un nom commun (les deux champs sont `NOT NULL` dans la base de données) | EF-REF-01 |
| RG-12 | La saison correspond à l'année de production du plant et permet de connaître son âge au moment de la vente | EF-REF-03, EF-CMD-07 |
| RG-13 | Un plant ne peut être réservé dans une commande que si sa quantité disponible est supérieure à 0 | EF-CMD-01 |
| RG-15 | Lorsqu'un admin réinitialise le mot de passe d'un utilisateur, un mot de passe provisoire `Password123!` est attribué et le flag `must_change_password` est activé : l'utilisateur est forcé à changer son mot de passe à la prochaine connexion | EF-ADMIN-01 |

---

## 7. Exigences non fonctionnelles

### 7.1 Performances

- Temps de chargement d'une page < 2 secondes pour 95% des requêtes
- Recherche dans le catalogue de plants : résultats retournés en < 500ms
- Pagination obligatoire sur les listes dépassant 20 éléments (KnpPaginator Bundle)

### 7.2 Sécurité

- Authentification par le **Symfony Security Bundle** avec hachage bcrypt
- Toutes les communications chiffrées via **HTTPS**
- Protection contre les **injections SQL** (Doctrine ORM avec requêtes préparées)
- Protection contre les attaques **XSS** (échappement automatique Twig) et **CSRF** (tokens de formulaire Symfony)
- Contrôle d'accès **RBAC** : chaque route protégée par rôle (`is_granted()`)
- Token de réinitialisation de mot de passe à **usage unique**, expirant dans les 24h
- Respect des **règles de codage sécurisé** et bonnes pratiques métier imposées par le CdC

### 7.3 Disponibilité et fiabilité

- Exports de base de données réguliers dans `var/backups/db_export_xxxx-xx-xx.sql`
- Migrations Doctrine versionnées dans `migrations/` pour une reproductibilité complète
- Variables sensibles externalisées dans `.env.local` (non versionné dans Git)

### 7.4 Compatibilité

- Navigateurs supportés : Chrome, Firefox, Safari, Edge (2 dernières versions)
- Responsive design : mobile (≥ 320px), tablette, desktop – approche mobile-first TailwindCSS

### 7.5 Règles de codage (imposées par le CdC)

- Pattern **MVC** strictement respecté (Controller / Entity / Repository / Form / Template Twig)
- Architecture de production **3-tiers** (client / serveur applicatif Symfony / base de données MySQL)
- Tout le code commenté **en français**
- Noms de fichiers, classes, méthodes et membres **explicites et cohérents** avec leurs fonctions
- Déploiement rapide grâce au README et aux migrations Doctrine
- Git : **1 commit minimum par fonctionnalité**, messages de commit pertinents et explicites
- Jeu de données de test : **au moins 10 enregistrements par table**

---

## 8. Modèle de données

### 8.1 Description des entités principales

> 📌 Le MCD complet est dans `assets/MCD.pdf` et le MLD dans `assets/MLD.pdf`.

Le modèle repose sur **une table `stock` unifiée** : le champ `partner_id` permet de distinguer le stock réel (`partner_id` = NULL) du stock virtuel (`partner_id` renseigné). Il n'y a donc pas deux tables séparées pour les deux types de stocks.

```
user                   (id, email, roles, password, last_name, first_name,
                        is_active, must_change_password, partner_id)

partner                (id, company_name, contact_details, created_at)

plant                  (id, latin_name, common_name, type)

packaging              (id, label)

season                 (id, year)

stock                  (id, plant_id, packaging_id, season_id, partner_id,
                        updated_by_id, quantity, created_at, updated_at)
                        → partner_id NULL  = stock réel (Pépi+)
                        → partner_id renseigné = stock virtuel (partenaire)

order                  (id, collaborator_id, order_number, status,
                        created_at, updated_by_id, updated_at)

order_line             (id, stock_id, purchase_order_id, quantity)

order_status_history   (id, changed_by_id, purchase_order_id, status, created_at)

reset_password_request (id, user_id, selector, hashed_token,
                        requested_at, expires_at)
```

**Relations clés :**
- Un `stock` est lié à un `plant`, un `packaging` et une `season` → garantit la traçabilité sanitaire
- Un `order_line` est lié à un `stock` avec `onDelete: SET NULL` → si un stock est supprimé, la ligne de commande est conservée avec `stock_id = NULL` (intégrité historique)
- `order_status_history` trace chaque changement de statut avec l'horodatage et l'utilisateur responsable, en cascade avec la commande (`cascade: persist`)
- Un `user` peut être lié à un `partner` (rôle PARTNER) ou avoir `partner_id` NULL (COLLABORATOR / ADMIN)
- `plant.common_name` est **obligatoire** dans l'entité Doctrine (NOT NULL, VARCHAR 255)

### 8.2 Dictionnaire de données

| Attribut | Type | Taille | Contrainte | Description |
|---|---|---|---|---|
| `user.email` | VARCHAR | 180 | UNIQUE, NOT NULL | Identifiant de connexion – contrainte `UniqueEntity` Symfony |
| `user.roles` | JSON | — | NOT NULL, DEFAULT `["ROLE_USER"]` | Tableau de rôles Symfony. Tout utilisateur hérite de `ROLE_USER`. Valeurs applicatives : `ROLE_ADMIN`, `ROLE_COLLABORATOR`, `ROLE_PARTNER` |
| `user.password` | VARCHAR | 255 | NOT NULL | Mot de passe haché bcrypt (sérialisé via CRC32C côté session) |
| `user.last_name` | VARCHAR | 255 | NOT NULL | Nom de famille de l'utilisateur |
| `user.first_name` | VARCHAR | 255 | NOT NULL | Prénom de l'utilisateur |
| `user.is_active` | TINYINT(1) | — | NOT NULL | `false` = compte désactivé sans suppression |
| `user.must_change_password` | TINYINT(1) | — | NOT NULL, DEFAULT `false` | Forcé à `true` par l'admin lors d'une réinitialisation – oblige le changement à la prochaine connexion |
| `user.partner_id` | INT | — | NULL | FK vers `partner`. NULL pour COLLABORATOR / ADMIN ; renseigné pour PARTNER |
| `partner.company_name` | VARCHAR | 255 | NOT NULL | Raison sociale du partenaire fournisseur |
| `partner.contact_details` | TEXT | — | NOT NULL | Coordonnées complètes (format libre) |
| `partner.created_at` | DATETIME | — | NOT NULL | Date d'ajout du partenaire dans le système |
| `plant.latin_name` | VARCHAR | 255 | NOT NULL | Nom scientifique obligatoire (ex. : *Sorbus torminalis*) |
| `plant.common_name` | VARCHAR | 255 | NOT NULL | Nom vernaculaire (ex. : *Alisier torminal*) – champ obligatoire dans l'entité |
| `plant.type` | VARCHAR | 255 | NOT NULL | Catégorie du plant (ex. : arbre, arbuste, vivace…) |
| `packaging.label` | VARCHAR | 255 | NOT NULL | Ex. : GF400, racine nue, pot 1L |
| `season.year` | INT | — | NOT NULL | Année de production du plant (ex. : 2024, 2025) |
| `stock.quantity` | INT | — | NOT NULL | Quantité disponible – peut être 0 à la création |
| `stock.partner_id` | INT | — | NULL | NULL = stock réel interne Pépi+ ; renseigné = stock virtuel partenaire |
| `stock.updated_by_id` | INT | — | NULL | FK vers `user` – dernier utilisateur ayant modifié ce stock |
| `stock.created_at` | DATETIME | — | NOT NULL | Date de création de l'entrée de stock |
| `stock.updated_at` | DATETIME | — | NOT NULL | Date de dernière mise à jour (décrémentation, modification…) |
| `order.order_number` | VARCHAR | 255 | NOT NULL | Numéro de commande généré : format `CMD-XXXXXXXX` (hex aléatoire) |
| `order.status` | VARCHAR | 255 | NOT NULL | Valeurs : `Réservation`, `En cours`, `Livrée`, `Annulée` |
| `order.collaborator_id` | INT | — | NULL | FK vers `user` (collaborateur créateur). Nullable : la commande subsiste si l'utilisateur est supprimé |
| `order.updated_by_id` | INT | — | NOT NULL | FK vers `user` – utilisateur ayant effectué la dernière modification (non nullable) |
| `order.created_at` | DATETIME | — | NOT NULL | Date de création de la commande |
| `order.updated_at` | DATETIME | — | NOT NULL | Date de dernière mise à jour de la commande |
| `order_line.quantity` | INT | — | NOT NULL | Quantité réservée pour cette ligne |
| `order_line.stock_id` | INT | — | NULL (`onDelete: SET NULL`) | FK vers `stock`. Mis à NULL si le stock est supprimé – conservation de la ligne pour historique |
| `order_line.purchase_order_id` | INT | — | NULL | FK vers `order` |
| `order_status_history.status` | VARCHAR | 255 | NOT NULL | Statut au moment du changement : `Réservation`, `En cours`, `Livrée`, `Annulée` |
| `order_status_history.created_at` | DATETIME | — | NOT NULL | Date et heure du changement de statut |
| `order_status_history.changed_by_id` | INT | — | NULL | FK vers `user` – auteur du changement de statut |
| `order_status_history.purchase_order_id` | INT | — | NULL | FK vers `order` |
| `reset_password_request.selector` | VARCHAR | 20 | NOT NULL | Identifiant public du token (fourni par `ResetPasswordRequestTrait`) |
| `reset_password_request.hashed_token` | VARCHAR | 100 | NOT NULL | Token haché à usage unique |
| `reset_password_request.requested_at` | DATETIME | — | NOT NULL | Date de la demande de réinitialisation |
| `reset_password_request.expires_at` | DATETIME | — | NOT NULL | Date d'expiration du token |
| `reset_password_request.user_id` | INT | — | NOT NULL | FK vers `user` – propriétaire du token |

---

## 9. Interfaces externes et intégrations

| Interface | Description |
|---|---|
| **Symfony Mailer (SMTP)** | Envoi des emails de réinitialisation de mot de passe via `SymfonyCasts ResetPassword Bundle`. Email expéditeur : `contact@pepiplus.fr` (nom : *Pépi+ Security*). Token à usage unique, stocké haché en base, avec date d'expiration. |
| **Doctrine ORM** | Abstraction complète de la base MySQL 8.0 – toutes les requêtes via les repositories Doctrine |
| **KnpPaginator Bundle** | Pagination des listes : 7 éléments/page (stocks), 10 éléments/page (commandes, packaging, saisons, users), 8 éléments/page (partenaires) |
| **Webpack Encore** | Compilation des assets front-end (TailwindCSS 4.1, JS) via `npm run build` / `npm run watch` |
| **CartService** | Service Symfony gérant le panier en session (ajout, suppression, mise à jour avec contrôle du stock disponible, vidage après validation) |
| **OrderNotificationService** | Service prévu pour l'envoi d'emails aux partenaires à la validation d'une commande *(fonctionnalité commentée dans le code, non active en v1)* |
| **GitHub** | Dépôt de source, versioning Git (`https://github.com/CASTELLS-Cyprien/PEPI-PLUS`) |

### 9.1 Tableau des routes principales

| Route | Méthode | Nom Symfony | Rôle requis | Description |
|---|---|---|---|---|
| `/login` | GET | `app_login` | Public | Page de connexion |
| `/logout` | GET | `app_logout` | Connecté | Déconnexion |
| `/reset-password` | GET/POST | `app_forgot_password_request` | Public | Demande de réinitialisation |
| `/dashboard` | GET | `app_dashboard` | Connecté | Tableau de bord (vue adaptée au rôle) |
| `/stock/global` | GET | `app_stock_index` | COLLABORATOR/ADMIN | Stock global (réel + virtuel) |
| `/stock/gestion` | GET | `app_stock_gestion_index` | COLLABORATOR/ADMIN | Stock interne uniquement |
| `/cart` | GET | `app_cart_index` | COLLABORATOR/ADMIN | Panier |
| `/add/{id}` | POST | `app_cart_add` | COLLABORATOR/ADMIN | Ajout au panier |
| `/validate` | POST | `app_cart_validate` | COLLABORATOR/ADMIN | Validation du panier → création commande |
| `/order` | GET | `app_order_index` | COLLABORATOR/ADMIN | Liste des commandes |
| `/order/show/{id}` | GET | `app_order_show` | COLLABORATOR/ADMIN | Détail commande |
| `/order/edit/{id}` | GET/POST | `app_order_edit` | COLLABORATOR/ADMIN | Modification commande |
| `/order/{id}/deliver` | POST | `app_order_deliver` | COLLABORATOR/ADMIN | Passer en `Livrée` + bascule stock |
| `/order/{id}/cancel` | POST | `app_order_cancel` | COLLABORATOR/ADMIN | Annuler + restitution stock |
| `/plant` | GET | `app_plant_index` | COLLABORATOR/ADMIN | Référentiel plants |
| `/packaging` | GET | `app_packaging_index` | COLLABORATOR/ADMIN | Conditionnements |
| `/season` | GET | `app_season_index` | COLLABORATOR/ADMIN | Saisons |
| `/partner` | GET | `app_partner_index` | ADMIN | Liste partenaires |
| `/partner/gestion/new` | GET/POST | `app_partner_new` | ADMIN | Nouveau partenaire |
| `/user` | GET | `app_user_index` | ADMIN | Liste utilisateurs |
| `/user/profile/change-password` | GET/POST | `app_user_change_password` | Connecté | Changement de mot de passe |
| `/partner/my-stock/liste` | GET | `app_partner_myStock` | PARTNER | Mon stock |
| `/partner/my-stock/new` | GET/POST | `app_partner_newMyStock` | PARTNER | Nouveau stock virtuel |
| `/partner/my-reservations/liste` | GET | `app_partner_reservations` | PARTNER | Mes réservations |

---

## 10. Glossaire et traçabilité

### 10.1 Glossaire

| Terme | Définition |
|---|---|
| **Plant** | Végétal cultivé, identifié obligatoirement par son nom latin ET son nom commun (les deux champs sont requis dans l'application) |
| **Essence** | Espèce végétale identifiée par son nom latin (plus de 100 disponibles chez Pépi+) |
| **Stock réel** | Plants produits en interne par la pépinière Pépi+, physiquement disponibles |
| **Stock virtuel** | Plants mis à disposition par un partenaire fournisseur, intégrés dans le stock global de Pépi+ |
| **Conditionnement** | Mode de présentation du plant (ex. : GF400, racine nue, pot normalisé) |
| **Saison** | Année de production d'un plant, permettant de connaître son âge au moment de la vente |
| **Réservation** | Blocage automatique d'une quantité de plants lors de la validation d'une commande |
| **Traçabilité** | Obligation sanitaire réglementaire : capacité à fournir la pépinière de provenance et la saison pour chaque plant vendu |
| **Partenaire** | Fournisseur externe de plants, disposant d'un accès restreint à l'application |
| **Référentiel** | Ensemble des données de base partagées entre Pépi+ et ses partenaires : plants, conditionnements, saisons |
| **RBAC** | Role-Based Access Control – contrôle d'accès par rôle |
| **MVC** | Model-View-Controller – pattern architectural imposé par le CdC |
| **3-tiers** | Architecture client / serveur applicatif / base de données |
| **MCD** | Modèle Conceptuel de Données |
| **MLD** | Modèle Logique de Données |

### 10.2 Matrice de traçabilité

| Besoin CdC | Exigence SF | Cas d'utilisation | Cas de test |
|---|---|---|---|
| B-01 : Accès distinct et sécurisé par collaborateur | EF-AUTH-01, EF-AUTH-03, EF-AUTH-04 | UC-01 | CT-AUTH-01, CT-AUTH-02 |
| B-02 : Réinitialisation de mot de passe | EF-AUTH-02 | UC-01 | CT-AUTH-03 |
| B-03 : Gestion des accès collaborateurs par l'admin | EF-ADMIN-01 | UC-09 | CT-ADMIN-01 |
| B-04 : Gestion du référentiel de plants | EF-REF-01 | UC-11 | CT-REF-01 |
| B-05 : Gestion des conditionnements | EF-REF-02 | UC-12 | CT-REF-02 |
| B-06 : Gestion des saisons | EF-REF-03 | UC-13 | CT-REF-03 |
| B-07 : Gestion des partenaires fournisseurs | EF-ADMIN-02 | UC-10 | CT-ADMIN-02 |
| B-08 : Visualisation ergonomique du stock complet | EF-STOCK-01, EF-STOCK-02, EF-STOCK-03, EF-STOCK-05 | UC-03, UC-04 | CT-STOCK-01, CT-STOCK-02 |
| B-09 : Gestion du stock réel par saison | EF-STOCK-06 | UC-03 | CT-STOCK-03 |
| B-10 : Tableau de bord de pilotage | EF-DASH-01, EF-DASH-02, EF-DASH-03 | UC-02 | CT-DASH-01, CT-DASH-02 |
| B-11 : Création de commande avec réservation automatique | EF-CMD-01, EF-CMD-02, EF-CMD-03 | UC-05 | CT-CMD-01, CT-CMD-02 |
| B-12 : Traçabilité sanitaire sur les commandes | EF-CMD-07, EF-STOCK-04 | UC-05, UC-03 | CT-CMD-03, CT-STOCK-04 |
| B-13 : Modification de commande avec impact stocks | EF-CMD-04 | UC-06 | CT-CMD-04 |
| B-14 : Suppression de commande avec restitution des stocks | EF-CMD-05 | UC-07 | CT-CMD-05 |
| B-15 : Accès partenaire – gestion du stock virtuel | EF-PART-01 | UC-14 | CT-PART-01 |
| B-16 : Accès partenaire – consultation des réservations | EF-PART-02 | UC-15 | CT-PART-02 |

---

## 11. Gestion et évolution du document

### 11.1 Tableau de révisions

| Version | Date | Auteur | Modifications |
|---|---|---|---|
| V0.1 | 01/12/2025 | CASTELLS Cyprien | Création initiale du document (démarrage projet) |
| V0.2 | 01/01/2026 | CASTELLS Cyprien | Ajout des cas d'utilisation, exigences fonctionnelles |
| V0.3 | 01/02/2026 | CASTELLS Cyprien | Ajout modèle de données, règles de gestion, ENF |
| V1.0 | 27/02/2026 | CASTELLS Cyprien | Version complète – prête pour livraison du 27/03/2026 |

### 11.2 Processus de validation

1. Rédaction par CASTELLS Cyprien sur la base du CdC fourni par le Pôle Sup Saint-Denis
2. Vérification de la cohérence avec le MCD/MLD (`assets/MCD.pdf`, `assets/MLD.pdf`)
3. Archivage dans le dossier `assets/` du dépôt GitHub
4. Évaluation par le jury lors de la soutenance en Avril 2026

> Ce document fait partie des livrables obligatoires définis dans le CdC : *« Les documents de spécifications fonctionnelles et techniques »*, à déposer dans le dossier livrable du dépôt Git.

---

## 12. Synthèse

| Section | Contenu | Statut |
|---|---|---|
| Page de garde | Titre, version, dates, auteur, établissement | ✅ |
| Table des matières | Navigation complète | ✅ |
| Présentation générale | Contexte Pépi+, parties prenantes, périmètre | ✅ |
| Acteurs & Cas d'utilisation | 15 UC avec fiches détaillées | ✅ |
| Exigences fonctionnelles | 27 EF avec priorités MoSCoW | ✅ |
| Maquettes / IHM | Charte graphique, sitemap, description des wireframes | ✅ |
| Règles de gestion | 14 RG liées aux EF | ✅ |
| Exigences non fonctionnelles | Performances, sécurité, dispo., compatibilité, règles de codage CdC | ✅ |
| Modèle de données | 9 entités + dictionnaire de données | ✅ |
| Interfaces externes | SMTP, Doctrine, KnpPaginator, Webpack, GitHub | ✅ |
| Glossaire | Termes métier et techniques du projet | ✅ |
| Matrice de traçabilité | 16 besoins CdC → EF → UC → Tests | ✅ |
| Gestion du document | Révisions, processus de validation, lien livrable Git | ✅ |

---

*Document réalisé dans le cadre du BTS SIO SLAM – Pôle Sup Saint-Denis – 2025/2026*
**Pépi+ © 2026 – CASTELLS Cyprien**