<?php
if (!isset($_COOKIE["client"])) {
    header("Location: index.php");
    exit();
}

$client = json_decode($_COOKIE["client"], true);
require_once __DIR__ . '/config.php';
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

if (
    isset($data[$client["email"]]) &&
    $data[$client["email"]]["role"]["bloque"] == true
) {
    setcookie("client", "", time() - 3600);
    header("Location: index.php?msg=bloque");
    exit();
}

if (isset($_COOKIE["admin"])) {
    setcookie("admin", json_encode($data[$mail]), time() - 3600);
    header("Location: admin.php");
    exit();
}
$client = json_decode($_COOKIE["client"], true);
$file = file_get_contents(DATA_DIR . '/data.json');
$data = json_decode($file, true);

if ($data[$client["email"]]["role"]["admin"] == false) {
    header("Location: profil.php");
}
function aff_role($client)
{
    if ($client["role"]["bloque"] == true) {
        echo "BLOQUE";
    } elseif ($client["role"]["livreur"] == true) {
        echo "Livreur";
        if ($client["role"]["admin"] == true) {
            echo "&Admin";
        }
        if ($client["role"]["restaurateur"] == true) {
            echo "&Restaurateur";
        }
    } elseif ($client["role"]["admin"] == true) {
        echo "Admin";
        if ($client["role"]["livreur"] == true) {
            echo "&Livreur";
        }
        if ($client["role"]["restaurateur"] == true) {
            echo "&Restaurateur";
        }
    } elseif ($client["role"]["restaurateur"] == true) {
        echo "Restaurateur";
        if ($client["role"]["admin"] == true) {
            echo "&Admin";
        }
        if ($client["role"]["livreur"] == true) {
            echo "&Livreur";
        }
    } else {
        echo "Client";
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
foreach ($data as $pers) {
    if (isset($_REQUEST["bloque_" . $pers["numero_fidelite"]])) {
        if ($data[$pers["email"]]["role"]["bloque"] == true) {
            $data[$pers["email"]]["role"]["bloque"] = false;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        } else {
            $data[$pers["email"]]["role"]["bloque"] = true;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        }
    }
    if (isset($_REQUEST["restaurateur_" . $pers["numero_fidelite"]])) {
        if ($data[$pers["email"]]["role"]["restaurateur"] == false) {
            $data[$pers["email"]]["role"]["restaurateur"] = true;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        } else {
            $data[$pers["email"]]["role"]["restaurateur"] = false;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        }
    }
    if (isset($_REQUEST["admin_" . $pers["numero_fidelite"]])) {
        if ($data[$pers["email"]]["role"]["admin"] == false) {
            $data[$pers["email"]]["role"]["admin"] = true;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        } else {
            $data[$pers["email"]]["role"]["admin"] = false;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        }
    }
    if (isset($_REQUEST["livreur_" . $pers["numero_fidelite"]])) {
        if ($data[$pers["email"]]["role"]["livreur"] == false) {
            $data[$pers["email"]]["role"]["livreur"] = true;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        } else {
            $data[$pers["email"]]["role"]["livreur"] = false;
            file_put_contents(
                DATA_DIR . '/data.json',
                json_encode($data, JSON_PRETTY_PRINT),
            );
            header("Location: admin.php");
            exit();
        }
    }
    if (isset($_REQUEST["profil_" . $pers["numero_fidelite"]])) {
        setcookie("admin", json_encode($data[$pers["email"]]), time() + 3600);
        header("Location: profil.php");
        exit();
    }
    if (isset($_REQUEST["supprimer_" . $pers["numero_fidelite"]])) {
        $commande_data = file_get_contents(DATA_DIR . '/commande.json');
        $commande = json_decode($commande_data, true);
        $commande_passees_data = file_get_contents(
            DATA_DIR . '/commande_passe.json',
        );
        $commande_passees = json_decode($commande_passees_data, true);
        if (file_exists(DATA_DIR . '/client_spprimes.json')) {
            $client_supp_end = json_decode(
                file_get_contents(DATA_DIR . '/client_spprimes.json'),
                true,
            );
        } else {
            $client_supp_end = [];
        }
        if (isset($commande_passees[$pers["email"]])) {
            $client_supp = [
                "data" => $pers,
                "commandes_passees" => $commande_passees[$pers["email"]],
            ];
        } else {
            $client_supp = ["data" => $pers, "commandes_passees" => []];
        }
        $client_supp_end[$pers["email"]] = $client_supp;
        unset($data[$pers["email"]]);
        unset($commande_passees[$pers["email"]]);
        foreach ($commande as $id_cmd => $detail) {
            if ($detail["mail"] == $pers["email"]) {
                unset($commande[aff_num_cmd_sans_echo($detail["num"])]);
            }
        }
        file_put_contents(
            DATA_DIR . '/client_spprimes.json',
            json_encode(
                $client_supp_end,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
            ),
        );
        file_put_contents(
            DATA_DIR . '/data.json',
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        file_put_contents(
            DATA_DIR . '/commande.json',
            json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
        );
        file_put_contents(
            DATA_DIR . '/commande_passe.json',
            json_encode(
                $commande_passees,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
            ),
        );
        header("Location: admin.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Croquettes du Chef</title>

    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/client.css">
    <link href="assets/Logo projet.png" rel="icon">
    <script src="js/charte.js" defer></script>
    <script src="js/admin.js" defer></script>


</head>
<body>
<header>
        <div class="logo">
            <img src="assets/Logo projet.png" alt="Logo" class="header-logo" style="height: 35px;">
            Espace Administrateur
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menu.php">La Carte</a></li>
                <li><a href="admin.php" class="active">Admin</a></li>
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
<section class="titre-admin">
    <h1>Utilisateurs</h1>
    <div class="filtres-admin">
        <label for="filtres-choix">Filtrer :</label>
        <select name="type-utilisateur" id="filtres-choix">
            <option value="tous">Tous</option>
            <option value="clients">Tous les clients</option>
            <option value="commandes">Clients ayant déjà commandé</option>
            <option value="livreurs">Livreurs</option>
            <option value="administrateurs">Administrateurs</option>
        </select>
    </div>
</section>
<main>
<table border="1">
    <tr>
        <th>Email</th><th>Nom</th><th>Rôle</th><th>Actions</th>
    </tr>
    <?php foreach ($data as $pers) { ?>
    <tr>
        <td><?php echo $pers["email"]; ?></td>
        <td><?php echo $pers["name"] . " " . $pers["fname"]; ?></td>
        <td><?php aff_role($pers); ?></td>
        <td>
            <form method="POST" action="admin.php">
                <?php if ($pers["email"] != "nathaneviname@gmail.com") { ?>
                    <p><button name="profil_<?php echo $pers[
                        "numero_fidelite"
                    ]; ?>">Aller sur le profil</button></p>
                    <p>
                        <button type="button"
                                class="btn-bloque <?php if (
                                    $pers["role"]["bloque"] == true
                                ) {
                                    echo "bloque";
                                } ?>"
                                onclick="bloquerUtilisateur('<?php echo $pers[
                                    "email"
                                ]; ?>', this)">
                            <?php if ($pers["role"]["bloque"] == true) {
                                echo "🔓 Débloquer";
                            } else {
                                echo "🔒 Bloquer";
                            } ?>
                        </button>
                    </p>
                <?php } ?>
                <p><button name="restaurateur_<?php echo $pers[
                    "numero_fidelite"
                ]; ?>"><?php if ($pers["role"]["restaurateur"] == true) {
    echo "Retirer restaurateur";
} else {
    echo "Passer en restaurateur";
} ?></button></p>
                <?php if ($pers["email"] != "nathaneviname@gmail.com") { ?>
                    <p><button name="admin_<?php echo $pers[
                        "numero_fidelite"
                    ]; ?>"><?php if ($pers["role"]["admin"] == true) {
    echo "Retirer admin";
} else {
    echo "Passer en admin";
} ?></button></p>
                <?php } ?>
                <p><button name="livreur_<?php echo $pers[
                    "numero_fidelite"
                ]; ?>"><?php if ($pers["role"]["livreur"] == true) {
    echo "Retirer livreur";
} else {
    echo "Passer en livreur";
} ?></button></p>
                <?php if ($pers["email"] != "nathaneviname@gmail.com") { ?>
                    <p><button name="supprimer_<?php echo $pers[
                        "numero_fidelite"
                    ]; ?>">Supprimer</button></p>
                <?php } ?>
            </form>
        </td>
    </tr>
    <?php } ?>
</table>
</main>
<footer>
    <p>&copy; 2026 Les Croquettes du Chef - Espace Pro</p>
</footer>
</body>
</html>
