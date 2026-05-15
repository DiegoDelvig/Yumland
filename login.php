<?php 
    error_reporting(0);
    if(empty($_REQUEST['nemail'])){
    }
    else{
        $file= "donnees/data.json";
        if(file_exists($file)){
            $data=file_get_contents($file);
            $data=json_decode($data, true);
            $mail=$_REQUEST['nemail'];
            if(isset($data[$mail])){
                $mdp=$_REQUEST['ncode'];
                if(password_verify($mdp, $data[$mail]['code']) && $data[$mail]['role']['bloque']!=true){
                    if(!file_exists("donnees/panier_$mail.json")){
                        $panier_passe_data =file_get_contents("donnees/panier.json");
                        $panier_passe = json_decode($panier_passe_data, true); 
                        if(isset($panier_passe[$mail])){
                            $panier=$panier_passe[$mail];
                        }
                        else{
                            $panier=array("total"=>0, "reduction"=>false);
                        }
                        $panier_data="donnees/panier_$mail.json";
                        file_put_contents($panier_data, json_encode($panier, JSON_PRETTY_PRINT));
                    }
                    setcookie("client", json_encode($data[$mail]), time()+3600);
                    header("Location: index.php");
                    exit;
                }
                else if(password_verify($mdp, $data[$mail]['code']) && $data[$mail]['role']['bloque']==true){
                    $mdp_false=false;
                }    
                else{
                    $mdp_false=true;
                }
            }
            else{
                $mail_false=true;
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
        <link rel="stylesheet" href="css/login.css">
        <link rel="stylesheet" href="css/client.css">
        <title>Connexion - Les Croquettes du Chef</title>
        <link href="assets/Logo projet.png" rel="icon">
        <script src="js/charte.js"></script>
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
                <li><a href="login.php" class="active btn">Connexion</a></li>
            </ul>
        </nav>
        </header>
        <h1>Connexion à votre Espace</h1>
        <div class="rect_bleu">
            <form action="" method="post" target="_top" id="formulaire_login" name="connexion">
                <img class="logo_login" src="assets/Logo projet.png" alt="logo de notre site de vente">
                <?php if(isset($mail_false)){ ?>
                    <p class="mdp_faux">Compte inexistant ou adresse mail erronnée</p>
                <?php } ?>
                <?php if(isset($mdp_false)){ ?>
                    <p class="mdp_faux"><?php if($mdp_false==true){ echo "Mot de passe erroné";}else{ echo "Vous êtes bloqué";} ?></p>
                <?php } ?>
                <input type="email" name="nemail" id="idemail" class="login_case_email" placeholder="   Adresse email">
                <input type="password" name="ncode" id="idcode" class="login_case_code" placeholder="   Mot de passe">
                <input type="submit" value="Se connecter" class="login_submit">
                <div class="liens-bas">
                    <a class="mdp_oublie" href="mdp_oublie.php">
                        Mot de passe oublié 
                    </a>
                    <a class="pas_de_compte" href="inscription.php">
                        S'inscrire
                    </a>
                </div>
            </form>
        </div>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
    </body>
</html>













