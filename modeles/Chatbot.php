<?php
require_once 'config/database.php';
require_once 'config/config.php';  // Inclure le fichier de configuration
require_once 'Produit.php';
require_once 'Categorie.php';
require_once 'Fournisseur.php';

class Chatbot {
    private $db;
    private $produit;
    private $categorie;
    private $fournisseur;
    private $api_key; // Clé API pour l'IA
    private $use_anthropic = false;

    public function __construct() {
        global $conn;
        $this->db = $conn;
        $this->produit = new Produit($conn);
        $this->categorie = new Categorie($conn);
        $this->fournisseur = new Fournisseur($conn);
        
        // Vérifier si on utilise Anthropic ou OpenAI
        if (defined('USE_ANTHROPIC') && USE_ANTHROPIC && defined('ANTHROPIC_API_KEY')) {
            $this->api_key = ANTHROPIC_API_KEY;
            $this->use_anthropic = true;
        } else if (defined('OPENAI_API_KEY')) {
            $this->api_key = OPENAI_API_KEY;
        }
    }
    
    /**
     * Analyse une question et retourne une réponse
     */
    public function analyserQuestion($question) {
        $question = strtolower(trim($question));
        $reponse = "Désolé, je n'ai pas compris votre question. Vous pouvez me demander des informations sur le stock, la catégorie ou le fournisseur d'un produit.";

        try {
            // Vérifier d'abord si on peut répondre avec notre logique interne
            if (strpos($question, 'stock') !== false || strpos($question, 'quantité') !== false) {
                if (preg_match('/(?:stock|quantité).*?(?:de|du|des|pour|sur)\s+(.+)$/i', $question, $matches)) {
                    $produit = trim($matches[1]);
                    $reponse = $this->getStockProduit($produit);
                    return $reponse;
                }
            } elseif (strpos($question, 'catégorie') !== false || strpos($question, 'categorie') !== false) {
                if (preg_match('/(?:catégorie|categorie).*?(?:de|du|des|pour|sur)\s+(.+)$/i', $question, $matches)) {
                    $produit = trim($matches[1]);
                    $reponse = $this->getCategorieProduit($produit);
                    return $reponse;
                }
            } elseif (strpos($question, 'fournisseur') !== false) {
                if (preg_match('/fournisseur.*?(?:de|du|des|pour|sur)\s+(.+)$/i', $question, $matches)) {
                    $produit = trim($matches[1]);
                    $reponse = $this->getFournisseurProduit($produit);
                    return $reponse;
                }
            }
            
            // Si nous n'avons pas pu répondre avec notre logique interne, utilisons l'IA
            if ($this->api_key) {
                $reponseIA = $this->obtenirReponseIA($question);
                if ($reponseIA) {
                    return $reponseIA;
                }
            }
            
            // Utiliser nos méthodes de réponse existantes comme fallback
            if (strpos($question, 'stock') !== false) {
                $reponse = $this->repondreStock($question);
            } elseif (strpos($question, 'produit') !== false) {
                $reponse = $this->repondreProduit($question);
            } elseif (strpos($question, 'catégorie') !== false || strpos($question, 'categorie') !== false) {
                $reponse = $this->repondreCategorie($question);
            } elseif (strpos($question, 'fournisseur') !== false) {
                $reponse = $this->repondreFournisseur($question);
            }
        } catch (Exception $e) {
            $reponse = "Une erreur est survenue lors du traitement de votre question. Veuillez réessayer.";
        }

        return $reponse;
    }
    
    /**
     * Interroge l'API GPT pour obtenir une réponse
     */
    private function obtenirReponseIA($question) {
        if (empty($this->api_key)) {
            return null;
        }
        
        try {
            $stats = $this->obtenirStatsGlobales();
            $contexte = "Tu es un assistant de gestion de stock pour une entreprise. 
            Voici quelques informations sur notre inventaire actuel :
            - Nombre total de produits: {$stats['total_produits']}
            - Valeur totale du stock: {$stats['valeur_totale']} Fr
            - Nombre de produits en stock faible: {$stats['produits_stock_faible']}
            - Nombre de produits en rupture: {$stats['produits_rupture']}
            - Nombre de catégories: {$stats['total_categories']}
            - Nombre de fournisseurs: {$stats['total_fournisseurs']}
            
            Si tu ne connais pas la réponse, sois honnête et dis simplement que tu n'as pas cette information.
            Réponds en français de manière concise et professionnelle.";
            
            if ($this->use_anthropic) {
                // Utiliser l'API Anthropic Claude
                $ch = curl_init('https://api.anthropic.com/v1/messages');
                $model = defined('ANTHROPIC_MODEL') ? ANTHROPIC_MODEL : 'claude-3-haiku-20240307';
                
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
                    'x-api-key: ' . $this->api_key,
                    'anthropic-version: 2023-06-01'
                ]);
                
                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                
                if ($err) {
                    error_log("Erreur cURL Anthropic: " . $err);
                    return null;
                }
                
                $response_data = json_decode($response, true);
                
                if (isset($response_data['content'][0]['text'])) {
                    return $response_data['content'][0]['text'];
                }
            } else {
                // Code existant pour OpenAI
            }
        } catch (Exception $e) {
            error_log("Erreur API IA: " . $e->getMessage());
        }
        
        return null;
    }
    
    /**
     * Obtient les statistiques globales pour le contexte de l'IA
     */
    private function obtenirStatsGlobales() {
        $stats = [
            'total_produits' => 0,
            'valeur_totale' => 0,
            'produits_stock_faible' => 0,
            'produits_rupture' => 0,
            'total_categories' => 0,
            'total_fournisseurs' => 0
        ];
        
        try {
            // Nombre total de produits
            $query = "SELECT COUNT(*) as total FROM produits";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_produits'] = $result['total'];
            
            // Valeur totale du stock
            $query = "SELECT SUM(quantite * prix_unitaire) as valeur FROM produits";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['valeur_totale'] = number_format($result['valeur'] ?? 0, 2, ',', ' ');
            
            // Produits en stock faible
            $query = "SELECT COUNT(*) as total FROM produits WHERE quantite <= quantite_min AND quantite > 0";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['produits_stock_faible'] = $result['total'];
            
            // Produits en rupture
            $query = "SELECT COUNT(*) as total FROM produits WHERE quantite <= 0";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['produits_rupture'] = $result['total'];
            
            // Nombre de catégories
            $query = "SELECT COUNT(*) as total FROM categories";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_categories'] = $result['total'];
            
            // Nombre de fournisseurs
            $query = "SELECT COUNT(*) as total FROM fournisseurs";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_fournisseurs'] = $result['total'];
        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des statistiques: " . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Obtient les informations de stock d'un produit spécifique
     */
    private function getStockProduit($nomProduit) {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id WHERE p.nom LIKE ?");
        $stmt->execute(['%' . $nomProduit . '%']);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit) {
            $statut = "en stock";
            if ($produit['quantite'] <= 0) {
                $statut = "en rupture de stock";
            } elseif ($produit['quantite'] <= $produit['quantite_min']) {
                $statut = "en stock faible";
            }
            
            return "Le produit '{$produit['nom']}' de la catégorie '{$produit['categorie_nom']}' a une quantité de {$produit['quantite']} unités (minimum recommandé: {$produit['quantite_min']}). Ce produit est actuellement {$statut}.";
        }
        
        return "Je n'ai pas trouvé de produit correspondant à '{$nomProduit}'.";
    }
    
    /**
     * Obtient la catégorie d'un produit spécifique
     */
    private function getCategorieProduit($nomProduit) {
        $stmt = $this->db->prepare("SELECT p.*, c.nom as categorie_nom FROM produits p LEFT JOIN categories c ON p.categorie_id = c.id WHERE p.nom LIKE ?");
        $stmt->execute(['%' . $nomProduit . '%']);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit) {
            return "Le produit '{$produit['nom']}' appartient à la catégorie '{$produit['categorie_nom']}'.";
        }
        
        return "Je n'ai pas trouvé de produit correspondant à '{$nomProduit}'.";
    }
    
    /**
     * Obtient le fournisseur d'un produit spécifique
     */
    private function getFournisseurProduit($nomProduit) {
        $stmt = $this->db->prepare("SELECT p.*, f.nom as fournisseur_nom FROM produits p LEFT JOIN fournisseurs f ON p.fournisseur_id = f.id WHERE p.nom LIKE ?");
        $stmt->execute(['%' . $nomProduit . '%']);
        $produit = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($produit) {
            return "Le produit '{$produit['nom']}' est fourni par '{$produit['fournisseur_nom']}'.";
        }
        
        return "Je n'ai pas trouvé de produit correspondant à '{$nomProduit}'.";
    }
    
    /**
     * Répond aux questions sur le stock
     */
    private function repondreStock($question) {
        if (preg_match('/stock de (.+)/', $question, $matches)) {
            $nomProduit = trim($matches[1]);
            $produit = $this->produit->rechercher($nomProduit)->fetch(PDO::FETCH_ASSOC);
            
            if ($produit) {
                return "Le produit " . htmlspecialchars($produit['nom']) . " a une quantité de " . 
                       htmlspecialchars($produit['quantite']) . ". La quantité minimale recommandée est " . 
                       htmlspecialchars($produit['quantite_min']);
            } else {
                return "Je n'ai pas trouvé de produit correspondant à : " . htmlspecialchars($nomProduit);
            }
        }
        
        if (strpos($question, 'stock faible') !== false) {
            $produitsFaibles = $this->produit->obtenirStockFaible();
            if (count($produitsFaibles) > 0) {
                $liste = "";
                foreach ($produitsFaibles as $produit) {
                    $liste .= htmlspecialchars($produit['nom']) . " (" . 
                             htmlspecialchars($produit['quantite']) . " pièces), ";
                }
                return "Les produits en stock faible sont : " . rtrim($liste, ", ");
            } else {
                return "Tous les produits ont un stock suffisant.";
            }
        }
        
        return "Je ne comprends pas votre question sur le stock. Essayez par exemple :
        - Quel est le stock de produit X ?
        - Quels sont les produits en stock faible ?";
    }
    
    /**
     * Répond aux questions sur les produits
     */
    private function repondreProduit($question) {
        if (preg_match('/nombre de produits/', $question)) {
            $nombre = $this->produit->compter();
            return "Il y a actuellement " . $nombre . " produits dans le stock.";
        }
        
        if (preg_match('/valeur du stock/', $question)) {
            $valeur = $this->produit->calculerValeurStock();
            return "La valeur totale du stock est de " . number_format($valeur, 2) . "Fr";
        }
        
        return "Je ne comprends pas votre question sur les produits. Essayez par exemple :
        - Combien avons-nous de produits ?
        - Quelle est la valeur du stock ?";
    }
    
    /**
     * Répond aux questions sur les catégories
     */
    private function repondreCategorie($question) {
        if (preg_match('/nombre de catégories/', $question)) {
            $query = "SELECT COUNT(*) as nombre FROM categories";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return "Il y a actuellement " . $result['nombre'] . " catégories.";
        }
        
        return "Je ne comprends pas votre question sur les catégories. Essayez par exemple :
        - Combien avons-nous de catégories ?";
    }
    
    /**
     * Répond aux questions sur les fournisseurs
     */
    private function repondreFournisseur($question) {
        if (preg_match('/nombre de fournisseurs/', $question)) {
            $query = "SELECT COUNT(*) as nombre FROM fournisseurs";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return "Il y a actuellement " . $result['nombre'] . " fournisseurs.";
        }
        
        return "Je ne comprends pas votre question sur les fournisseurs. Essayez par exemple :
        - Combien avons-nous de fournisseurs ?";
    }
}