<?php
require_once __DIR__ . '/config.php';
error_reporting(0);
if (!isset($_COOKIE["client"])) {
    header("Location: index.php");
    exit();
}

$client = json_decode($_COOKIE["client"], true);
$file = file_get_contents(DATA_DIR . '/data.json');
$data = json_decode($file, true);

if (
    isset($data[$client["email"]]) &&
    $data[$client["email"]]["role"]["bloque"] == true
) {
    setcookie("client", "", time() - 3600);
    header("Location: index.php?msg=bloque");
    exit();
}
$client = json_decode($_COOKIE["client"], true);
$mail = $client["email"];
$file = file_get_contents(DATA_DIR . '/data.json');
$data = json_decode($file, true);
$commande_data = file_get_contents(DATA_DIR . '/panier_' . $mail . '.json');
$commande = json_decode($commande_data, true);
$plat_data = file_get_contents(DATA_DIR . '/plat.json');
$plat = json_decode($plat_data, true);

function aff_num_cmd_ou_fidelite($num, $cmd_ou_fidelite)
{
    if ($cmd_ou_fidelite == 1) {
        if ($num < 10) {
            echo "000" . $num;
        } elseif ($num < 100) {
            echo "00" . $num;
        } elseif ($num < 1000) {
            echo "0" . $num;
        } else {
            echo $num;
        }
    } else {
        if ($num < 10) {
            echo "0000000" . $num;
        } elseif ($num < 100) {
            echo "000000" . $num;
        } elseif ($num < 10000) {
            echo "00000" . $num;
        } elseif ($num < 100000) {
            echo "0000" . $num;
        } elseif ($num < 1000000) {
            echo "000" . $num;
        } elseif ($num < 10000000) {
            echo "00" . $num;
        } elseif ($num < 100000000) {
            echo "0" . $num;
        } else {
            echo $num;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande Refusée - Les Croquettes du Chef</title>
    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/client.css">
    <link rel="stylesheet" href="css/confirmation.css">
    <link href="assets/Logo projet.png" rel="icon">
    <script src="js/charte.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/Logo projet.png" alt="Logo" class="header-logo">
            Les croquettes du chef
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menu.php">La Carte</a></li>
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li><a href="profil.php" class="btn">Profil</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="bloc-confirmation">
            <div class="entete-confirmation">
                <div class="icone-check">❌</div>
                <h1>Commande refusée !</h1>
                <p>Votre paiement a été refusé.</p>
            </div>
            <div class="carte">
                <h2>Récapitulatif de commande</h2>
                <?php foreach ($commande["plats"] as $id => $detail) { ?>
                    <div class="ligne-article">
                        <span><?php echo $commande["plats"][$id][
                            "name"
                        ]; ?> x<?php echo $commande["plats"][$id][
     "quantite"
 ]; ?></span>
                        <span><?php echo number_format(
                            $commande["plats"][$id]["prix"],
                            2,
                            ",",
                            " ",
                        ); ?>€</span>
                    </div>
                <?php } ?>
                <?php foreach ($commande["menus"] as $id => $detail) { ?>
                    <div class="ligne-article">
                        <span><?php echo $commande["menus"][$id][
                            "name"
                        ]; ?> x<?php echo $commande["menus"][$id][
     "quantite"
 ]; ?></span>
                        <span><?php echo number_format(
                            $commande["menus"][$id]["prix"],
                            2,
                            ",",
                            " ",
                        ); ?>€</span>
                    </div>
                <?php } ?>
                <?php if ($commande["reduction"] == true) { ?>
                    <div class="ligne-reduction">
                        <span>Réduction coupon fidélité</span>
                        <span>-<?php echo number_format(
                            $commande["total"] / 4,
                            2,
                            ",",
                            " ",
                        ); ?></span>
                    </div>
                <?php } ?>
                <div class="ligne-total">
                    <span>Total</span>
                    <span><?php if ($commande["reduction"] == true) {
                        echo number_format(
                            (3 * $commande["total"]) / 4,
                            2,
                            ",",
                            " ",
                        );
                    } else {
                        echo number_format($commande["total"], 2, ",", " ");
                    } ?>€</span>
                </div>
            </div>
            <div class="zone-boutons">
                <a href="panier.php" class="btn-confirmation principal">Retour au panier</a>
                <a href="profil.php" class="btn-confirmation secondaire">Retour au profil</a>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
</body>
</html>
