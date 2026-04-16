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





$produit = $mysqlClient->prepare("SELECT * FROM produits");
$produit->execute();
$affiches = $produit->fetchAll();

if (isset($_GET['recherche']) && !empty($_GET['recherche'])) {

    $search = trim($_GET['recherche']);
    $results = [];

    for ($i = 0; $i < count($affiches); $i++) {

        if (
            stripos($affiches[$i]['Nom'], $search) !== false ||
            stripos($affiches[$i]['Code_bare'], $search) !== false
        ) {
            $results[] = $affiches[$i];
        }
    }

    $affiches = $results;
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Produits — CaisseShop</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header class="site-header">

  <div class="logo">
    <img src="logo.jpg" alt="CaisseShop">
  </div>

  <nav class="menu">
    <a href="caisse.php">🛒 Caisse</a>
    <a href="produits.php" class="active">📦 Produits</a>
    <a href="historique.php">🕐 Ventes</a>
  </nav>

  <div class="header-right">
    <span class="session-info">👤 Session : <?php echo $_SESSION['utilisateur']['nom']; ?></span>
    <button class="btn-deconnexion">↩ Déconnexion</button>
  </div>

</header>

<!-- CONTENU PRINCIPAL -->
<div class="page-content">

  <!-- TITRE DE PAGE -->
  <div class="page-header">
    <div>
      <h1>Liste des Produits</h1>
      <p>Gérez votre inventaire et suivez vos stocks en temps réel.</p>
    </div>
  </div>

  <!-- TOOLBAR : recherche + bouton -->
  <div class="toolbar">
    <div class="toolbar-left">
      <div class="search-bar" style="margin-bottom:0;">
        <form action="produits.php" method="GET">
          <input type="text" name="recherche" placeholder="🔍 Rechercher un produit par nom ou code-barres...">
        </form>
      </div>
    </div>
    <div class="toolbar-right">
      <button class="btn-secondary">🔽 Filtrer</button>
      <a href="ajouter-produit.php" class="btn-orange">+ Nouveau Produit</a>
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
        <?php
          for ($i = 0; $i < count($affiches); $i++) {
            $stock    = (int) $affiches[$i]['Stock'];
            $lowStock = $stock <= 15;

            echo '<tr>';

            // Nom + catégorie
            echo '<td>
                    <div style="font-weight:700;color:var(--text-dark);">' . htmlspecialchars($affiches[$i]['Nom']) . '</div>
                    <div style="font-size:0.78rem;color:var(--text-light);font-weight:600;">' . htmlspecialchars($affiches[$i]['Categorie'] ?? '') . '</div>
                  </td>';

            // Code-barres
            echo '<td style="color:var(--text-mid);">' . htmlspecialchars($affiches[$i]['Code_bare']) . '</td>';

            // Prix
            echo '<td style="font-weight:700;">' . number_format($affiches[$i]['Prix'], 2, '.', '') . ' €</td>';

            // Stock (badge rouge si bas)
            if ($lowStock) {
              echo '<td><span style="
                      background:var(--danger);
                      color:#fff;
                      padding:3px 10px;
                      border-radius:20px;
                      font-size:0.78rem;
                      font-weight:700;
                    ">' . $stock . ' en stock</span></td>';
            } else {
              echo '<td class="stock-ok">' . $stock . ' en stock</td>';
            }

            // Action
            echo '<td>
                    <form method="POST" action="detail-produit.php">
                      <button class="lien-modifier" type="submit" name="Id" value="' . $affiches[$i]['Id'] . '">✏️ Modifier</button>
                    </form>
                  </td>';

            echo '</tr>';
          }
        ?>
      </tbody>

    </table>

    <!-- PAGINATION -->
    <div class="pagination">
      <div class="pagination-info">
        Affichage de <?php echo count($affiches); ?> sur <?php echo count($affiches); ?> produits
      </div>
      <div class="pagination-buttons">
        <button>Précédent</button>
        <button class="active">1</button>
        <button>Suivant</button>
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