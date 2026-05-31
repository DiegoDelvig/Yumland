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
$file = file_get_contents(DATA_DIR . '/data.json');
$data = json_decode($file, true);

$client = json_decode($_COOKIE["client"], true);
$mail = $client["email"];
$file = file_get_contents(DATA_DIR . '/data.json');
$data = json_decode($file, true);
$commande_data = file_get_contents(DATA_DIR . '/panier_' . $mail . '.json');
$commande = json_decode($commande_data, true);
$plat_data = file_get_contents(DATA_DIR . '/plat.json');
$plat = json_decode($plat_data, true);
$menu_data = file_get_contents(DATA_DIR . '/menu.json');
$menu_dispo = json_decode($menu_data, true);

foreach ($commande["plats"] as $id => $detail) {
    if (isset($_REQUEST["btn_suppr_" . str_replace(" ", "_", $id)])) {
        $commande["total"] =
            $commande["total"] - $detail["prix"] * $detail["quantite"];
        unset($commande["plats"][$id]);
        file_put_contents(
            DATA_DIR . '/panier_' . $mail . '.json',
            json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        header("Location: panier.php");
        exit();
    } elseif (isset($_REQUEST["btn_plus_" . str_replace(" ", "_", $id)])) {
        $commande["total"] = round($commande["total"] + $detail["prix"], 2);
        $commande["plats"][$id]["quantite"]++;
        file_put_contents(
            DATA_DIR . '/panier_' . $mail . '.json',
            json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        header("Location: panier.php");
        exit();
    } elseif ($detail["quantite"] > 1) {
        if (isset($_REQUEST["btn_moins_" . str_replace(" ", "_", $id)])) {
            $commande["total"] = round($commande["total"] - $detail["prix"], 2);
            $commande["plats"][$id]["quantite"]--;
            file_put_contents(
                DATA_DIR . '/panier_' . $mail . '.json',
                json_encode(
                    $commande,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header("Location: panier.php");
            exit();
        }
    }
}
foreach ($commande["menus"] as $id_m => $detail_m) {
    if (isset($_REQUEST["btn_suppr_" . str_replace(" ", "_", $id_m)])) {
        $commande["total"] =
            $commande["total"] - $detail_m["prix"] * $detail_m["quantite"];
        unset($commande["menus"][$id_m]);
        file_put_contents(
            DATA_DIR . '/panier_' . $mail . '.json',
            json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        header("Location: panier.php");
        exit();
    } elseif (isset($_REQUEST["btn_plus_" . str_replace(" ", "_", $id_m)])) {
        $commande["total"] = round($commande["total"] + $detail_m["prix"], 2);
        $commande["menus"][$id_m]["quantite"]++;
        file_put_contents(
            DATA_DIR . '/panier_' . $mail . '.json',
            json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        header("Location: panier.php");
        exit();
    } elseif ($detail_m["quantite"] > 1) {
        if (isset($_REQUEST["btn_moins_" . str_replace(" ", "_", $id_m)])) {
            $commande["total"] = round(
                $commande["total"] - $detail_m["prix"],
                2,
            );
            $commande["menus"][$id_m]["quantite"]--;
            file_put_contents(
                DATA_DIR . '/panier_' . $mail . '.json',
                json_encode(
                    $commande,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header("Location: panier.php");
            exit();
        }
    }
}

if (isset($_REQUEST["mode_temps"])) {
    $commande["planification"] = $_REQUEST["mode_temps"];
    $commande["date_voulue"] =
        $commande["planification"] == "planifie"
            ? $_REQUEST["date_prevue"]
            : "maintenant";
    file_put_contents(
        DATA_DIR . '/panier_' . $mail . '.json',
        json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    );
}

if ($data[$client["email"]]["point_fidelite"] > 299) {
    $commande["reduction"] = true;
} else {
    $commande["reduction"] = false;
}

file_put_contents(
    DATA_DIR . '/panier_' . $mail . '.json',
    json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
);

include "getapikey/getapikey.php";
$getapikey = getAPIKey("MI-1_I");

if ($commande["reduction"] == true) {
    $reduc = $commande["total"] / 4;
    $montant = number_format((3 * $commande["total"]) / 4, 2, ".", "");
} else {
    $reduc = 0;
    $montant = number_format($commande["total"], 2, ".", "");
}

$transac = uniqid();
$vendeur = "MI-1_I";
$retour = "http://localhost:7180/post-cybank.php";
$control = md5(
    $getapikey .
        "#" .
        $transac .
        "#" .
        $montant .
        "#" .
        $vendeur .
        "#" .
        $retour .
        "#",
);

function conjugaison($sing, $plur, $val)
{
    return $val == 1 ? $sing : $plur;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - Les Croquettes du Chef</title>
    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/panier.css">
    <link rel="stylesheet" href="css/client.css">
    <link href="assets/Logo projet.png" rel="icon">
    <script src="js/charte.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/Logo projet.png" alt="Logo" class="header-logo" style="height: 35px;">
            Espace client
        </div>
        <nav>
            <ul>
                <li><a href="menu.php">La Carte</a></li>
                <li><a href="panier.php" class="active">Panier</a></li>
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li><a href="profil.php" class="btn">Profil</a></li>
            </ul>
        </nav>
    </header>

    <h1>Mon Panier</h1>

    <?php if (empty($commande["plats"]) && empty($commande["menus"])) { ?>
        <div class="rien_commander">
            <p><strong>Votre panier est vide</strong></p>
        </div>
    <?php } ?>

    <main class="container">
        <div class="diff_part">
            <section class="contenu-profil">
                <?php foreach ($commande["plats"] as $id => $detail) { ?>
                    <div class="carte-info">
                        <img class="img_cmd" src="assets/<?php echo $plat[$id][
                            "image"
                        ]; ?>" alt="">
                        <div class="clm_1">
                            <h2 class="name"><?php echo $plat[$id][
                                "name"
                            ]; ?></h2>
                            <p class="name"><small>Plat individuel</small></p>
                        </div>
                        <div class="clm_2">
                            <p class="prix"><?php echo number_format(
                                $detail["quantite"] * $plat[$id]["prix"],
                                2,
                                ",",
                                " ",
                            ); ?>€</p>
                            <form method="POST">
                                <div class="gestion_quantite">
                                    <?php if ($detail["quantite"] == 1) { ?>
                                        <button name="btn_suppr_<?php echo str_replace(
                                            " ",
                                            "_",
                                            $id,
                                        ); ?>" class="btn-carte">🗑️</button>
                                    <?php } else { ?>
                                        <button name="btn_moins_<?php echo str_replace(
                                            " ",
                                            "_",
                                            $id,
                                        ); ?>" class="btn-carte">-</button>
                                    <?php } ?>
                                    <p><strong><?php echo $detail[
                                        "quantite"
                                    ]; ?></strong></p>
                                    <button name="btn_plus_<?php echo str_replace(
                                        " ",
                                        "_",
                                        $id,
                                    ); ?>" class="btn-carte">+</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php } ?>

                <?php if (isset($commande["menus"])) {
                    foreach ($commande["menus"] as $id_m => $detail_m) { ?>
                        <div class="carte-info">
                            <div class="clm_1">
                                <h2 class="name">🎁 <?php echo $detail_m[
                                    "name"
                                ]; ?></h2>
                                <p class="name"><small>
                                    <?php if (
                                        isset($detail_m["plats"]) &&
                                        count($detail_m["plats"]) > 0
                                    ) {
                                        $noms = array_map(function ($p) use (
                                            $plat,
                                        ) {
                                            return isset($plat[$p])
                                                ? $plat[$p]["name"]
                                                : ucfirst($p);
                                        }, $detail_m["plats"]);
                                        echo implode(", ", $noms);
                                    } else {
                                        echo "Menu complet";
                                    } ?>
                                </small></p>
                            </div>
                            <div class="clm_2">
                                <p class="prix"><?php echo number_format(
                                    $detail_m["quantite"] * $detail_m["prix"],
                                    2,
                                    ",",
                                    " ",
                                ); ?>€</p>
                                <form method="POST">
                                    <div class="gestion_quantite">
                                    <?php if ($detail_m["quantite"] == 1) { ?>
                                        <button name="btn_suppr_<?php echo str_replace(
                                            " ",
                                            "_",
                                            $id_m,
                                        ); ?>" class="btn-carte">🗑️</button>
                                    <?php } else { ?>
                                        <button name="btn_moins_<?php echo str_replace(
                                            " ",
                                            "_",
                                            $id_m,
                                        ); ?>" class="btn-carte">-</button>
                                    <?php } ?>
                                    <p><strong><?php echo $detail_m[
                                        "quantite"
                                    ]; ?></strong></p>
                                    <button name="btn_plus_<?php echo str_replace(
                                        " ",
                                        "_",
                                        $id_m,
                                    ); ?>" class="btn-carte">+</button>
                                </div>
                                </form>
                            </div>
                        </div>
                <?php }
                } ?>

            </section>

            <?php if ($commande["total"] != 0) { ?>
                <div class="recapitulatif">
                    <h2>Récapitulatif</h2>

                    <div class="bloc-planification">
                            <p><strong>🕒 Préparation :</strong></p>
                            <label><input type="radio" name="mode_temps" value="immediat" checked onclick="document.getElementById('zone_p').style.display='none'"> Immédiat</label><br>
                            <label><input type="radio" name="mode_temps" value="planifie" onclick="document.getElementById('zone_p').style.display='block'"> Planifier</label>

                            <div id="zone_p" class="zone-planification">
                                <input type="datetime-local" name="date_prevue" min="<?php echo date(
                                    "Y-m-d\TH:i",
                                ); ?>" style="width:100%;">
                            </div>
                        </div>

                    <?php foreach ($commande["plats"] as $id => $detail) { ?>
                        <div class="commande">
                            <p><?php echo $detail["name"] .
                                " x" .
                                $detail["quantite"]; ?></p>
                            <p class="prix_recap"><?php echo number_format(
                                $detail["quantite"] * $plat[$id]["prix"],
                                2,
                                ",",
                                " ",
                            ); ?>€</p>
                        </div>
                    <?php } ?>

                    <?php if (isset($commande["menus"])) {
                        foreach ($commande["menus"] as $id_m => $detail_m) { ?>
                            <div class="commande">
                                <p><?php echo $detail_m["name"] .
                                    " x" .
                                    $detail_m["quantite"]; ?></p>
                                <p class="prix_recap"><?php echo number_format(
                                    $detail_m["quantite"] * $detail_m["prix"],
                                    2,
                                    ",",
                                    " ",
                                ); ?>€</p>
                            </div>
                    <?php }
                    } ?>

                    <?php if ($commande["reduction"]) { ?>
                        <div class="reduction">
                            <p>Réduction coupon fidélité</p>
                            <p class="prix_recap">-<?php echo number_format(
                                $reduc,
                                2,
                                ",",
                                " ",
                            ); ?>€</p>
                        </div>
                    <?php } ?>

                    <div class="commande_total">
                        <p><strong>TOTAL</strong></p>
                        <p class="prix_recap"><strong><?php echo number_format(
                            $montant,
                            2,
                            ",",
                            " ",
                        ); ?>€</strong></p>
                    </div>

                    <form action='https://www.plateforme-smc.fr/cybank/index.php' method='POST'>
                        <input type='hidden' name='transaction' value='<?php echo $transac; ?>'>
                        <input type='hidden' name='montant' value='<?php echo $montant; ?>'>
                        <input type='hidden' name='vendeur' value='<?php echo $vendeur; ?>'>
                        <input type='hidden' name='retour' value='<?php echo $retour; ?>'>
                        <input type='hidden' name='control' value='<?php echo $control; ?>'>
                        <input class="bouton-recommande" type='submit' value="Paiement">
                    </form>
                </div>
            <?php } ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
</body>
</html>
