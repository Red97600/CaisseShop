<?php
session_start();


if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}

try {
    $mysqlClient = new PDO('mysql:host=localhost;dbname=caisseshop;charset=utf8', 'root', '');
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>insert des produits</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header class="site-header">

  <div class="logo">
    <img src="logo.jpg" alt="CaisseShop">
  </div>

  <div class="header-right">
    <span class="session-info">👤 Session : <?php echo $_SESSION['utilisateur']['nom']; ?></span>
    <button class="btn-deconnexion">↩ Déconnexion</button>
  </div>

</header>

<!-- CONTENU PRINCIPAL -->
<div class="page-content">

  <div class="breadcrumb">
    <a href="produits.php">← Retour à la liste</a>
  </div>

  <!-- TITRE DE PAGE -->
  <div class="page-header">
    <div>
      <h1>ajoute un produit</h1>
    </div>
  </div>

  <!-- TABLEAU DES PRODUITS -->
  <div class="table-wrapper">
    <table>

      <thead>
        <tr>
          <th>Nom</th>
          <th>Code-barres</th>
          <th>Prix</th>
          <th>Stock</th>
          <th>Action</th>
        </tr>
      </thead>

      <tbody>

      </tbody>

    </table>

    <!-- PAGINATION -->
    <div class="pagination">
      <div class="pagination-info">
        
      </div>
    </div>

  </div><!-- /table-wrapper -->

</div><!-- /page-content -->

<!-- FOOTER -->
<footer>
  <span class="copyright">© 2024 CaisseShop — Système de Gestion de Caisse de Proximité</span>
</footer>

</body>
</html>