# Système de Gestion de Stock avec Assistant IA

Ce système de gestion de stock intègre un assistant intelligent basé sur l'IA pour aider les utilisateurs à gérer leur inventaire de manière plus efficace.

## Nouvelles fonctionnalités avec l'IA

L'assistant IA peut :
- Répondre aux questions sur l'état du stock
- Fournir des informations sur les produits, catégories et fournisseurs
- Donner des conseils sur l'optimisation de la gestion du stock
- Analyser les tendances et faire des recommandations
- Répondre à des questions complexes grâce à l'intégration avec l'API OpenAI

## Configuration de l'IA

Pour activer l'assistant IA avancé, suivez ces étapes :

1. Obtenez une clé API OpenAI sur [https://platform.openai.com/](https://platform.openai.com/)
2. Ouvrez le fichier `config/config.php`
3. Modifiez la ligne suivante en remplaçant par votre clé API :
   ```php
   define('OPENAI_API_KEY', 'VOTRE_CLÉ_API_ICI'); 
   ```
4. Vous pouvez également modifier le modèle utilisé (par défaut GPT-3.5-turbo) :
   ```php
   define('OPENAI_MODEL', 'gpt-3.5-turbo');
   ```

Si la clé API n'est pas configurée, l'assistant fonctionnera en mode basique, en utilisant uniquement les réponses préprogrammées.

## Utilisation de l'Assistant IA

1. Accédez à l'assistant via le menu latéral en cliquant sur "Assistant"
2. Posez vos questions en langage naturel
3. L'assistant vous répondra en fonction des données de votre système de gestion de stock

## Exemples de questions

- "Quel est le stock actuel de [nom du produit] ?"
- "Quels produits sont en stock faible ?"
- "Quelle est la valeur totale de mon stock ?"
- "Qui est le fournisseur de [nom du produit] ?"
- "Comment optimiser la gestion de mon stock ?"
- "Quels sont les produits les plus vendus ?"

## Sécurité

Attention : La clé API OpenAI est une information sensible. Ne la partagez pas et assurez-vous que le fichier de configuration n'est pas accessible publiquement.

## Limitations

L'assistant IA utilise les données de votre système mais peut également générer des réponses basées sur sa connaissance générale. Il est recommandé de vérifier les informations critiques dans l'interface principale de gestion.

## Informations techniques

- L'API utilise le modèle GPT pour générer des réponses intelligentes
- Les statistiques du système sont fournies comme contexte à l'IA
- Les requêtes et réponses sont sécurisées via HTTPS
- La consommation de l'API est optimisée pour minimiser les coûts

## Fonctionnalités

- Gestion des produits (ajout, modification, suppression)
- Gestion des mouvements de stock (entrées/sorties)
- Gestion des fournisseurs
- Suivi des stocks
- Journal d'activités
- Interface utilisateur intuitive

## Installation

### Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)

### Installation locale

1. Clonez ce dépôt dans votre répertoire web
2. Importez le fichier `database.sql` dans votre base de données MySQL
3. Configurez les paramètres de connexion dans `config/database.php`
4. Accédez à l'application via votre navigateur

### Déploiement sur InfinityFree

1. Créez un compte sur [InfinityFree](https://app.infinityfree.net/register)
2. Créez un nouveau site web
3. Notez les informations de connexion à la base de données fournies
4. Modifiez le fichier `config/database.php` avec les nouvelles informations
5. Importez le fichier `database.sql` via phpMyAdmin
6. Téléversez tous les fichiers via FTP ou le gestionnaire de fichiers

## Identifiants par défaut

- Email: admin@admin.com
- Mot de passe: admin123

## Structure des dossiers

```
├── assets/         # Fichiers CSS, JS et images
├── config/         # Fichiers de configuration
├── controleurs/    # Contrôleurs de l'application
├── modeles/        # Modèles de données
├── vues/          # Templates et vues
└── index.php      # Point d'entrée de l'application
```

## Support

Pour toute question ou problème, veuillez contacter l'administrateur du système. 