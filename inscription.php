<?php 
    error_reporting(0);
    if(empty($_REQUEST['nname'])||empty($_REQUEST['nfname'])||empty($_REQUEST['nadr'])||empty($_REQUEST['ntel'])||empty($_REQUEST['nemail'])||empty($_REQUEST['ncode'])){
    }
    else{
        $file= "donnees/data.json";
        $commande="donnees/commande_passe.json";
        if(file_exists($file)){
            $client_passe=file_get_contents($file);
            $data=json_decode($client_passe, true);
            $dernier_client=end($data);
            $num=$dernier_client['numero_fidelite']+1;
        }
        else{
            $data=[];
            $num=1;
        }
        if(file_exists($commande)){
            $commande_passe=file_get_contents($commande);
            $data_commande=json_decode($commande_passe, true);

        }
        $name=$_POST['nname'];
        $fname=$_POST['nfname'];
        $adr=$_POST['nadr'];
        $tel=$_POST['ntel'];
        $infocomp=$_POST['ninfocomp'];
        $email=$_POST['nemail'];
        $code=$_POST['ncode'];
        $new_user=array('name' => $name, 'fname'=>$fname, 'adr'=>$adr, 'tel'=>$tel, 'infocomp'=>$infocomp, 'email'=>$email, 'code'=>password_hash($code, PASSWORD_DEFAULT), 'point_fidelite'=>0, 'numero_fidelite'=>$num, 'role'=>array('livreur'=>false,'admin'=>false,'bloque'=>false, 'restaurateur'=>false));
        $data[$email]=$new_user;
        $vide=[];
        $data_commande[$email]=(object) $vide;
        file_put_contents("donnees/data.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents("donnees/commande.json", json_encode($data_commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: login.php");
        exit;
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
        <script src="js/charte.js"></script>
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
        <form action="" method="post" target="_top" id="formulaire" name="connexion">
            <div class="rect_bleu">
                <img class="logo_login" src="assets/Logo projet.png" alt="logo de notre site de vente">
                <input type="text" name="nname" id="idname" class="login_case_name" placeholder="   Prénom">
                <input type="text" name="nfname" id="idfname" class="login_case_fname" placeholder="   Nom">
                <input type="text" name="nadr" id="idadr" class="login_case_adr" placeholder="   Adresse Postale">
                <input type="tel" name="ntel" id="idtel" class="login_case_tel" placeholder="   Numéro de téléphone">
                <textarea name="ninfocomp" id="idinfocomp" class="login_case_infocomp" placeholder="   Information complémentaire"></textarea>
                <input type="email" name="nemail" id="idemail" class="login_case_email" placeholder="   Adresse email">
                <input type="password" name="ncode" id="idcode" class="login_case_code" placeholder="   Mot de passe">
                <input type="submit" value="S'inscrire" class="login_submit">
            </div>
        </form>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
    </body>
</html>










