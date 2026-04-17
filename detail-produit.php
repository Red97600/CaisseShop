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
    <script src="https://cdn.jsdelivr.net/gh/dymosoftware/dymo-connect-framework/dymo.connect.framework.js">


</script>
</header>

<!-- CONTENU -->
<div class="page-content">

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
          <input type="text" class="field-value" name="Nom" id="productName" placeholder="Modifier le Nom_produit" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Nom']) : ''; ?>">
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
            <input type="text" class="field-value" name="Prix" id="priceValue" placeholder="Modifier le prix" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Prix']) : ''; ?>">
          </div>
          <div class="field-row">
            <label>📦 Stock actuel</label>
            <input type="text" class="field-value" name="Stock" placeholder="Modifier le stock" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Stock']) : ''; ?>">
          </div>
        </div>    

        <!-- Code-barres -->
        <div class="field-row">
          <label>||| Code-barres (EAN-13)</label>
          <input type="text" class="field-value" name="Code_bare" id="barcodeValue" placeholder="Modifier le code-barres" value="<?php echo $table !== null ? htmlspecialchars($montré[$table]['Code_bare']) : ''; ?>">
        </div>

        <!-- Aperçu code-barres -->
          <div class="barcode-box">
            <h2>Aperçu DYMO</h2>
            <div id="previewZone">
              <div >Aucun aperçu généré.</div>
            </div>
          </div>

          <div class="field-select">
            <label for="printerSelect">Imprimante DYMO</label>
            <select id="printerSelect"></select>
          </div>

      <!-- BOUTONS -->
      <div class="detail-actions">
        <button type="submit" name="Enregistrer les modifications" class="btn-primary">💾 Enregistrer les modifications</button>
        <button class="btn-secondary">✕ Annuler</button>
        <button type="button" id="printBtn">⬇ Imprimer</button>
        <?php if ($Id !== null): ?><input type="hidden" name="Id" value="<?php echo $Id; ?>"><?php endif; ?>
      </div>
    </form>
  </div>
</script>
</body>


  <script>
    const printerSelect = document.getElementById('printerSelect');
    const barcodeValue = document.getElementById('barcodeValue');
    const productName = document.getElementById('productName');
    const priceValue = document.getElementById('priceValue');
    const printBtn = document.getElementById('printBtn');
    const previewZone = document.getElementById('previewZone');

    const LABEL_XML = `<?xml version="1.0" encoding="utf-8"?>
<DieCutLabel Version="8.0" Units="twips" MediaType="Default">
	<PaperOrientation>Portrait</PaperOrientation>
	<Id>Small30334</Id>
	<IsOutlined>false</IsOutlined>
	<PaperName>30334 2-1/4 in x 1-1/4 in</PaperName>
	<DrawCommands>
		<RoundRectangle X="0" Y="0" Width="3240" Height="1800" Rx="270" Ry="270" />
	</DrawCommands>
	<ObjectInfo>
		<BarcodeObject>
			<Name>BARCODE</Name>
			<ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
			<BackColor Alpha="0" Red="255" Green="255" Blue="255" />
			<LinkedObjectName />
			<Rotation>Rotation0</Rotation>
			<IsMirrored>False</IsMirrored>
			<IsVariable>True</IsVariable>
			<GroupID>-1</GroupID>
			<IsOutlined>False</IsOutlined>
			<Text>REF-1234</Text>
			<Type>Code128Auto</Type>
			<Size>Medium</Size>
			<TextPosition>Bottom</TextPosition>
			<TextFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
			<CheckSumFont Family="Arial" Size="8" Bold="False" Italic="False" Underline="False" Strikeout="False" />
			<TextEmbedding>None</TextEmbedding>
			<ECLevel>0</ECLevel>
			<HorizontalAlignment>Center</HorizontalAlignment>
			<QuietZonesPadding Left="0" Top="0" Right="0" Bottom="0" />
		</BarcodeObject>
		<Bounds X="228" Y="885" Width="2880" Height="720" />
	</ObjectInfo>
	<ObjectInfo>
		<TextObject>
			<Name>NOM_PRODUIT</Name>
			<ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
			<BackColor Alpha="0" Red="255" Green="255" Blue="255" />
			<LinkedObjectName />
			<Rotation>Rotation0</Rotation>
			<IsMirrored>False</IsMirrored>
			<IsVariable>True</IsVariable>
			<GroupID>-1</GroupID>
			<IsOutlined>False</IsOutlined>
			<HorizontalAlignment>Center</HorizontalAlignment>
			<VerticalAlignment>Top</VerticalAlignment>
			<TextFitMode>ShrinkToFit</TextFitMode>
			<UseFullFontHeight>True</UseFullFontHeight>
			<Verticalized>False</Verticalized>
			<StyledText>
				<Element>
					<String xml:space="preserve">10 KG de RIZ</String>
					<Attributes>
						<Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
						<ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />
					</Attributes>
				</Element>
			</StyledText>
		</TextObject>
		<Bounds X="888" Y="135" Width="1410" Height="255" />
	</ObjectInfo>
	<ObjectInfo>
		<TextObject>
			<Name>PRIX</Name>
			<ForeColor Alpha="255" Red="0" Green="0" Blue="0" />
			<BackColor Alpha="0" Red="255" Green="255" Blue="255" />
			<LinkedObjectName />
			<Rotation>Rotation0</Rotation>
			<IsMirrored>False</IsMirrored>
			<IsVariable>True</IsVariable>
			<GroupID>-1</GroupID>
			<IsOutlined>False</IsOutlined>
			<HorizontalAlignment>Center</HorizontalAlignment>
			<VerticalAlignment>Top</VerticalAlignment>
			<TextFitMode>ShrinkToFit</TextFitMode>
			<UseFullFontHeight>True</UseFullFontHeight>
			<Verticalized>False</Verticalized>
			<StyledText>
				<Element>
					<String xml:space="preserve">Prix : 9,99€</String>
					<Attributes>
						<Font Family="Arial" Size="12" Bold="False" Italic="False" Underline="False" Strikeout="False" />
						<ForeColor Alpha="255" Red="0" Green="0" Blue="0" HueScale="100" />
					</Attributes>
				</Element>
			</StyledText>
		</TextObject>
		<Bounds X="903" Y="480" Width="1560" Height="270" />
	</ObjectInfo>
</DieCutLabel>`;


// Validation du label DYMO
function validateLabel() {
    try {
        const label = dymo.label.framework.openLabelXml(LABEL_XML);

        const isValid = label.isValidLabel();
        const isDLS = label.isDLSLabel();
        const isDCD = label.isDCDLabel();

        const result = document.getElementById("validationResult");

        result.innerHTML = `
            Label valide : ${isValid}<br>
            Format DYMO Label Software (DLS) : ${isDLS}<br>
            Format DYMO Connect (DCD) : ${isDCD}
        `;

    } catch (e) {
        document.getElementById("validationResult").innerText = "Erreur de validation : " + e;
    }
}

    function setStatus(message, type = '') {
      /*statusBox.textContent = message;
      statusBox.className = 'status' + (type ? ' ' + type : '');*/
    }

    function getSelectedPrinter() {
      return printerSelect.value;
    }

    function openLabel() {
      return dymo.label.framework.openLabelXml(LABEL_XML);
    }

    function updateLabelValues(label) {
      const barcode = barcodeValue.value.trim();
      const name = productName.value.trim();
      const price = priceValue.value.trim();

      if (!barcode) {
        throw new Error('Veuillez saisir une valeur pour BARCODE.');
      }

      label.setObjectText('BARCODE', barcode);
      label.setObjectText('NOM_PRODUIT', name || '');
      label.setObjectText('PRIX', price || '');
    }

    function loadPrinters() {
      try {
        const printers = dymo.label.framework.getPrinters() || [];
        const dymoPrinters = printers.filter(printer => {
          const type = (printer.printerType || '').toLowerCase();
          const name = (printer.name || '').toLowerCase();
          return type.includes('labelwriter') || name.includes('dymo');
        });

        printerSelect.innerHTML = '';

        if (dymoPrinters.length === 0) {
          setStatus('Aucune imprimante DYMO LabelWriter détectée.', 'error');
          const option = document.createElement('option');
          option.value = '';
          option.textContent = 'Aucune imprimante trouvée';
          printerSelect.appendChild(option);
          return;
        }

        dymoPrinters.forEach(printer => {
          const option = document.createElement('option');
          option.value = printer.name;
          option.textContent = printer.name;
          printerSelect.appendChild(option);
        });

        setStatus('Imprimante DYMO détectée. Vous pouvez générer un aperçu ou imprimer.', 'ok');
      } catch (error) {
        setStatus('Impossible de charger les imprimantes DYMO : ' + error.message, 'error');
      }
    }

function renderPreview() {
  try {
    const label = openLabel();
    updateLabelValues(label);

    const printerName = getSelectedPrinter();
    const renderParamsXml = "";

    const pngData = label.render(renderParamsXml, printerName);

    previewZone.innerHTML = "";
    const img = document.createElement("img");
    img.src = "data:image/png;base64," + pngData;
    img.alt = "Aperçu de l’étiquette DYMO";
    previewZone.appendChild(img);

    setStatus("Aperçu généré.", "ok");
  } catch (error) {
    previewZone.innerHTML = '<div class="muted">Impossible de générer l’aperçu.</div>';
    setStatus("Erreur d’aperçu : " + error.message, "error");
    console.error(error);
  }
}

function printLabel() {
  try {
    const printerName = getSelectedPrinter();
    if (!printerName) {
      throw new Error("Aucune imprimante DYMO sélectionnée.");
    }

    const copies = Number(1);
    const label = openLabel();
    updateLabelValues(label);

    const printParamsXml = `
      <LabelWriterPrintParams>
        <Copies>${copies}</Copies>
      </LabelWriterPrintParams>
    `;

    label.print(printerName, printParamsXml, "");
    setStatus("Impression envoyée à " + printerName + ".", "ok");
  } catch (error) {
    setStatus("Erreur d’impression : " + error.message, "error");
    console.error(error);
  }
}

    function initDymo() {
      try {
        if (!window.dymo || !dymo.label || !dymo.label.framework) {
          setStatus('Le framework DYMO n’est pas chargé.', 'error');
          return;
        }

        dymo.label.framework.init(() => {
          loadPrinters();
          renderPreview();
        });
      } catch (error) {
        setStatus('Initialisation DYMO impossible : ' + error.message, 'error');
      }
    }

    
    
    printBtn.addEventListener('click', printLabel);
    barcodeValue.addEventListener('input', renderPreview);
    productName.addEventListener('input', renderPreview);
    priceValue.addEventListener('input', renderPreview);

    initDymo();
  </script>

</html>




















