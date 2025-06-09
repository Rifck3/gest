<?php
require_once 'modeles/Produit.php';
require_once 'modeles/Categorie.php';
require_once 'modeles/Fournisseur.php';
require_once 'modeles/Chatbot.php';

class ChatbotControleur {
    private $chatbot;
    
    public function __construct() {
        $this->chatbot = new Chatbot();
    }
    
    public function index() {
        require_once 'vues/chatbot/index.php';
    }
    
    public function traiterQuestion() {
        header('Content-Type: application/json');

        if (!isset($_GET['question'])) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Question manquante']);
            return;
        }

        $question = trim($_GET['question']);
        
        if (empty($question)) {
            http_response_code(400);
            echo json_encode(['erreur' => 'Question vide']);
            return;
        }

        try {
            // Vérifier si la clé API est configurée
            if (!defined('OPENAI_API_KEY') || empty(OPENAI_API_KEY) || OPENAI_API_KEY === 'VOTRE_CLÉ_API_ICI') {
                // Si pas de clé API, utiliser uniquement les réponses basiques
                $reponse = $this->chatbot->analyserQuestion($question);
                echo json_encode(['reponse' => $reponse, 'source' => 'local'], JSON_UNESCAPED_UNICODE);
            } else {
                // Si clé API disponible, utiliser l'IA
                $reponse = $this->chatbot->analyserQuestion($question);
                echo json_encode(['reponse' => $reponse, 'source' => 'ia'], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            error_log('Erreur Chatbot: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['erreur' => 'Erreur lors du traitement de la question']);
        }
        
        exit;
    }
}