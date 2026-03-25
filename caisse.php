<?php
session_start(); // Démarrage de la session pour gérer le panier


try
{
    $mysqlClient = new PDO('mysql:host=localhost;dbname=caiseshop;charset=utf8', 'root', '');
}
catch (Exception $e)
{
    die('Erreur : ' . $e->getMessage());
}




$sqlQuery='SELECT * FROM produits';
$select=$mysqlClient->prepare($sqlQuery);    
$select->execute();
$Produits =$select->fetchAll();



if(isset($_POST['id']) && !empty($_POST['id'])) {
    $id = (int) $_POST['id'];

    $sqlQuery = 'SELECT * FROM produits WHERE Id = :id';
    $select = $mysqlClient->prepare($sqlQuery);
    $select->execute(['id' => $id]);
    $Produit = $select->fetch();

    if(!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if(isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]['Quantite']++;
    } else {
        $_SESSION['panier'][$id] = [
            'Nom' => $Produit['Nom'],
            'Prix' => $Produit['Prix'],
            'Quantite' => 1
        ];
    }
}
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

<header class="app-header">

    <div class="logo">
        <img src="logo.webp" alt="Logo">
    </div>

    <nav class="nav">
        <a href="caisse.php">Caisse</a>
        <a href="produits.php">Produits</a>
        <a href="historique.php">Historique</a>
    </nav>

</header>

<!-- BARRE DE RECHERCHE -->
<div>
    <form action="caisse.php" method="GET" class="search-bar">
        <input type="text" name="q" placeholder="Rechercher un produit ou scanner...">
    </form>
</div>

<h2>Ticket Actuel</h2>

<div class="ticket">

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

        echo '<p>' . $_SESSION['panier'][$id]['Nom'] . ' x' . $quantite . ' - ' . $sousTotal . ' €</p>';
    }
}

echo '<h3>Total: ' . $total . ' €</h3>';
?>

</div>






<!-- FILTRES -->
<form method="GET" class="filters">

    <button type="submit" name="categorie" value="tous">Tous</button>
    <button type="submit" name="categorie" value="boulangerie">Boulangerie</button>
    <button type="submit" name="categorie" value="laitiers">Produits Laitiers</button>
    <button type="submit" name="categorie" value="fruits">Fruits</button>
    <button type="submit" name="categorie" value="epicerie">Épicerie</button>
    <button type="submit" name="categorie" value="boissons">Boissons</button>
</form>


<!-- PRODUITS -->
<section class="section">
    <div class="container">
        <h2>Produits</h2>

        <div class="cards">
            <?php for ($i = 0; $i < count($Produits); $i++) { ?>

                    <article class="card">

                        <p class="badge">Stock: <?php echo $Produits[$i]['Stock']; ?></p>

                        <img src="img/default.jpg">

                        <h3><?php echo $Produits[$i]['Nom']; ?></h3>

                        <p class="meta"><?php echo $Produits[$i]['categorie']; ?></p>

                        <p class="prix"><?php echo $Produits[$i]['Prix']; ?> €</p>

                            <form method="POST" action="">
                                
                                <input type="hidden" name="id" value="<?php echo $Produits[$i]['Id']; ?>">

                                <button type="submit" class="btn-plus">+</button>

                            </form>
                    </article>

            <?php } ?>
        </div>
    </div>
</section>


</body>
</html>