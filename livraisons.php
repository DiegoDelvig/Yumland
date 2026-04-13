<?php
    error_reporting(0);
    if(!isset($_COOKIE["client"])){
        header("Location: index.php");
    }
    $client = json_decode($_COOKIE["client"], true);
    $liste_client_data = file_get_contents("donnees/data.json");
    $liste_client = json_decode($liste_client_data, true);
    $commande_data = file_get_contents("donnees/commande.json");
    $commande = json_decode($commande_data, true);
    if($liste_client[$client['email']]['role']['bloque']==true ){
        setcookie("client", json_encode($data[$mail]), time()-3600);  
        header("Location: index.php");
    }
    if($liste_client[$client['email']]['role']['livreur']==false ){
        header("Location: profil.php");
    }
    function aff_num_cmd_ou_fidelite($num, $cmd_ou_fidelite){
        if ($cmd_ou_fidelite == 1) {
            if ($num < 10) {
                echo "000" . $num;
            } elseif ($num < 100) {
                echo "00" . $num;
            } elseif ($num < 1000) {
                echo "0" . $num;
            } 
            else {
                echo $num;
            }
        } 
        else {
            if ($num < 10) {
                echo "0000000" . $num;
            } 
            elseif ($num < 100) {
                echo "000000" . $num;
            } 
            elseif ($num < 10000) {
                echo "00000" . $num;
            } 
            elseif ($num < 100000) {
                echo "0000" . $num;
            } 
            elseif ($num < 1000000) {
                echo "000" . $num;
            } 
            elseif ($num < 10000000) {
                echo "00" . $num;
            } 
            elseif ($num < 100000000) {
                echo "0" . $num;
            } 
            else {
                echo $num;
            }
        }
    }
    $cmd_ou_pas = false;
    $temps=0;
    if (!empty($commande)) {
        foreach ($commande as $id_cmd => $detail) {
            if ($detail["etat"]["cuisinee"] == true &&$detail["livreur"] == $client["email"]) {
                $cmd_ou_pas = true;
                if($detail['temps']>=$temps){
                    $cmd=$detail;
                    $temps=$detail['temps'];
                }
            }
        }
    }
    function aff_temps($num){
        if ($num < 10) {
            return "0" . $num;
        } 
        else {
            return $num;
        }
    }
    function aff_num_cmd_sans_echo($num){
        if($num<10){
            return "000".$num;
        }
        else if($num<100){
            return "00".$num;
        }
        else if($num<1000){
            return "0".$num;
        }
        else{
            return $num;
        }
    }
    if(isset($_REQUEST['button'])){
        $file_data=file_get_contents("donnees/commande_passe.json");
        $file=json_decode($file_data, true);
        $mail=$cmd['mail'];
        $new_tab=array('num'=>$cmd['num'], 'date'=>$cmd['date'], 'total'=>$cmd['total'], 'plats'=>$cmd['plats'], 'menus'=> $cmd['menus']);
        $file[$mail][aff_num_cmd_sans_echo($cmd['num'])]=$new_tab;
        file_put_contents("donnees/data.json", json_encode($liste_client, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        unset($commande[aff_num_cmd_sans_echo($cmd['num'])]);
        file_put_contents("donnees/commande_passe.json", json_encode($file, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents("donnees/commande.json", json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: livraisons.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livraison - Les Croquettes du Chef</title>
    <link rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/client.css">
    <link rel="stylesheet" href="css/livraisons.css">
    <link href="assets/Logo projet.png" rel="icon">
</head>
<body>

    <header>
        <div class="logo">
            <img src="assets/Logo projet.png" alt="Logo" class="header-logo" style="height: 35px;">
            Espace Livraison
        </div>
        <nav>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="menu.php">La Carte</a></li>
                <li><a href="commandes.php" class="active">Livraison</a></li>
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

        <h2>LIVRAISON EN COURS</h2>

        <?php if ($cmd_ou_pas == true) { ?>
            <div class="livreur-info">
                <h3>COMMANDE N°<?php aff_num_cmd_ou_fidelite($cmd['num'], 1); ?></h3>
                <div class="bloc-blanc">
                    <p><u>ADRESSE</u> : <b><?php echo strtoupper($liste_client[$cmd['mail']]['adr']); ?></b></p>
                    <p><u>NOM</u> : <b><?php echo strtoupper($liste_client[$cmd['mail']]['fname']); ?></b></p>
                    <p><u>PRÉNOM</u> : <b><?php echo strtoupper($liste_client[$cmd['mail']]['name']); ?></b></p>
                    <p><u>MAIL</u> : <b><?php echo $liste_client[$cmd['mail']]['email']; ?></b></p>
                    <p><u><?php echo "TELEPHONE"; ?></u> : <b><?php echo strtoupper($liste_client[$cmd['mail']]['tel']); ?></b></p>
                    <?php if($liste_client[$cmd['mail']]['infocomp']!=""){?>
                        <p><u>INFORMATION COMPLEMENTAIRE</u> :<b><?php echo " ".$liste_client[$cmd['mail']]['infocomp']; ?></b></p>
                    <?php } ?>
                    
                </div>
                <div class="zone-boutons">
                    <a class="maps" href="https://www.google.com/maps/place/<?php echo urlencode($liste_client[$cmd['mail']]['adr']) ?>/" target="_blank">LANCER L'ITINÉRAIRE (Google Maps)</a>
                    <form method="POST" action="livraisons.php">
                        <button name="button" class="btn-livraison">LIVRAISON TERMINÉE</button>
                    </form>
                </div>
            </div>
        <?php } else { ?>
    <div class="livreur-info aucune-commande">
        <div class="bloc-vide">
            <div class="icone-vide">🛵</div>
            <h1>AUCUNE COMMANDE À LIVRER</h1>
            <p>Vous n'avez pas de livraison assignée pour le moment.<br>Revenez vérifier dans quelques instants.</p>
        </div>
    </div>
<?php } ?>
</main>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Livreur</p>
    </footer>

</body>
</html>
