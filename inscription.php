<?php
require_once __DIR__ . '/config.php';
    error_reporting(0);
    $message = "";
    $error = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Vérif des inputs demandés
        $required = ["nname", "nfname", "nadr", "ntel", "nemail", "ncode"];
        $missing = false;
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $missing = true;
                break;
            }
        }
        if ($missing) {
            $error = "Veuillez remplir tous les champs obligatoires.";
        } else {
            $file = DATA_DIR . '/data.json';
            $commande = DATA_DIR . '/commande.json';
            
            // Charger les clients 
            if (file_exists($file)) {
                $client_passe = file_get_contents($file);
                $data = json_decode($client_passe, true);

                if (!is_array($data)) { $data = []; }
                $dernier_client = end($data);
                $num = !empty($dernier_client["numero_fidelite"]) ? $dernier_client["numero_fidelite"] + 1 : 1;
            } else {
                $data = [];
                $num = 1; 
            }
                

            $name = trim($_POST["nname"]);
            $fname = trim($_POST["nfname"]);
            $adr = trim($_POST["nadr"]);
            $tel = trim($_POST["ntel"]);
            $infocomp = trim($_POST["ninfocomp"]);
            $email = trim($_POST["nemail"]);
            $code = trim($_POST["ncode"]);

            // Vérif email existe
            if (isset($data[$email])) {
                $error = "Un compte existe déjà pour cette adresse e-mail.";
            } else {
                $new_user = array(
                    "name" => $name,
                    "fname" => $fname,
                    "adr" => $adr,
                    "tel" => $tel,
                    "infocomp" => $infocomp,
                    "email" => $email,
                    "code" => password_hash($code, PASSWORD_DEFAULT),
                    "point_fidelite" => 0,
                    "numero_fidelite" => $num,
                    "role" => array("livreur"=>false, "admin"=>false, "bloque"=>false, "restaurateur"=>false)
                );
                $data[$email] = $new_user;

                // Charger/initialiser les commandes
                if (file_exists($commande)) {
                    $commande_passe = file_get_contents($commande);
                    $data_commande = json_decode($commande_passe, true);

                    if (!is_array($data_commande)) {
                        $data_commande = [];
                    }
                    $data_commande[$email] = (object) [];

                    // Sauv. les fichiers
                    $sauv1 = file_put_contents(DATA_DIR . '/data.json', json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    $sauv2 = file_put_contents(DATA_DIR . '/commande.json', json_encode($data_commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    
                    if ($sauv1 === false || $sauv2 === false) {
                        $error = "Une erreur est survenue lors de l'enregistrement. Veuillez réessayer.";
                    } else {
                        $message = "Inscription réussie. Vous pouvez maintenant vous connecter.";

                        // Vider POST
                        $_POST = array();
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html> 
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link id="theme-css" rel="stylesheet" href="css/variables.css">
        <link rel="stylesheet" href="css/client.css">
        <link rel="stylesheet" href="css/inscription.css">
        <link href="assets/Logo projet.png" rel="icon">
        <script src="js/charte.js" defer></script>
        <script src="js/validation.js" defer></script>
        <script src="js/inscription.js" defer></script>
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
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li><a href="inscription.php" class="active">Inscription</a></li>
            </ul>
        </nav>
        </header>
        <h1>INSCRIPTION</h1>
        <?php if(!empty($message)): ?>
            <div class="message success" role="alert"><a class="message-link" href="login.php"><?=htmlspecialchars($message)?></a></div>
        <?php endif; ?>
        <?php if(!empty($error)): ?>
            <div class="message error" role="alert"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>
        <form action="" method="post" target="_top" id="formulaire" name="connexion">
            <div class="rect_bleu">
                <img class="logo_login" src="assets/Logo projet.png" alt="logo de notre site de vente">

                <input type="text" name="nname" id="idname" class="login_case_name" placeholder="   Prénom" maxlength="50">
                <span class="compteur" id="compteur_idname">0 / 50 caractères</span>
                <span class="erreur_champ" id="erreur_idname"></span>


                <input type="text" name="nfname" id="idfname" class="login_case_fname" placeholder="   Nom" maxlength="50">
                <span class="compteur" id="compteur_idfname">0 / 50 caractères</span>
                <span class="erreur_champ" id="erreur_idfname"></span>


                <input type="text" name="nadr" id="idadr" class="login_case_adr" placeholder="   Adresse Postale">
                <span class="erreur_champ" id="erreur_idadr"></span>


                <input type="tel" name="ntel" id="idtel" class="login_case_tel" placeholder="   Numéro de téléphone" maxlength="15">
                <span class="compteur" id="compteur_idtel">0 / 14 caractères</span>
                <span class="erreur_champ" id="erreur_idtel"></span>


                <textarea name="ninfocomp" id="idinfocomp" class="login_case_infocomp" placeholder="   Information complémentaire"></textarea>


                <input type="email" name="nemail" id="idemail" class="login_case_email" placeholder="   Adresse email" maxlength="100">
                <span class="compteur" id="compteur_idemail">0 / 100 caractères</span>
                <span class="erreur_champ" id="erreur_idemail"></span>

                <!--classe mdp avec icone cacher -->
                <div class="conteneur_mdp">
                    <input type="password" name="ncode" id="idcode" class="login_case_code" placeholder="   Mot de passe" maxlength="50">
                    <button type="button" id="btn_oeil" class="btn_oeil" onclick="basculerVisibiliteMotDePasse();">👁️</button>
                </div>
                <span class="compteur" id="compteur_idcode">0 / 50 caractères</span>
                <span class="erreur_champ" id="erreur_idcode"></span>
                
                <input type="submit" value="S'inscrire" class="login_submit">
            </div>
        </form>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
    </body>
</html>
