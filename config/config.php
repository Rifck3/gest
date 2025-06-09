<?php
/**
 * Fichier de configuration de l'application
 * 
 * Ce fichier contient les configurations générales de l'application
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_stock');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Configuration de l'application
define('APP_NAME', 'Gestion de Stock');
define('APP_VERSION', '1.0.0');
define('DEBUG_MODE', true); // Gardez à true pour détecter les erreurs sur l'hébergement

// Configuration des chemins
define('BASE_URL', '/'); // Modifié pour la racine sur l'hébergement
define('ROOT_PATH', dirname(__DIR__) . '/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');

// Configuration de l'IA
define('USE_ANTHROPIC', true);
define('ANTHROPIC_API_KEY', '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');
define('ANTHROPIC_MODEL', 'claude-3-haiku-20240307'); // Utiliser le modèle Claude le plus abordable

// Configuration des emails (pour les notifications)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'user@example.com');
define('SMTP_PASSWORD', 'password');
define('MAIL_FROM', 'noreply@example.com');
define('MAIL_FROM_NAME', 'Système de Gestion de Stock');

// Configuration du système
define('SESSION_LIFETIME', 3600); // Durée de session en secondes (1 heure)
define('MAX_LOGIN_ATTEMPTS', 5); // Nombre maximum de tentatives de connexion
define('PASSWORD_RESET_EXPIRY', 86400); // Expiration du lien de réinitialisation du mot de passe (24 heures)

// Configuration de sécurité
define('HASH_SALT', 'Vo8!vgS#9^2pLmK&'); // Salt pour les hachages (modifiez cette valeur) 