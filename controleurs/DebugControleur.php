<?php
require_once 'config/config.php';

class DebugControleur {
    public function testAnthropicAPI() {
        // Activer l'affichage des erreurs
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        
        // Définir la clé API
        $api_key = defined('ANTHROPIC_API_KEY') ? ANTHROPIC_API_KEY : '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';
        
        // Paramètres pour l'API
        $model = defined('ANTHROPIC_MODEL') ? ANTHROPIC_MODEL : 'claude-3-haiku-20240307';
        $question = "Bonjour, comment vas-tu?";
        $contexte = "Tu es un assistant pour une application de gestion de stock.";
        
        // Le reste du code de test...
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        $data = [
            'model' => $model,
            'system' => $contexte,
            'messages' => [
                ['role' => 'user', 'content' => $question]
            ],
            'max_tokens' => 500
        ];
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'x-api-key: ' . $api_key,
            'anthropic-version: 2023-06-01'
        ]);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Afficher les résultats dans une vue simple
        include 'vues/debug/test_api.php';
    }
}