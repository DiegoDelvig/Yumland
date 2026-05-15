<?php
error_reporting(0);
$client = json_decode($_COOKIE["client"], true);
if (!isset($_COOKIE["client"])) {
    header("Location: index.php");
}
if (
    empty($_REQUEST["nname"]) ||
    empty($_REQUEST["nfname"]) ||
    empty($_REQUEST["nadr"]) ||
    empty($_REQUEST["ntel"])
) {
} else {
    $file = "donnees/data.json";
    if (file_exists($file)) {
        $client_passe = file_get_contents($file);
        $data = json_decode($client_passe, true);
    }
    $name = $_REQUEST["nname"];
    $fname = $_REQUEST["nfname"];
    $adr = $_REQUEST["nadr"];
    $tel = $_REQUEST["ntel"];
    $infocomp = $_REQUEST["ninfocomp"];
    $new_user = [
        "name" => $name,
        "fname" => $fname,
        "adr" => $adr,
        "tel" => $tel,
        "infocomp" => $infocomp,
        "email" => $client["email"],
        "code" => $client["code"],
        "point_fidelite" => $client["point_fidelite"],
        "numero_fidelite" => $client["numero_fidelite"],
        "role" => [
            "livreur" => $client["role"]["livreur"],
            "admin" => $client["role"]["admin"],
            "bloque" => $client["role"]["bloque"],
            "restaurateur" => $client["role"]["restaurateur"],
        ],
    ];
    $data[$client["email"]] = $new_user;
    file_put_contents(
        "donnees/data.json",
        json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    );
    setcookie("client", json_encode($data[$client["email"]]), time() - 3600);
    setcookie("client", json_encode($data[$client["email"]]), time() + 3600);
    header("Location: profil.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link id="theme-css" rel="stylesheet" href="css/variables.css">
        <link rel="stylesheet" href="css/client.css">
        <link rel="stylesheet" href="css/modification.css">
        <link href="assets/Logo projet.png" rel="icon">
        <script src="js/charte.js></script>
        <title>Inscription - Les Croquettes du Chef</title>

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
                <li><a href="inscription.php" class="active">Modifaction de Compte</a></li>
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li><a href="profil.php" class="btn">Profil</a></li>
            </ul>
        </nav>
        </header>
        <h1>MODIFICATION DE COMPTE</h1>
                <form action="" method="post" target="_top" id="formulaire" name="connexion">
                    <div class="rect_bleu">
                        <img class="logo_login" src="assets/Logo projet.png" alt="logo de notre site de vente">

                        <label for="idname" class="input_label">Nom :</label>
                        <input type="text" name="nname" id="idname" class="login_case_name" value="<?php echo $client[
                            "name"
                        ]; ?>">

                        <label for="idfname" class="input_label">Prénom :</label>
                        <input type="text" name="nfname" id="idfname" class="login_case_fname" value="<?php echo $client[
                            "fname"
                        ]; ?>">

                        <label for="idadr" class="input_label">Adresse postale :</label>
                        <input type="text" name="nadr" id="idadr" class="login_case_adr" value="<?php echo $client[
                            "adr"
                        ]; ?>">

                        <label for="idtel" class="input_label">Numéro de téléphone :</label>
                        <input type="tel" name="ntel" id="idtel" class="login_case_tel" value="<?php echo $client[
                            "tel"
                        ]; ?>">

                        <label for="idinfocomp" class="input_label">Informations complémentaires :</label>
                        <textarea name="ninfocomp" id="idinfocomp" class="login_case_infocomp"><?php echo $client[
                            "infocomp"
                        ]; ?></textarea>

                        <input type="submit" value="Valider" class="login_submit">
                    </div>
                </form>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
    </body>
</html>
