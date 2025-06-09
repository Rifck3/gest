<?php
// Désactiver l'affichage des erreurs
error_reporting(0);
ini_set('display_errors', 0);

// Fonction pour vérifier et corriger les permissions
function checkAndFixPermissions($path) {
    $results = [];
    
    // Vérifier si le chemin existe
    if (!file_exists($path)) {
        return ["error" => "Le chemin n'existe pas"];
    }
    
    // Parcourir tous les fichiers et dossiers
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $item) {
        $path = $item->getPathname();
        $isDir = $item->isDir();
        $currentPerms = substr(sprintf('%o', fileperms($path)), -4);
        $targetPerms = $isDir ? '0755' : '0644';
        
        // Vérifier si les permissions sont correctes
        if ($currentPerms !== $targetPerms) {
            // Tenter de corriger les permissions
            if (chmod($path, octdec($targetPerms))) {
                $results[] = [
                    'path' => $path,
                    'type' => $isDir ? 'Dossier' : 'Fichier',
                    'old_perms' => $currentPerms,
                    'new_perms' => $targetPerms,
                    'status' => 'Corrigé'
                ];
            } else {
                $results[] = [
                    'path' => $path,
                    'type' => $isDir ? 'Dossier' : 'Fichier',
                    'old_perms' => $currentPerms,
                    'new_perms' => $targetPerms,
                    'status' => 'Erreur de correction'
                ];
            }
        }
    }
    
    return $results;
}

// Vérifier les permissions
$results = checkAndFixPermissions(__DIR__);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification des Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Vérification des Permissions des Fichiers</h4>
            </div>
            <div class="card-body">
                <?php if (isset($results['error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $results['error']; ?>
                    </div>
                <?php else: ?>
                    <?php if (empty($results)): ?>
                        <div class="alert alert-success">
                            Toutes les permissions sont correctement configurées !
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Chemin</th>
                                        <th>Type</th>
                                        <th>Anciennes Permissions</th>
                                        <th>Nouvelles Permissions</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($results as $result): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($result['path']); ?></td>
                                            <td><?php echo $result['type']; ?></td>
                                            <td><?php echo $result['old_perms']; ?></td>
                                            <td><?php echo $result['new_perms']; ?></td>
                                            <td>
                                                <?php if ($result['status'] === 'Corrigé'): ?>
                                                    <span class="badge bg-success">Corrigé</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Erreur</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="mt-3">
                    <a href="index.php" class="btn btn-primary">Retour à l'accueil</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 