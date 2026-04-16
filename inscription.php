<?php

try
{
    $mysqlClient = new PDO('mysql:host=localhost;dbname=caisseshop;charset=utf8', 'root', '');
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}


if (isset($_POST['inscrire']) && !empty($_POST['username']) && !empty($_POST['password'])) {

    $non_utilisateur = trim($_POST['username']);
    $motdepasse = $_POST['password'];

    // Hachage du mot de passe
    $hashedPassword = password_hash($motdepasse, PASSWORD_DEFAULT);

    // Insertion dans la base
    try {
        $stmt = $mysqlClient->prepare("INSERT INTO utilisateurs (Nom, Mot_de_passe) VALUES (?, ?)");
        $stmt->execute([$non_utilisateur, $hashedPassword]);

     
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}


?>



<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Créer un compte</h2>
<!-- form.html -->
    <form action="inscription.php" method="POST">
        <label for="username">Nom d'utilisateur :</label>
        <input type="text" name="username" id="username" required>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" required>

        <button name="inscrire" type="submit">S'inscrire</button>
    </form>
</div>

</body>
</html>

