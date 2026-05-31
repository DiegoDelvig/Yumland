<?php
session_start();
error_reporting(0);
if (!isset($_COOKIE["client"])) {
    header("Location: index.php");
    exit();
}

$client = json_decode($_COOKIE["client"], true);
$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);

if (
    isset($data[$client["email"]]) &&
    $data[$client["email"]]["role"]["bloque"] == true
) {
    setcookie("client", "", time() - 3600);
    header("Location: index.php?msg=bloque");
    exit();
}

if (isset($_COOKIE["admin"])) {
    $client_temp = json_decode($_COOKIE["admin"], true);
} else {
    $client_temp = json_decode($_COOKIE["client"], true);
}

$commande_data = file_get_contents("donnees/commande_passe.json");
$commande = json_decode($commande_data, true);
$commande_en_cours_data = file_get_contents("donnees/commande.json");
$commande_en_cours = json_decode($commande_en_cours_data, true);
$plats = json_decode(file_get_contents("donnees/plat.json"), true);
$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);
$client = $data[$client_temp["email"]];
$mail = $client["email"];
foreach ($plats as $id => $plat) {
    $plats[$id]["vente"] = 0;
    foreach ($commande as $id_client => $cclient) {
        foreach ($cclient as $id_cmd => $cmd) {
            foreach ($cmd["plats"] as $id_plat_cmd => $plat_cmd) {
                if ($plat_cmd["name"] == $plat["name"]) {
                    $plats[$id]["vente"] += $plat_cmd["quantite"];
                }
            }
        }
    }
}
file_put_contents(
    "donnees/plat.json",
    json_encode($plats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
);
if (!isset($_COOKIE["admin"])) {
    setcookie("client", json_encode($client), time() - 3600);
    setcookie("client", json_encode($client), time() + 3600);
}

$fichier_panier = "donnees/panier_$mail.json";
if (!file_exists($fichier_panier)) {
    $panier = ["total" => 0, "reduction" => false];
    file_put_contents($fichier_panier, json_encode($panier, JSON_PRETTY_PRINT));
} else {
    $panier = json_decode(file_get_contents($fichier_panier), true);
}

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

function aff_num_cmd($num)
{
    if ($num < 10) {
        return "000" . $num;
    } elseif ($num < 100) {
        return "00" . $num;
    } elseif ($num < 1000) {
        return "0" . $num;
    } else {
        return $num;
    }
}

function aff_temps($num)
{
    if ($num < 10) {
        return "0" . $num;
    } else {
        return $num;
    }
}

if (isset($commande[$mail])) {
    foreach ($commande[$mail] as $id_cmd => $details) {
        if (isset($_REQUEST[aff_num_cmd($details["num"])])) {
            if (isset($details["plats"])) {
                foreach ($details["plats"] as $id => $pla) {
                    $panier["total"] += $pla["quantite"] * $pla["prix"];
                    if (isset($panier["plats"][$id])) {
                        $panier["plats"][$id]["quantite"] += $pla["quantite"];
                    } else {
                        $panier["plats"][$id] = $pla;
                    }
                }
            }
            if (isset($details["menus"])) {
                foreach ($details["menus"] as $id_m => $menu) {
                    $panier["total"] += $menu["quantite"] * $menu["prix"];
                    if (isset($panier["menus"][$id_m])) {
                        $panier["menus"][$id_m]["quantite"] +=
                            $menu["quantite"];
                    } else {
                        $panier["menus"][$id_m] = $menu;
                    }
                }
            }
            file_put_contents(
                $fichier_panier,
                json_encode(
                    $panier,
                    JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
                ),
            );
            header("Location: panier.php");
            exit();
        }
    }
}

if (!empty($commande_en_cours)) {
    foreach ($commande_en_cours as $id_cmd => $details) {
        $nom_bouton = "modifier_" . aff_num_cmd($details["num"]);
        if (isset($_REQUEST[$nom_bouton])) {
            header(
                "Location: modification_commande.php?cmd=" .
                    aff_num_cmd($details["num"]),
            );
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <title>Les Croquettes du Chef</title>
    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/profil.css">
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
                <?php if (
                    isset($client["role"]["restaurateur"]) &&
                    $client["role"]["restaurateur"] == true &&
                    !isset($_COOKIE["admin"])
                ) { ?>
                    <li><a href="commandes.php">Commande</a></li>
                <?php } ?>
                <?php if (
                    isset($client["role"]["livreur"]) &&
                    $client["role"]["livreur"] == true &&
                    !isset($_COOKIE["admin"])
                ) { ?>
                    <li><a href="livraisons.php">Livraison</a></li>
                <?php } ?>
                <?php if (
                    isset($client["role"]["admin"]) &&
                    $client["role"]["admin"] == true &&
                    !isset($_COOKIE["admin"])
                ) { ?>
                    <li><a href="admin.php">Administration</a></li>
                <?php } ?>
                <?php if (!isset($_COOKIE["admin"])) { ?>
                    <li><a href="menu.php">La Carte</a></li>
                    <li><a href="panier.php">🛒</a></li>
                    <li><a href="profil.php" class="active">Mon profil</a></li>
                <?php } ?>
                <?php if (isset($_COOKIE["admin"])) { ?>
                    <li><a href="admin.php" class="btn">Retour page admin</a></li>
                <?php } else { ?>
                    <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                    <li><a href="logout.php" class="btn">Déconnexion</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>

    <main class="container">
        <aside class="sidebar-perso">
            <div class="titre-modif">
                <h3>Mes informations</h3>
                <a href="modification.php" class="carre-crayon" aria-label="Modifier le profil">✏️</a>
            </div>
            <div class="info-details">
                <p>NOM :</p>
                <p><strong><?php echo $client["fname"]; ?></strong></p>
                <p>PRÉNOM :</p>
                <p><strong><?php echo $client["name"]; ?></strong></p>
                <p>ADRESSE :</p>
                <p><strong><?php echo $client["adr"]; ?></strong></p>
                <p>TÉLÉPHONE :</p>
                <p><strong><?php echo $client["tel"]; ?></strong></p>
                <?php if ($client["infocomp"] != "") { ?>
                    <p>INFOS COMPLÉMENTAIRES :</p>
                    <p><strong><?php echo $client["infocomp"]; ?></strong></p>
                <?php } ?>
            </div>
        </aside>

        <section class="contenu-profil">
            <h1>Mon Profil</h1>

            <div class="carte-info">
                <h3>Programme De Fidélité</h3>
                <p>
                    N° de carte de fidélité :<br />
                    <span class="numero-carte"><?php aff_num_cmd_ou_fidelite(
                        $client["numero_fidelite"],
                        2,
                    ); ?></span>
                </p>
                <?php
                $pourcentage = (100 * $client["point_fidelite"]) / 300;
                if ($pourcentage >= 100) {
                    $pourcentage = 100;
                }
                ?>
                <p>Vous avez <strong><?php echo $client[
                    "point_fidelite"
                ]; ?> points</strong></p>
                <div class="barre">
                    <div class="avancee" style="width:<?php echo $pourcentage; ?>%;"></div>
                </div>
                <?php if ($pourcentage < 100) { ?>
                    <p><small>Encore <?php echo 300 -
                        $client[
                            "point_fidelite"
                        ]; ?> points avant la réduction de 25% sur la commande suivante !</small></p>
                <?php } else { ?>
                    <p><small>🎉 Félicitations ! Vous avez débloqué une réduction de 25% sur votre prochaine commande !</small></p>
                <?php } ?>
            </div>

            <div class="carte-info">
                <h3>Commandes en cours ...</h3>
                <?php
                $var = 0;
                if (!empty($commande_en_cours)) {
                    foreach (
                        $commande_en_cours
                        as $id_cmd_en_cours => $details_cmd_en_cours
                    ) {
                        if (
                            $details_cmd_en_cours["mail"] == $mail &&
                            $details_cmd_en_cours["etat"]["cuisinee"] == false
                        ) {
                            $var = 1; ?>
                            <div class="commande">
                                <div class="numero">
                                    <strong>Commande n°<?php aff_num_cmd_ou_fidelite(
                                        $details_cmd_en_cours["num"],
                                        1,
                                    ); ?> (<?php echo aff_temps(
     $details_cmd_en_cours["date"]["jour"],
 ) .
     "/" .
     aff_temps($details_cmd_en_cours["date"]["mois"]) .
     "/" .
     aff_temps($details_cmd_en_cours["date"]["annee"]) .
     " à " .
     aff_temps($details_cmd_en_cours["date"]["heure"]) .
     ":" .
     aff_temps($details_cmd_en_cours["date"]["minute"]); ?>)</strong>
                                    <form method="POST">
                                        <button name="modifier_<?php aff_num_cmd_ou_fidelite(
                                            $details_cmd_en_cours["num"],
                                            1,
                                        ); ?>" class="bouton-recommande">Modifier</button>
                                    </form>
                                </div>
                                <?php foreach (
                                    $details_cmd_en_cours["plats"]
                                    as $produit
                                ) { ?>
                                    <p><?php echo $produit["quantite"] .
                                        "x       -" .
                                        $produit[
                                            "name"
                                        ]; ?> - <?php echo number_format(
     $produit["quantite"] * $produit["prix"],
     2,
     ",",
     " ",
 ); ?>€</p>
                                <?php } ?>
                                <?php foreach (
                                    $details_cmd_en_cours["menus"]
                                    as $produit
                                ) { ?>
                                    <p><?php echo $produit["quantite"] .
                                        "x       -" .
                                        $produit[
                                            "name"
                                        ]; ?> - <?php echo number_format(
     $produit["quantite"] * $produit["prix"],
     2,
     ",",
     " ",
 ); ?>€</p>
                                <?php } ?>
                            </div>
                        <?php
                        }
                    }
                    foreach (
                        $commande_en_cours
                        as $id_cmd_en_cours => $details_cmd_en_cours
                    ) {
                        if (
                            $details_cmd_en_cours["mail"] == $mail &&
                            $details_cmd_en_cours["etat"]["cuisinee"] == true
                        ) {
                            $var = 1; ?>
                            <div class="commande">
                                <div class="numero">
                                    <strong>Commande n°<?php aff_num_cmd_ou_fidelite(
                                        $details_cmd_en_cours["num"],
                                        1,
                                    ); ?> (<?php echo aff_temps(
     $details_cmd_en_cours["date"]["jour"],
 ) .
     "/" .
     aff_temps($details_cmd_en_cours["date"]["mois"]) .
     "/" .
     aff_temps($details_cmd_en_cours["date"]["annee"]) .
     " à " .
     aff_temps($details_cmd_en_cours["date"]["heure"]) .
     ":" .
     aff_temps($details_cmd_en_cours["date"]["minute"]); ?>)</strong>
                                </div>
                                <?php foreach (
                                    $details_cmd_en_cours["plats"]
                                    as $produit
                                ) { ?>
                                    <p><?php echo $produit["quantite"] .
                                        "x       -" .
                                        $produit[
                                            "name"
                                        ]; ?> - <?php echo number_format(
     $produit["quantite"] * $produit["prix"],
     2,
     ",",
     " ",
 ); ?>€</p>
                                <?php } ?>
                                <?php foreach (
                                    $details_cmd_en_cours["menus"]
                                    as $produit
                                ) { ?>
                                    <p><?php echo $produit["quantite"] .
                                        "x       -" .
                                        $produit[
                                            "name"
                                        ]; ?> - <?php echo number_format(
     $produit["quantite"] * $produit["prix"],
     2,
     ",",
     " ",
 ); ?>€</p>
                                <?php } ?>
                            </div>
                        <?php
                        }
                    }
                }
                if ($var == 0) { ?>
                    <div class="commande">
                        <div class="numero">
                            <strong><br>Vous n'avez pas de commande en cours.</strong>
                            <a href="menu.php" class="bouton-recommande" style="text-decoration: none; display: inline-block; text-align: center;">Commander</a>
                        </div>
                    </div>
                <?php }
                ?>
            </div>

            <div class="carte-info">
                <h3>Anciennes commandes 🛍️</h3>
                <?php if (!empty($commande[$mail])) {
                    foreach ($commande[$mail] as $id_cmd => $details) { ?>
                        <div class="commande">
                            <div class="numero">
                                <strong>Commande n°<?php aff_num_cmd_ou_fidelite(
                                    $details["num"],
                                    1,
                                ); ?> (<?php echo aff_temps(
     $details["date"]["jour"],
 ) .
     "/" .
     aff_temps($details["date"]["mois"]) .
     "/" .
     aff_temps($details["date"]["annee"]) .
     " à " .
     aff_temps($details["date"]["heure"]) .
     ":" .
     aff_temps($details["date"]["minute"]); ?>)</strong>
                                <form method="POST">
                                    <button name="<?php echo aff_num_cmd(
                                        $details["num"],
                                    ); ?>" class="bouton-recommande">Recommander</button>
                                </form>
                            </div>
                            <?php foreach ($details["plats"] as $produit) { ?>
                                <p><?php echo $produit["quantite"] .
                                    "x       -" .
                                    $produit[
                                        "name"
                                    ]; ?> - <?php echo number_format(
     $produit["quantite"] * $produit["prix"],
     2,
     ",",
     " ",
 ); ?>€</p>
                            <?php } ?>
                            <?php foreach ($details["menus"] as $produit) { ?>
                                <p><?php echo $produit["quantite"] .
                                    "x       -" .
                                    $produit[
                                        "name"
                                    ]; ?> - <?php echo number_format(
     $produit["quantite"] * $produit["prix"],
     2,
     ",",
     " ",
 ); ?>€</p>
                            <?php } ?>
                        </div>
                    <?php }
                } else {
                     ?>
                    <div class="commande">
                        <div class="numero">
                            <strong><br>Vous n'avez pas encore passé de commande.</strong>
                            <a href="menu.php" class="bouton-recommande" style="text-decoration: none; display: inline-block; text-align: center;">Commander</a>
                        </div>
                    </div>
                <?php
                } ?>
            </div>
        </section>

        <div id="modal-edit-profile" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Modifier mon profil</h2>
                    <button type="button" class="modal-close" id="close-modal" aria-label="Fermer">&times;</button>
                </div>
                <form id="form-edit-profile" class="modal-form">
                    <div class="form-group">
                        <label for="edit-name">NOM *</label>
                        <input type="text" id="edit-name" name="name" required maxlength="100" value="<?php echo htmlspecialchars(
                            $client["name"],
                        ); ?>">
                        <small class="error-message" id="error-name"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit-fname">PRÉNOM *</label>
                        <input type="text" id="edit-fname" name="fname" required maxlength="100" value="<?php echo htmlspecialchars(
                            $client["fname"],
                        ); ?>">
                        <small class="error-message" id="error-fname"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit-adr">ADRESSE *</label>
                        <input type="text" id="edit-adr" name="adr" required maxlength="200" value="<?php echo htmlspecialchars(
                            $client["adr"],
                        ); ?>">
                        <small class="error-message" id="error-adr"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit-tel">TÉLÉPHONE *</label>
                        <input type="tel" id="edit-tel" name="tel" required pattern="[0-9+ ]+" maxlength="20" value="<?php echo htmlspecialchars(
                            $client["tel"],
                        ); ?>">
                        <small class="error-message" id="error-tel"></small>
                    </div>
                    <div class="form-group">
                        <label for="edit-infocomp">INFOS COMPLÉMENTAIRES</label>
                        <textarea id="edit-infocomp" name="infocomp" maxlength="500"><?php echo htmlspecialchars(
                            $client["infocomp"],
                        ); ?></textarea>
                        <small class="char-count" id="char-count">0/500</small>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-save" id="btn-save">
                            <span id="save-text">Sauvegarder</span>
                            <span id="save-loading" style="display: none;">⏳ Sauvegarde...</span>
                        </button>
                        <button type="button" class="btn-cancel" id="btn-cancel-form">Annuler</button>
                    </div>
                    <div id="form-message" class="form-message"></div>
                </form>
                <noscript>
                    <p style="text-align: center; margin-top: 20px;">
                        <a href="modification.php" class="btn">Aller à la page de modification</a>
                    </p>
                </noscript>
            </div>
        </div>
        <div id="modal-overlay" class="modal-overlay" style="display: none;"></div>
    </main>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
    <script src="js/edit-profile.js" defer></script>
</body>
</html>
