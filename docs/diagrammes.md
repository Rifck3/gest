# Diagrammes du Projet de Gestion de Stock

## 1. Diagramme de Cas d'Utilisation (Use Case)

```mermaid
graph TD
    A1[Admin] --> B[Gestion des Stocks]
    A1 --> C[Gestion des Commandes]
    A1 --> D[Gestion des Fournisseurs]
    A1 --> E[Gestion des Rapports]
    A1 --> F[Gestion des Utilisateurs]
    
    A2[Employé] --> B
    A2 --> C
    A2 --> D
    A2 --> E
    
    B --> B1[Consulter Stock]
    B --> B2[Ajouter Produit]
    B --> B3[Modifier Produit]
    B --> B4[Supprimer Produit]
    B --> B5[Gérer Catégories]
    
    C --> C1[Créer Commande]
    C --> C2[Suivre Commande]
    C --> C3[Valider Commande]
    C --> C4[Annuler Commande]
    
    D --> D1[Ajouter Fournisseur]
    D --> D2[Modifier Fournisseur]
    D --> D3[Consulter Fournisseur]
    D --> D4[Évaluer Fournisseur]
    
    E --> E1[Générer Rapport Stock]
    E --> E2[Générer Rapport Commandes]
    E --> E3[Générer Rapport Fournisseurs]
    E --> E4[Analyser Tendances]
    
    F --> F1[Créer Utilisateur]
    F --> F2[Modifier Utilisateur]
    F --> F3[Gérer Rôles]
    F --> F4[Gérer Permissions]
```

## 2. Diagramme de Classes

```mermaid
classDiagram
    class Utilisateur {
        +int id
        +string nom_utilisateur
        +string mot_de_passe
        +string nom_complet
        +string email
        +string role
        +bool actif
        +datetime derniere_connexion
        +creer()
        +modifier()
        +supprimer()
    }
    
    class Produit {
        +int id
        +string nom
        +string description
        +decimal prix_unitaire
        +int quantite
        +int quantite_min
        +ajouterStock()
        +retirerStock()
        +verifierStock()
    }
    
    class Categorie {
        +int id
        +string nom
        +string description
        +ajouter()
        +modifier()
        +supprimer()
    }
    
    class Fournisseur {
        +int id
        +string nom
        +string personne_contact
        +string telephone
        +string email
        +string adresse
        +ajouter()
        +modifier()
        +supprimer()
    }
    
    class Commande {
        +int id
        +string reference
        +datetime date_commande
        +string statut
        +decimal montant_total
        +creer()
        +valider()
        +annuler()
        +ajouterProduit()
    }
    
    class MouvementStock {
        +int id
        +string type_mouvement
        +int quantite
        +datetime date_mouvement
        +enregistrer()
        +annuler()
    }
    
    Utilisateur "1" -- "0..*" Commande
    Produit "1" -- "0..*" MouvementStock
    Categorie "1" -- "0..*" Produit
    Fournisseur "1" -- "0..*" Commande
    Commande "1" -- "1..*" Produit
```

## 3. Diagrammes de Séquence

### 3.1 Diagramme de Séquence : Création de Commande

```mermaid
sequenceDiagram
    participant U as Utilisateur
    participant S as Système
    participant B as Base de Données
    
    U->>S: Demande création commande
    S->>B: Vérifie stock disponible
    B-->>S: Retourne état stock
    S->>S: Calcule montant total
    S->>B: Enregistre commande
    B-->>S: Confirme enregistrement
    S-->>U: Affiche confirmation
```

### 3.2 Diagramme de Séquence : Authentification Utilisateur

```mermaid
sequenceDiagram
    participant U as Utilisateur
    participant S as Système
    participant B as Base de Données
    
    U->>S: Saisit identifiants (login/password)
    S->>B: Vérifie identifiants
    alt Identifiants valides
        B-->>S: Authentification réussie
        S->>B: Enregistre connexion
        S-->>U: Accès au tableau de bord
    else Identifiants invalides
        B-->>S: Authentification échouée
        S-->>U: Affiche message d'erreur
    end
```

### 3.3 Diagramme de Séquence : Ajout de Produit en Stock

```mermaid
sequenceDiagram
    participant A as Admin/Employé
    participant S as Système
    participant B as Base de Données
    
    A->>S: Demande ajout produit
    S-->>A: Affiche formulaire
    A->>S: Saisit informations produit
    S->>S: Valide données
    alt Données valides
        S->>B: Enregistre produit
        B-->>S: Confirmation
        S->>B: Crée mouvement stock (entrée)
        B-->>S: Confirmation
        S-->>A: Notification succès
    else Données invalides
        S-->>A: Affiche erreurs validation
    end
```

### 3.4 Diagramme de Séquence : Génération de Rapport

```mermaid
sequenceDiagram
    participant U as Utilisateur
    participant S as Système
    participant B as Base de Données
    
    U->>S: Demande génération rapport
    S-->>U: Affiche options de rapport
    U->>S: Sélectionne paramètres
    S->>B: Récupère données
    B-->>S: Retourne données
    S->>S: Traite et formate données
    S-->>U: Affiche rapport
    opt Exporter
        U->>S: Demande export (PDF/Excel)
        S->>S: Génère fichier
        S-->>U: Téléchargement fichier
    end
```

### 3.5 Diagramme de Séquence : Gestion des Alertes de Stock

```mermaid
sequenceDiagram
    participant S as Système
    participant B as Base de Données
    participant U as Utilisateur
    
    S->>B: Vérifie niveaux de stock
    B-->>S: Retourne produits sous seuil
    alt Produits sous seuil détectés
        S->>S: Génère alertes
        S->>U: Notifie utilisateur
        U->>S: Consulte alertes
        U->>S: Crée commande réapprovisionnement
    else Aucun produit sous seuil
        S->>S: Continue surveillance
    end
```

## 4. Diagramme d'État (Commande)

```mermaid
stateDiagram-v2
    [*] --> En_Attente
    En_Attente --> Validée: Validation
    En_Attente --> Annulée: Annulation
    Validée --> Livrée: Livraison
    Validée --> Annulée: Annulation
    Livrée --> [*]
    Annulée --> [*]
```

## 5. Diagramme d'Activité (Processus de Gestion de Stock)

```mermaid
graph TD
    A[Début] --> B{Vérifier Stock}
    B -->|Stock Bas| C[Générer Alerte]
    B -->|Stock Normal| D[Continuer Surveillance]
    C --> E[Créer Commande]
    E --> F[Valider Commande]
    F --> G[Recevoir Marchandise]
    G --> H[Mettre à Jour Stock]
    H --> B
    D --> B
```

## 6. Diagramme de Déploiement

```mermaid
graph TD
    subgraph Client
        A[Navigateur Web]
    end
    
    subgraph Serveur
        B[Apache]
        C[PHP]
        D[MySQL]
    end
    
    A --> B
    B --> C
    C --> D
```

## 7. Diagramme de Composants

```mermaid
graph TD
    subgraph Frontend
        A[Interface Utilisateur]
        B[Contrôleurs]
        C[Vues]
    end
    
    subgraph Backend
        D[Modèles]
        E[Services]
        F[API]
    end
    
    subgraph Base de Données
        G[MySQL]
    end
    
    A --> B
    B --> C
    B --> D
    D --> E
    E --> F
    F --> G
```

## Explications Détaillées des Diagrammes

### 1. Diagramme de Cas d'Utilisation
Ce diagramme présente les interactions entre les utilisateurs (acteurs) et le système. Il y a deux acteurs principaux :
- **Admin** : A accès à toutes les fonctionnalités du système, y compris la gestion des utilisateurs
- **Employé** : A accès aux opérations quotidiennes mais pas aux fonctionnalités administratives

Les fonctionnalités sont regroupées en 5 grands modules :
- **Gestion des Stocks** : Permet la gestion complète de l'inventaire
- **Gestion des Commandes** : Gère le cycle de vie des commandes
- **Gestion des Fournisseurs** : Centralise les informations sur les fournisseurs
- **Gestion des Rapports** : Génère des statistiques et analyses
- **Gestion des Utilisateurs** : Accessible uniquement par l'administrateur pour gérer les accès

### 2. Diagramme de Classes
Ce diagramme représente la structure de données du système et montre comment les différentes entités sont liées entre elles :

- **Utilisateur** : Stocke les informations sur les utilisateurs du système
  * Relations : Un utilisateur peut créer plusieurs commandes (1 à 0..*)
  
- **Produit** : Contient les informations sur les articles en stock
  * Relations : Un produit peut avoir plusieurs mouvements de stock (1 à 0..*)
  * Un produit appartient à une catégorie (n à 1)
  
- **Catégorie** : Permet de classifier les produits
  * Relations : Une catégorie peut contenir plusieurs produits (1 à 0..*)
  
- **Fournisseur** : Stocke les informations sur les fournisseurs
  * Relations : Un fournisseur peut être associé à plusieurs commandes (1 à 0..*)
  
- **Commande** : Représente une commande d'achat
  * Relations : Une commande concerne un ou plusieurs produits (1 à 1..*)
  
- **MouvementStock** : Enregistre chaque entrée ou sortie de stock
  * Relations : Un mouvement concerne un produit spécifique (n à 1)

### 3. Diagrammes de Séquence
Ce diagramme montre la séquence d'opérations lors de la création d'une commande :

1. L'utilisateur initie une demande de création de commande
2. Le système vérifie la disponibilité des produits dans la base de données
3. La base de données retourne l'état du stock
4. Le système calcule le montant total de la commande
5. Le système enregistre la commande dans la base de données
6. La base de données confirme l'enregistrement
7. Le système affiche une confirmation à l'utilisateur

Ce flux illustre l'interaction entre les trois composants principaux : l'utilisateur, le système applicatif, et la base de données.

### 4. Diagramme d'État
Ce diagramme montre les différents états possibles d'une commande et les transitions entre ces états :

- **État initial** → **En Attente** : Une commande commence toujours en attente
- **En Attente** → **Validée** : La commande est approuvée
- **En Attente** → **Annulée** : La commande est rejetée
- **Validée** → **Livrée** : Les produits ont été reçus
- **Validée** → **Annulée** : La commande validée est annulée
- **Livrée** → **État final** : Cycle complet de la commande
- **Annulée** → **État final** : Fin du processus

Ce diagramme est essentiel pour suivre le cycle de vie d'une commande et implémenter les règles de transition appropriées.

### 5. Diagramme d'Activité
Ce diagramme décrit le processus de gestion de stock :

1. Début du processus
2. Vérification du niveau de stock
3. Si le stock est bas, une alerte est générée, menant à la création et validation d'une commande
4. Après réception de la marchandise, le stock est mis à jour
5. Si le stock est normal, la surveillance continue
6. Le processus boucle constamment pour maintenir un niveau de stock optimal

Ce diagramme permet de comprendre le flux de travail complet pour gérer efficacement l'inventaire.

### 6. Diagramme de Déploiement
Ce diagramme illustre l'architecture physique du système :

- **Côté Client** : Navigateur Web qui accède à l'application
- **Côté Serveur** : 
  * Apache comme serveur web pour traiter les requêtes HTTP
  * PHP pour exécuter la logique métier
  * MySQL pour stocker et gérer les données

Cette architecture trois-tiers est courante pour les applications web modernes, séparant la présentation, la logique métier et le stockage des données.

### 7. Diagramme de Composants
Ce diagramme détaille l'organisation logicielle du système :

- **Frontend** : 
  * Interface Utilisateur (UI)
  * Contrôleurs pour gérer les interactions
  * Vues pour l'affichage
  
- **Backend** :
  * Modèles pour la logique métier
  * Services pour traiter les opérations complexes
  * API pour les communications entre composants
  
- **Base de Données** :
  * MySQL pour le stockage persistent des données

Ce diagramme montre comment les différents modules logiciels interagissent pour former un système cohérent basé sur le pattern MVC (Modèle-Vue-Contrôleur). 