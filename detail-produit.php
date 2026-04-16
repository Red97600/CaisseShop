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



$produit=$mysqlClient->prepare("SELECT * FROM `produits` WHERE 1");
$produit->execute();
$montré=$produit->fetchAll();


$table=null;

if (isset($_POST['Id'])) {
  $Id=(int) $_POST['Id'];   //je verifie il a bien un id si oui on le met en int 
}
else {
  $Id=null;
}

if ($Id !== null) {
    for ($i=0; $i<count($montré); $i++) {
        if ($montré[$i]['Id'] == $Id) {  // cette ligne va parcourire le tableau et compare tous les Id pour trouver celle qui est equal notre id
            $table = $i;
            break;
        }
    }
}


// modifier un produit

if (isset($_POST['Nom']) && !empty($_POST['Nom'])) {

  if (isset($_POST['Description']) && !empty($_POST['Description'])) {

    if (isset($_POST['Prix']) && !empty($_POST['Prix'])) {

      if (isset($_POST['Stock']) && !empty($_POST['Stock'])) {

        if (isset($_POST['Code_bare']) && !empty($_POST['Code_bare'])) {

          $nvnom = trim($_POST['Nom']);
          $nvdct = trim($_POST['Description']);
          $nvprix = trim($_POST['Prix']);
          $nvstock = trim($_POST['Stock']);
          $nvcode = trim($_POST['Code_bare']);

          $insert = $mysqlClient->prepare('UPDATE `produits` SET `Nom`=:Nom, `Description`=:Description, `Prix`=:Prix, `Stock`=:Stock, `Code_bare`=:Code_bare WHERE `Id` = :Id');            
          $insert->execute([
            'Nom' => $nvnom,
            'Description' => $nvdct,
            'Prix' => $nvprix,
            'Stock' => $nvstock,
            'Code_bare' => $nvcode,
            'Id' => $Id
         ]);
        }
      }
   }
  }
}







?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Détail Produit</title>
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
    <a href="produits.php" class="active">📦 Produits</a>
    <a href="historique.php">🧾 Ventes</a>
  </nav>
  <div class="header-right">
    <span class="session-info">👤 Session : <?php echo $_SESSION['utilisateur']['nom']; ?></span>
    <a href="deconnexion.php" class="btn-deconnexion">↩ Déconnexion</a>
  </div>
  <script src="https://labelwriter.com/software/dls/sdk/js/DYMO.Label.Framework.latest.js"></script>
</header>

<!-- CONTENU -->
<div class="page-content">

  <!-- BREADCRUMB -->
  <div class="breadcrumb">
    <a href="produits.php">← Retour à la liste</a>
  </div>

  <!-- TITRE -->
  <div class="page-header">
    <div>
      <h1>📦 Détail du Produit</h1>
    </div>
  </div>

  <!-- CARTE UNIQUE -->
  <div class="detail-card">

    <form action="detail-produit.php" method="POST"> 
        <!-- Nom -->
        <div class="field-row">
          <label>🏷 Nom du produit</label>
          <input type="text" class="field-value" name="Nom" placeholder="Modifier le Nom_produit" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Nom']) : ''; ?>">
        </div>

        <!-- Description -->
        <div class="field-row">
          <label>ℹ Description</label>
          <input type="text" class="field-value" name="Description" placeholder="Modifier la description" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Description']) : ''; ?>">
        </div>

        <!-- Prix + Stock côte à côte -->
        <div class="detail-grid">
          <div class="field-row">
            <label>€ Prix (€)</label>
            <input type="text" class="field-value" name="Prix" placeholder="Modifier le prix" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Prix']) : ''; ?>">
          </div>
          <div class="field-row">
            <label>📦 Stock actuel</label>
            <input type="text" class="field-value" name="Stock" placeholder="Modifier le stock" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Stock']) : ''; ?>">
          </div>
        </div>

        <!-- Code-barres -->
        <div class="field-row">
          <label>||| Code-barres (EAN-13)</label>
          <input type="text" class="field-value" name="Code_bare" placeholder="Modifier le code-barres" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Code_bare']) : ''; ?>">
        </div>

        <!-- Aperçu code-barres -->
        <div class="barcode-box">
          <p>APERÇU DU CODE-BARRES</p>
          <img src="https://barcodeapi.org/api/<?php echo $table !== null ? htmlspecialchars($montré[$table]['Code_bare']) : ''; ?>" alt="Code-barres" />
          <p><?php echo $table !== null ? htmlspecialchars($montré[$table]['Code_bare']) : ''; ?></p>
          <p>Ceci est une représentation visuelle générée pour le système d'étiquetage en magasin.</p>
        </div>

      <!-- BOUTONS -->
      <div class="detail-actions">
        <button type="submit" name="Enregistrer les modifications" class="btn-primary">💾 Enregistrer les modifications</button>
        <button class="btn-secondary">✕ Annuler</button>
        <button id="printBtn" class="btn-secondary">⬇ Imprimer</button>
        <?php if ($Id !== null): ?><input type="hidden" name="Id" value="<?php echo $Id; ?>"><?php endif; ?>
      </div>
    </form>
  </div>

</body>
</html>

