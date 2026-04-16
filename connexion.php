<?php
session_start();


try {
    $mysqlClient = new PDO('mysql:host=localhost;dbname=caisseshop;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Vérifie si le formulaire est soumit et que les champs sont remplis

if (!empty($_POST['nom']) && !empty($_POST['password'])) {

  $nom_utilisateur = trim($_POST['nom']);
  $mot_de_passe = trim($_POST['password']);

  $sqlQuery = "SELECT Nom, Mot_de_passe FROM utilisateurs WHERE Nom = :Nom";
  $selectprs = $mysqlClient->prepare($sqlQuery);
  $selectprs->execute(['Nom' => $nom_utilisateur]);
  $utilisateur = $selectprs->fetch(PDO::FETCH_ASSOC);

  if ($utilisateur && password_verify($mot_de_passe, $utilisateur['Mot_de_passe'])) {

      $_SESSION['utilisateur'] = [
         'nom' => $utilisateur['Nom'],
      ];

      header('Location: caisse.php');
      exit;

  } else {
     $message = "Nom d'utilisateur ou mot de passe incorrect."; 
  }
}




?>



<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CaisseShop — Connexion</title>
  <link rel="stylesheet" href="style.css" />
</head>

<body class="page-connexion">

  <div class="login-box">

    <img src="logo.jpg" alt="CaisseShop" class="logo" />

    <h2>Bienvenue sur CaisseShop</h2>
    <p>Veuillez vous identifier pour accéder à votre caisse.</p>

    <!-- Formulaire login par nom uniquement -->
    <form method="POST" action="" class="product-form">

      <div class="form-field">
        <label for="nom">Nom d'utilisateur</label>
        <input type="text" id="nom"  name="nom" placeholder="ex: marie.bertrand" required />
        <input type="password" name="password" placeholder="Mot de passe" required />
      </div>

      <button type="submit" class="btn-primary">Se connecter</button>

    </form>
    <p style="color:red; text-align:center;">
      <?php echo $message ?? ''; ?>
    </p>

    <p class="small text-muted" style="margin-top: 16px; text-align:center;">
      Besoin d'aide pour accéder à votre terminal ? Contactez
      votre administrateur système ou le support technique de <strong>CaisseShop</strong>.
    </p>

  </div>

  <footer>
    <p class="copyright">© 2024 CaisseShop — Système de Gestion de Caisse de Proximité</p>
  </footer>

</body>
</html>