<?php
session_start(); // Démarre la session pour gérer le panier et les utilisateurs

// Redirige vers la page de connexion si l'utilisateur n'est pas connecté
if (!isset($_SESSION['utilisateur'])) {
    header('Location: connexion.php');
    exit;
}


// Connexion à la base de données
try
{
    $mysqlClient = new PDO('mysql:host=localhost;dbname=caisseshop;charset=utf8', 'root', '');
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}


// Récupération des produits en fonction de la catégorie sélectionnée
$categorie ='tous'; // Valeur par défaut pour afficher tous les produits

if(isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $categorie = $_GET['categorie'];
}

if($categorie == 'tous') {
    $sqlQuery = 'SELECT * FROM produits';
    $select = $mysqlClient->prepare($sqlQuery);
    $select->execute();
} else {
    $sqlQuery = 'SELECT * FROM produits WHERE categorie = :categorie';
    $select = $mysqlClient->prepare($sqlQuery);
    $select->execute(['categorie' => $categorie]);
}

$Produits = $select->fetchAll();

// pour le scan de code barre
if(isset($_POST['barcode']) && !empty($_POST['barcode'])) {

    $code = trim($_POST['barcode']);

    $sql = "SELECT Id FROM produits WHERE code_barres = :code";
    $stmt = $mysqlClient->prepare($sql);
    $stmt->execute(['code' => $code]);

    $produit = $stmt->fetch();

    if($produit){
        // 🔥 on transforme en bouton +
        $_POST['action'] = 'plus';
        $_POST['id'] = $produit['Id'];
    } 
     else {
        echo "<p> Produit non trouvé</p>";
    }
}

// Gestion du panier
if(isset($_POST['action']) && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    if(!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

// + pour ajouter un produit ou augmenter la quantité    

    if($_POST['action'] == 'plus') {
        if(isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]['Quantite']++;
        } else {
            $sqlQuery = 'SELECT * FROM produits WHERE Id = :id';
            $select = $mysqlClient->prepare($sqlQuery);
            $select->execute(['id' => $id]);
            $Produit = $select->fetch();

            $_SESSION['panier'][$id] = [
                'Nom' => $Produit['Nom'],
                'Prix' => $Produit['Prix'],
                'Quantite' => 1
            ];
        }
    } // - pour diminuer la quantité ou supprimer le produit du panier
    if($_POST['action'] == 'moins') {
        if(isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]['Quantite']--;

            if($_SESSION['panier'][$id]['Quantite'] <= 0) {
                unset($_SESSION['panier'][$id]);
            }
        }
    }
}

// Validation de la vente
if(isset($_POST['validvente'])) {

    if(isset($_SESSION['panier']) && count($_SESSION['panier']) > 0) {

        $total = 0;
        $ids = array_keys($_SESSION['panier']);

        for($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $quantite = $_SESSION['panier'][$id]['Quantite'];
            $prix = $_SESSION['panier'][$id]['Prix'];
            $total += $prix * $quantite;
        }

        // Enregistrement de la vente dans la base de données
        $sqlQuery = 'INSERT INTO ventes (total, Date_creation, Utilisateur_id) VALUES (:total, NOW(), :Utilisateur_id)';
        $insert = $mysqlClient->prepare($sqlQuery);
        $insert->execute(['total' => $total, 'Utilisateur_id' => $_SESSION['utilisateur']['nom']]);

        // Récupération de l'ID de la vente pour enregistrer les détails
        $venteId = $mysqlClient->lastInsertId();

        for($i = 0; $i < count($ids); $i++) {
            $id = $ids[$i];
            $quantite = $_SESSION['panier'][$id]['Quantite'];
            $prix = $_SESSION['panier'][$id]['Prix'];

            // Enregistrement des détails de la vente
            $sqlQuery = 'INSERT INTO details_ventes (vente_id, produit_id, quantite, prix) VALUES (:vente_id, :produit_id, :quantite, :prix)';
            $insert = $mysqlClient->prepare($sqlQuery);
            $insert->execute([
                'vente_id' => $venteId,
                'produit_id' => $id,
                'quantite' => $quantite,
                'prix' => $prix
            ]);

            // Mise à jour du stock du produit
            $sqlQuery = 'UPDATE produits SET Stock = Stock - :quantite WHERE Id = :id';
            $update = $mysqlClient->prepare($sqlQuery);
            $update->execute([
                'quantite' => $quantite,
                'id' => $id
            ]);
        }

        // Réinitialisation du panier après validation
        unset($_SESSION['panier']);
    }
}

//----------------------------------------------------------------------------------------------------------------------
?>


<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Caisse</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- HEADER -->
<header class="site-header">

  <div class="logo">
    <img src="logo.jpg" alt="CaisseShop">
  </div>

  <nav class="menu">
    <a href="caisse.php" class="active">🛒 Caisse</a>
    <a href="produits.php" >📦 Produits</a>
    <a href="historique.php">🕐 Ventes</a>
  </nav>

  <div class="header-right">
    <span class="session-info">👤 Session : <?php echo $_SESSION['utilisateur']['nom']; ?></span>
    <button class="btn-deconnexion">↩ Déconnexion</button>
  </div>

</header>


<div class="pos-page">

  <!-- PRODUITS -->
  <section class="produits">
    <div class="pos-main">

      <!-- SEARCH -->
      <input id="scan" type="text" autofocus placeholder="Scannez un code-barres ou recherchez un produit..." />
      <!-- FILTRES -->
      <div class="filters">
        <form method="GET">
          <button type="submit" name="categorie" value="tous">Tous</button>
          <button type="submit" name="categorie" value="boulangerie">Boulangerie</button>
          <button type="submit" name="categorie" value="laitiers">Produits Laitiers</button>
          <button type="submit" name="categorie" value="fruits">Fruits</button>
          <button type="submit" name="categorie" value="epicerie">Épicerie</button>
          <button type="submit" name="categorie" value="boissons">Boissons</button>
        </form>
      </div>

      <!-- PRODUITS GRID -->
      <div class="product-grid">   
        <?php for ($i = 0; $i < count($Produits); $i++) { ?>

          <article>

            <p class="badge">Stock: <?php echo $Produits[$i]['Stock']; ?></p>

            <h3><?php echo $Produits[$i]['Nom']; ?></h3>
            <p class="meta"><?php echo $Produits[$i]['categorie']; ?></p>
            <p class="prix"><?php echo $Produits[$i]['Prix']; ?> €</p>

            <form method="POST">
              <input type="hidden" name="id" value="<?php echo $Produits[$i]['Id']; ?>">
              <input type="hidden" name="action" value="plus">
              <button type="submit" class="btn-plus">+</button>
            </form>

          </article>

        <?php } ?>
      </div>     

    </div>
  </section>

  <!-- PANIER -->
  <section class="panier">
    <div>

      <h2>
        Ticket Actuel 
        <span class="panier-count">
          <?php echo isset($_SESSION['panier']) ? count($_SESSION['panier']) : 0; ?>
        </span>
      </h2>

      <?php
      $total = 0;

      if(isset($_SESSION['panier'])) {

        $ids = array_keys($_SESSION['panier']);

        for($i = 0; $i < count($ids); $i++) {

          $id = $ids[$i];
          $quantite = $_SESSION['panier'][$id]['Quantite'];
          $prix = $_SESSION['panier'][$id]['Prix'];
          $sousTotal = $prix * $quantite;
          $total += $sousTotal;
      ?>

      <p>
        <?php echo $_SESSION['panier'][$id]['Nom']; ?>

        <!-- + -->
        <form method="POST">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <input type="hidden" name="action" value="plus">
          <button type="submit">+</button>
        </form>

        <!-- - -->
        <form method="POST">
          <input type="hidden" name="id" value="<?php echo $id; ?>">
          <input type="hidden" name="action" value="moins">
          <button type="submit">-</button>
        </form>

        x<?php echo $quantite; ?> - <?php echo $sousTotal; ?> €
      </p>

      <?php } } ?>

      <h3>
        Total 
        <span class="montant-total"><?php echo $total; ?> €</span>
      </h3>
      <form method="POST" action="caisse.php">
      <button name="validvente" class="btn-valider">Valider la vente</button>
      </form>

    </div>
  </section>

</div>
<script>
function azertyToQwerty(text) {
    const map = {
        a:"q", z:"w", e:"e", r:"r", t:"t", y:"y", u:"u", i:"i", o:"o", p:"p",
        q:"a", s:"s", d:"d", f:"f", g:"g", h:"h", j:"j", k:"k", l:"l", m:"m",
        w:"z", x:"x", c:"c", v:"v", b:"b", n:"n",
        A:"Q", Z:"W", E:"E", R:"R", T:"T", Y:"Y",
        U:"U", I:"I", O:"O", P:"P",
        Q:"A", S:"S", D:"D", F:"F", G:"G",
        H:"H", J:"J", K:"K", L:"L", M:"M",
        W:"Z", X:"X", C:"C", V:"V", B:"B", N:"N"
    };

    return text.split("").map(c => map[c] || c).join("");
}

document.getElementById("scan").addEventListener("keydown", function(e) {

    if (e.key === "Enter") {

        let barcode = azertyToQwerty(this.value);

        fetch("caisse.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "barcode=" + encodeURIComponent(barcode)
        })
        .then(() => location.reload());

        this.value = "";
    }
});
</script>
</body>
</html>


