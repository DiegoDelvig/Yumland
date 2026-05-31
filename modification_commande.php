<?php
require_once __DIR__ . '/config.php';
session_start();
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

$id_cmd_actuelle = isset($_REQUEST["cmd"]) ? $_REQUEST["cmd"] : "";

if (isset($_POST["valider_modification_negative"])) {
    $choix = $_POST["choix_remboursement"];

    if ($choix == "points") {
        $diff_abs = floatval($_POST["montant_diff_abs"]);

        $points_normaux = floor($diff_abs);
        $points_compensation = $points_normaux * 2;

        $data[$mail]["point_fidelite"] += $points_compensation;

        file_put_contents(
            DATA_DIR . '/data.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        setcookie("client", json_encode($data[$mail]), time() + 3600);
    }

    $commande_data = file_get_contents(DATA_DIR . '/commande.json');
    $cmd_temp = json_decode($commande_data, true);

    if (
        empty($cmd_temp[$id_cmd_actuelle]["plats"]) &&
        empty($cmd_temp[$id_cmd_actuelle]["menus"])
    ) {
        unset($cmd_temp[$id_cmd_actuelle]);
        file_put_contents(
            DATA_DIR . '/commande.json',
            json_encode($cmd_temp, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
    }

    unset($_SESSION["montant_initial_" . $id_cmd_actuelle]);
    header("Location: profil.php");
    exit();
}

$commande_data = file_get_contents(DATA_DIR . '/commande.json');
$commande = json_decode($commande_data, true);
$cmd_total = $commande;

foreach ($commande as $id_cmd => $details) {
    if ($details["mail"] != $mail || $details["etat"]["cuisinee"] == true) {
        unset($commande[$id_cmd]);
    }
}

if (empty($id_cmd_actuelle) || !isset($commande[$id_cmd_actuelle])) {
    header("Location: profil.php");
    exit();
}

if (!isset($_SESSION["montant_initial_" . $id_cmd_actuelle])) {
    if ($commande[$id_cmd_actuelle]["reduction"] == true) {
        $_SESSION["montant_initial_" . $id_cmd_actuelle] =
            (3 * $commande[$id_cmd_actuelle]["total"]) / 4;
    } else {
        $_SESSION["montant_initial_" . $id_cmd_actuelle] =
            $commande[$id_cmd_actuelle]["total"];
    }
}

$ma_commande = $commande[$id_cmd_actuelle];

$plat_data = file_get_contents(DATA_DIR . '/plat.json');
$plat = json_decode($plat_data, true);
$menu_data = file_get_contents(DATA_DIR . '/menu.json');
$menu_dispo = json_decode($menu_data, true);

if (isset($ma_commande["plats"])) {
    foreach ($ma_commande["plats"] as $id => $detail) {
        if (isset($_REQUEST["btn_suppr_" . str_replace(" ", "_", $id)])) {
            $cmd_total[$id_cmd_actuelle]["total"] = round(
                $cmd_total[$id_cmd_actuelle]["total"] -
                    $detail["prix"] * $detail["quantite"],
                2,
            );
            unset($cmd_total[$id_cmd_actuelle]["plats"][$id]);
            file_put_contents(
                DATA_DIR . '/commande.json',
                json_encode(
                    $cmd_total,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header(
                "Location: modification_commande.php?cmd=" . $id_cmd_actuelle,
            );
            exit();
        } elseif (isset($_REQUEST["btn_plus_" . str_replace(" ", "_", $id)])) {
            $cmd_total[$id_cmd_actuelle]["total"] = round(
                $cmd_total[$id_cmd_actuelle]["total"] + $detail["prix"],
                2,
            );
            $cmd_total[$id_cmd_actuelle]["plats"][$id]["quantite"]++;
            file_put_contents(
                DATA_DIR . '/commande.json',
                json_encode(
                    $cmd_total,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header(
                "Location: modification_commande.php?cmd=" . $id_cmd_actuelle,
            );
            exit();
        } elseif ($detail["quantite"] > 1) {
            if (isset($_REQUEST["btn_moins_" . str_replace(" ", "_", $id)])) {
                $cmd_total[$id_cmd_actuelle]["total"] = round(
                    $cmd_total[$id_cmd_actuelle]["total"] - $detail["prix"],
                    2,
                );
                $cmd_total[$id_cmd_actuelle]["plats"][$id]["quantite"]--;
                file_put_contents(
                    DATA_DIR . '/commande.json',
                    json_encode(
                        $cmd_total,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                    ),
                );
                header(
                    "Location: modification_commande.php?cmd=" .
                        $id_cmd_actuelle,
                );
                exit();
            }
        }
    }
}

if (isset($ma_commande["menus"])) {
    foreach ($ma_commande["menus"] as $id_m => $detail_m) {
        if (isset($_REQUEST["btn_suppr_" . str_replace(" ", "_", $id_m)])) {
            $cmd_total[$id_cmd_actuelle]["total"] = round(
                $cmd_total[$id_cmd_actuelle]["total"] -
                    $detail_m["prix"] * $detail_m["quantite"],
                2,
            );
            unset($cmd_total[$id_cmd_actuelle]["menus"][$id_m]);
            file_put_contents(
                DATA_DIR . '/commande.json',
                json_encode(
                    $cmd_total,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header(
                "Location: modification_commande.php?cmd=" . $id_cmd_actuelle,
            );
            exit();
        } elseif (
            isset($_REQUEST["btn_plus_" . str_replace(" ", "_", $id_m)])
        ) {
            $cmd_total[$id_cmd_actuelle]["total"] = round(
                $cmd_total[$id_cmd_actuelle]["total"] + $detail_m["prix"],
                2,
            );
            $cmd_total[$id_cmd_actuelle]["menus"][$id_m]["quantite"]++;
            file_put_contents(
                DATA_DIR . '/commande.json',
                json_encode(
                    $cmd_total,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header(
                "Location: modification_commande.php?cmd=" . $id_cmd_actuelle,
            );
            exit();
        } elseif ($detail_m["quantite"] > 1) {
            if (isset($_REQUEST["btn_moins_" . str_replace(" ", "_", $id_m)])) {
                $cmd_total[$id_cmd_actuelle]["total"] = round(
                    $cmd_total[$id_cmd_actuelle]["total"] - $detail_m["prix"],
                    2,
                );
                $cmd_total[$id_cmd_actuelle]["menus"][$id_m]["quantite"]--;
                file_put_contents(
                    DATA_DIR . '/commande.json',
                    json_encode(
                        $cmd_total,
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                    ),
                );
                header(
                    "Location: modification_commande.php?cmd=" .
                        $id_cmd_actuelle,
                );
                exit();
            }
        }
    }
}

if (isset($_REQUEST["mode_temps"])) {
    $cmd_total[$id_cmd_actuelle]["planification"] = $_REQUEST["mode_temps"];
    $cmd_total[$id_cmd_actuelle]["date_voulue"] =
        $cmd_total[$id_cmd_actuelle]["planification"] == "planifie"
            ? $_REQUEST["date_prevue"]
            : "maintenant";
    file_put_contents(
        DATA_DIR . '/commande.json',
        json_encode($cmd_total, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    );
}

$ma_commande["reduction"] = false;

include "getapikey/getapikey.php";
$getapikey = getAPIKey("MI-1_I");

if ($ma_commande["reduction"] == true) {
    $reduc = $cmd_total[$id_cmd_actuelle]["total"] / 4;
    $montant = number_format(
        (3 * $cmd_total[$id_cmd_actuelle]["total"]) / 4,
        2,
        ".",
        "",
    );
} else {
    $reduc = 0;
    $montant = number_format($cmd_total[$id_cmd_actuelle]["total"], 2, ".", "");
}

$difference = $montant - $_SESSION["montant_initial_" . $id_cmd_actuelle];

$transac = uniqid();
$vendeur = "MI-1_I";
$retour = "http://localhost:7180/post-cybank.php";

if ($difference > 0) {
    $montant_a_payer = number_format($difference, 2, ".", "");
    $control = md5(
        $getapikey .
            "#" .
            $transac .
            "#" .
            $montant_a_payer .
            "#" .
            $vendeur .
            "#" .
            $retour .
            "#",
    );
}

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
    <title>Modification Commande - Les Croquettes du Chef</title>
    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/panier.css">
    <link rel="stylesheet" href="css/modification_commande.css">
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
                <li><a href="panier.php">Panier</a></li>
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li><a href="profil.php" class="btn">Profil</a></li>
            </ul>
        </nav>
    </header>

    <h1>Modification de la Commande n°<?php echo $id_cmd_actuelle; ?></h1>

    <?php if (empty($ma_commande["plats"]) && empty($ma_commande["menus"])) { ?>
        <div class="rien_commander" style="text-align: center; margin: 20px 0; padding: 15px; background: #ffebee; border-radius: 8px;">
            <p style="color: #c62828;"><strong>Cette commande ne contient plus aucun article.</strong><br>Veuillez valider les modifications ci-dessous pour annuler la commande et récupérer votre compensation.</p>
        </div>
    <?php } ?>

    <main class="container">
        <div class="diff_part">
            <section class="contenu-profil">
                <?php if (isset($ma_commande["plats"])) {
                    foreach ($ma_commande["plats"] as $id => $detail) { ?>
                        <div class="carte-info">
                            <img class="img_cmd" src="assets/<?php echo $plat[
                                $id
                            ]["image"]; ?>" alt="">
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
                <?php }
                } ?>

                <?php if (isset($ma_commande["menus"])) {
                    foreach ($ma_commande["menus"] as $id_m => $detail_m) { ?>
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

            <div class="recapitulatif">
                <h2>Récapitulatif Modifié</h2>

                <?php if ($difference > 0) { ?>
                    <div class="bloc-planification">
                        <p><strong>🕒 Préparation :</strong></p>
                        <label><input type="radio" name="mode_temps" value="immediat" checked onclick="document.getElementById('zone_p').style.display='none'"> Immédiat</label><br>
                        <label><input type="radio" name="mode_temps" value="planifie" onclick="document.getElementById('zone_p').style.display='block'"> Planifier</label>

                        <div id="zone_p" class="zone-planification" style="display: none;">
                            <input type="datetime-local" name="date_prevue" min="<?php echo date(
                                "Y-m-d\TH:i",
                            ); ?>" style="width:100%;">
                        </div>
                    </div>
                <?php } else { ?>
                    <form action="modification_commande.php?cmd=<?php echo $id_cmd_actuelle; ?>" method="POST" class="form-modification">
                        <div class="bloc-planification">
                            <p><strong>💰 Compensation pour le retrait (<?php echo number_format(
                                abs($difference),
                                2,
                                ",",
                                " ",
                            ); ?>€) :</strong></p>
                            <label><input type="radio" name="choix_remboursement" value="rien" checked> Ne rien demander</label><br>

                            <?php
                            $points_normaux = floor(abs($difference));
                            $points_compensation = $points_normaux * 2;
                            ?>
                            <label><input type="radio" name="choix_remboursement" value="points"> Convertir en <strong><?php echo $points_compensation; ?> points</strong> de fidélité</label>

                            <input type="hidden" name="montant_diff_abs" value="<?php echo abs(
                                $difference,
                            ); ?>">
                        </div>
                <?php } ?>

                <?php if (isset($ma_commande["plats"])) {
                    foreach ($ma_commande["plats"] as $id => $detail) { ?>
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
                <?php }
                } ?>

                <?php if (isset($ma_commande["menus"])) {
                    foreach ($ma_commande["menus"] as $id_m => $detail_m) { ?>
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

                <?php if ($ma_commande["reduction"]) { ?>
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
                    <p><strong>AJUSTEMENT DU PRIX</strong></p>
                    <p class="prix_recap"><strong><?php if ($difference > 0) {
                        echo "+" .
                            number_format($difference, 2, ",", " ") .
                            " €";
                    } else {
                        echo number_format($difference, 2, ",", " ") . " €";
                    } ?></strong></p>
                </div>

                <?php if ($difference > 0) { ?>
                    <form action='https://www.plateforme-smc.fr/cybank/index.php' method='POST' class="form-modification">
                        <input type='hidden' name='transaction' value='<?php echo $transac; ?>'>
                        <input type='hidden' name='montant' value='<?php echo $montant_a_payer; ?>'>
                        <input type='hidden' name='vendeur' value='<?php echo $vendeur; ?>'>
                        <input type='hidden' name='retour' value='<?php echo $retour; ?>'>
                        <input type='hidden' name='control' value='<?php echo $control; ?>'>
                        <input class="bouton-recommande" type='submit' value="Payer le surplus (+<?php echo number_format(
                            $difference,
                            2,
                            ",",
                            " ",
                        ); ?>€)">
                    </form>
                <?php } else { ?>
                        <input class="bouton-recommande" type="submit" name="valider_modification_negative" value="Valider les modifications">
                    </form>
                <?php } ?>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
</body>
</html>
