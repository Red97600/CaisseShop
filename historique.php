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




$ventes = $mysqlClient->prepare(
"SELECT 
v.Date_creation,u.Nom,
SUM(dv.Quantite) AS total_quantite,
SUM(dv.Total_ligne) AS total_argent
FROM ventes v
JOIN utilisateurs u ON v.Utilisateur_id = u.Id
JOIN details_vente dv ON dv.Vente_id = v.Id
GROUP BY v.Id, v.Date_creation, u.Nom
");

$ventes->execute();
$affiche = $ventes->fetchAll();


?>


<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Historique</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header class="site-header">
  <div class="logo">
    <img src="logo.jpg" alt="Logo">
  </div>
  <nav class="menu">
    <a href="caisse.php">🛒 Caisse</a>
    <a href="produits.php">📦 Produits</a>
    <a href="historique.php" class="active">🧾 Ventes</a>
  </nav>
  <div class="header-right">
    <span class="session-info">👤 Session: <?php echo $_SESSION['utilisateur']['nom']; ?></span>
    <a href="caisse.php" class="btn-deconnexion">↩ Déconnexion</a>
  </div>
</header>

<!-- CONTENU -->
<div class="page-content">

  <!-- HEADER PAGE -->
  <div class="page-header">
    <div>
      <h1>Historique des Ventes</h1>
      <p>Consultez et gérez l'ensemble des transactions passées.</p>
    </div>
  </div>

  <!-- FILTRES + PÉRIODE -->
  <div class="toolbar">
    <div class="toolbar-left">
      <button class="btn-secondary">Aujourd'hui</button>
      <button class="btn-secondary">Cette semaine</button>
      <button class="btn-orange">Tout</button>
    </div>
    <div class="toolbar-right">
      <span class="text-muted small">📅 Période : Toutes les transactions</span>
    </div>
  </div>

  <!-- TABLEAU -->
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>DATE</th>
          <th>UTILISATEUR</th>
          <th>ARTICLES</th>
          <th>MONTANT</th>
        </tr>
      </thead>
      <tbody>
          <?php
           $Total = 0;
            for($i=0; $i <count($affiche); $i++) {
              echo '<tr>';
              echo '<td>' . $affiche[$i]['Date_creation'] . '</td>';
              echo '<td>' . $affiche[$i]['Nom'] . '</td>';
              echo '<td>' . $affiche[$i]['total_quantite'] . '</td>';
              echo '<td>' . $affiche[$i]['total_argent'] . ' €</td>';
              echo '</tr>';
              $Total += $affiche[$i]['total_argent'];
            };
          ?>
      </tbody>
    </table>

    <!-- TOTAL -->
    <div class="historique-total">
      <div class="bloc gauche">
        <span class="label">Volume total des transactions</span>
        <span class="montant" style="color: var(--text-dark); font-size: 1.2rem;">
          Total des ventes (<?php echo count($affiche); ?>)
        </span>
      </div>
      <div class="bloc droite">
        <span class="label">Montant cumulé</span>
        <span class="montant"><?php echo $Total; ?>€</span>
      </div>
    </div>

  </div>

  <!-- NOTE BAS DE PAGE -->
  <p class="small text-muted" style="text-align:center; margin-top: 24px;">
    L'historique est mis à jour en temps réel après chaque validation de vente en caisse.
  </p>

</div>

<!-- FOOTER -->
<footer>
  <p class="copyright">© 2024 CaisseShop — Système de Gestion de Caisse de Proximité</p>
</footer>

</body>
</html>