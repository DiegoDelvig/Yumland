<?php
error_reporting(0);
if (!isset($_COOKIE["client"])) {
    header("Location: index.php");
}
$client = json_decode($_COOKIE["client"], true);
$liste_client_data = file_get_contents("donnees/data.json");
$liste_client = json_decode($liste_client_data, true);
$commande_data = file_get_contents("donnees/commande.json");
$commande = json_decode($commande_data, true);
if ($liste_client[$client["email"]]["role"]["bloque"] == true) {
    setcookie("client", "", time() - 3600);
    header("Location: index.php");
}
if ($liste_client[$client["email"]]["role"]["restaurateur"] == false) {
    header("Location: profil.php");
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
function aff_num_cmd_sans_echo($num)
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
function get_livreurs($liste_client)
{
    $livreurs = [];
    foreach ($liste_client as $email => $data) {
        if (
            $data["role"]["livreur"] == true &&
            $data["role"]["bloque"] == false
        ) {
            $livreurs[$email] = $data["name"] . " " . $data["fname"];
        }
    }
    return $livreurs;
}
$nb_cmd_cuisine = 0;
$nb_cmd_livraison = 0;
if (!empty($commande)) {
    foreach ($commande as $id_cmd => $detail) {
        if ($detail["etat"]["cuisinee"] == true) {
            $nb_cmd_livraison++;
        } else {
            $nb_cmd_cuisine++;
        }
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
if (!empty($commande)) {
    foreach ($commande as $id_cmd => $detail) {
        if (
            $detail["etat"]["cuisinee"] == false &&
            isset($_REQUEST[aff_num_cmd_sans_echo($detail["num"])])
        ) {
            $commande[aff_num_cmd_sans_echo($detail["num"])]["etat"][
                "cuisinee"
            ] = true;
            file_put_contents(
                "donnees/commande.json",
                json_encode($commande, JSON_PRETTY_PRINT),
            );
            header("Location: commandes.php");
            exit();
        }
        if (
            isset(
                $_REQUEST[
                    "assign_livreur_" . aff_num_cmd_sans_echo($detail["num"])
                ],
            )
        ) {
            $livreur_email =
                $_REQUEST[
                    "select_livreur_" . aff_num_cmd_sans_echo($detail["num"])
                ];
            if (!empty($livreur_email)) {
                $commande[aff_num_cmd_sans_echo($detail["num"])][
                    "livreur"
                ] = $livreur_email;
                file_put_contents(
                    "donnees/commande.json",
                    json_encode($commande, JSON_PRETTY_PRINT),
                );
                header("Location: commandes.php");
                exit();
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
    <title>Gestion Commandes - Les Croquettes du Chef</title>

    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/client.css">
    <link rel="stylesheet" href="css/accueil.css">
    <link rel="stylesheet" href="css/commandes.css">
    <link href="assets/Logo projet.png" rel="icon">
    <script src="js/charte.js" defer></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/Logo projet.png" alt="Logo" class="header-logo" style="height: 35px;">
            Espace Cuisine 👨‍🍳
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Voir le site</a></li>
                <li><a href="commandes.php" class="active">Commandes</a></li>
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li><a href="<?php if (isset($_COOKIE["client"])) {
                    echo "profil.php";
                } else {
                    echo "login.php";
                } ?>" class="btn">
                        <?php if (isset($_COOKIE["client"])) {
                            echo "Profil";
                        } else {
                            echo "Connexion";
                        } ?>
                    </a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="en-tete-tableau">
            <h1>Suivi du Service 🛎️</h1>
            <p>Gérez la préparation et le départ des commandes en temps réel.</p>
        </section>
        <div class="conteneur-tableau">

            <section class="colonne-commandes a-preparer">
                <div class="en-tete-colonne">
                    <h2>🔥 À Préparer (<?php echo $nb_cmd_cuisine; ?>)</h2>
                </div>
                <div class="liste-commandes">
                    <?php
                    $livreurs = get_livreurs($liste_client);
                    foreach ($commande as $cmd => $detail) { ?>
                            <?php if ($detail["etat"]["cuisinee"] == false) { ?>
                                <article class="carte-commande">
                                    <div class="en-tete-commande">
                                        <span class="numero-commande">#CMD-<?php aff_num_cmd_ou_fidelite(
                                            $detail["num"],
                                            1,
                                        ); ?></span>
                                        <span class="<?php if (
                                            $detail["temps"] < 15
                                        ) {
                                            echo "timer";
                                        } else {
                                            echo "timer alert";
                                        } ?>">🕒 <?php echo $detail[
    "temps"
]; ?> min</span>
                                    </div>
                                    <div class="infos-client">
                                        <strong>Client : </strong><?php echo $liste_client[
                                            $detail["mail"]
                                        ]["name"] .
                                            " " .
                                            $liste_client[$detail["mail"]][
                                                "fname"
                                            ]; ?>
                                    </div>
                                    <ul class="liste-articles">
                                        <?php foreach (
                                            $detail["plats"]
                                            as $plats => $plat
                                        ) {
                                            if (
                                                $detail["etat"]["cuisinee"] ==
                                                true
                                            ) {
                                                continue;
                                            } ?>
                                            <li>
                                                <?php echo $plat["quantite"] .
                                                    "x " .
                                                    $plat["name"]; ?>
                                            </li>
                                        <?php
                                        } ?>
                                        <?php foreach (
                                            $detail["menus"]
                                            as $plats => $plat
                                        ) {
                                            if (
                                                $detail["etat"]["cuisinee"] ==
                                                true
                                            ) {
                                                continue;
                                            } ?>
                                            <li>
                                                <?php echo $plat["quantite"] .
                                                    "x " .
                                                    $plat["name"]; ?>
                                            </li>
                                        <?php
                                        } ?>
                                    </ul>
                                    <div class="section-livreur">
                                        <form method="POST" action="commandes.php">
                                            <div class="groupe-assignation">
                                                <label for="select_livreur_<?php aff_num_cmd_ou_fidelite(
                                                    $detail["num"],
                                                    1,
                                                ); ?>" class="label-livreur">
                                                    📦 Assigner un livreur :
                                                </label>
                                                <select name="select_livreur_<?php aff_num_cmd_ou_fidelite(
                                                    $detail["num"],
                                                    1,
                                                ); ?>"
                                                    id="select_livreur_<?php aff_num_cmd_ou_fidelite(
                                                        $detail["num"],
                                                        1,
                                                    ); ?>"
                                                    class="select-livreur">
                                                        <option value="">-- Choisir un livreur --</option>
                                                        <?php foreach (
                                                            $livreurs
                                                            as $email => $nom
                                                        ): ?>
                                                            <option value="<?php echo $email; ?>"
                                                                <?php if (
                                                                    $detail[
                                                                        "livreur"
                                                                    ] == $email
                                                                ) {
                                                                    echo "selected";
                                                                } ?>>
                                                                    <?php echo $nom; ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                        <button type="submit"
                                                                name="assign_livreur_<?php aff_num_cmd_ou_fidelite(
                                                                    $detail[
                                                                        "num"
                                                                    ],
                                                                    1,
                                                                ); ?>"
                                                                class="btn-assign-livreur">✓ Assigner</button>
                                                    </div>
                                                        </form>
                                                    </div>
                                    <div class="actions-commande">
                                        <form method="POST" action="commandes.php">
                                            <button name="<?php aff_num_cmd_ou_fidelite(
                                                $detail["num"],
                                                1,
                                            ); ?>" class="btn-action ready">✅ Prête à partir</button>
                                        </form>
                                    </div>
                                </article>
                            <?php } ?>
                    <?php }
                    ?>
                </div>
            </section>
            <section class="colonne-commandes livraison">
                <div class="en-tete-colonne">
                    <h2>🚀 En Livraison (<?php echo $nb_cmd_livraison; ?>)</h2>
                </div>
                <div class="liste-commandes">
                    <?php foreach ($commande as $cmd => $detail) { ?>
                            <?php if ($detail["etat"]["cuisinee"] == true) { ?>
                                <article class="carte-commande delivering">
                                    <div class="en-tete-commande">
                                        <span class="numero-commande">#CMD-<?php aff_num_cmd_ou_fidelite(
                                            $detail["num"],
                                            1,
                                        ); ?></span>
                                        <span class="status-badge">🛵 En route</span>
                                    </div>
                                    <div class="infos-client">
                                        <strong>Livreur : </strong><?php echo $liste_client[
                                            $detail["livreur"]
                                        ]["name"] .
                                            " " .
                                            $liste_client[$detail["livreur"]][
                                                "fname"
                                            ]; ?>
                                    </div>
                                    <div class="adresse-livraison">
                                        📍 <?php echo $liste_client[
                                            $detail["mail"]
                                        ]["adr"]; ?>
                                    </div>
                                    <div class="actions-commande">
                                        <button name="<?php aff_num_cmd_ou_fidelite(
                                            $detail["num"],
                                            1,
                                        ); ?>" class="btn-action info">Voir détails</button>
                                    </div>
                                </article>
                            <?php } ?>
                    <?php } ?>
                </div>
            </section>
        </div>
    </main>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Pro</p>
    </footer>
</body>
</html>
