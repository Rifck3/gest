<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Test API Anthropic</title>
</head>
<body>
    <h2>Test d'API Claude</h2>
    <p>Envoi de la requête à l'API Anthropic Claude...</p>

    <h3>Code HTTP: <?php echo $httpcode; ?></h3>

    <?php if ($err): ?>
        <p>Erreur cURL: <?php echo htmlspecialchars($err); ?></p>
    <?php else: ?>
        <p>Réponse brute:</p>
        <pre><?php echo htmlspecialchars($response); ?></pre>
        
        <?php 
        $response_data = json_decode($response, true);
        echo "<p>Réponse décodée:</p>";
        echo "<pre>" . htmlspecialchars(print_r($response_data, true)) . "</pre>";
        
        if (isset($response_data['content'][0]['text'])) {
            echo "<p>Réponse finale: " . htmlspecialchars($response_data['content'][0]['text']) . "</p>";
        }
        ?>
    <?php endif; ?>
</body>
</html>