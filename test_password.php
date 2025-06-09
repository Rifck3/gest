<?php
// Test de vérification de mot de passe
$password = 'admin123';
$hash = '$2y$10$i5Sx1lTwCMefcvdIo85ele/mUIL32e2h9TUCZVc4kqnvbdA3CXPE2';

echo "Mot de passe: " . $password . "<br>";
echo "Hash: " . $hash . "<br>";
echo "Vérification: " . (password_verify($password, $hash) ? "SUCCÈS" : "ÉCHEC") . "<br>";
?>
