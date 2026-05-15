<?php 
    error_reporting(0);
    if(!isset($_COOKIE["client"])){
        header("Location: index.php");
        exit;
    }
    $client=json_decode($_COOKIE["client"], true);
    $mail=$client['email'];   
    $file=file_get_contents("donnees/data.json");
    $data=json_decode($file, true);
    $commande_data =file_get_contents("donnees/commande_passe.json");
    $commande = json_decode($commande_data, true); 
    $plat_data=file_get_contents("donnees/plat.json");
    $pla=json_decode($plat_data, true);
    if($data[$client['email']]['role']['bloque']==true){
        setcookie("client", json_encode($data[$mail]), time()-3600);  
        header("Location: index.php");
        exit;
    }
    foreach($commande[$mail] as $id => $cmd_client){
        if($cmd_client['note']['etat']==false){
            $cmd=$cmd_client;
            $id_cmd = $id;
        }
        else if($cmd_client['note']['note']==0){
            $commande[$mail][$id]['note']['etat']=false;
            file_put_contents("donnees/commande_passe.json", json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header("Location: notation.php");
            exit;
        }
    }
    if(!isset($cmd)){
        header("Location: index.php");
        exit;
    }
    if(isset($_REQUEST['submit_btn'])){
        $somme = 0;
        $quantite = 0;
        foreach($cmd['plats'] as $idplat => $details_plat){
            if(isset($_REQUEST["name".str_replace(" ", "_", $idplat)])){
                $note = intval($_REQUEST["name".str_replace(" ", "_", $idplat)]);
                $commande[$mail][$id_cmd]['plats'][$idplat]['note'] = $note;
            } else {
                $note = 0;
                $commande[$mail][$id_cmd]['plats'][$idplat]['note'] = $note;
            }
            if(isset($_REQUEST["com_".str_replace(" ", "_", $idplat)])){
                $commande[$mail][$id_cmd]['plats'][$idplat]['com_plat'] = $_REQUEST["com_".str_replace(" ", "_", $idplat)];
            } else {
                $commande[$mail][$id_cmd]['plats'][$idplat]['com_plat'] = "";
            }
            
            $somme += $note;
            $quantite++;
        }
        if(isset($_REQUEST['com'])){
            $commande[$mail][$id_cmd]['note']['com'] = $_REQUEST['com'];
        } 
        else {
            $commande[$mail][$id_cmd]['note']['com'] = "";
        }
        if($quantite > 0){
            $commande[$mail][$id_cmd]['note']['note'] = intval($somme/$quantite);
        } 
        else {
            $commande[$mail][$id_cmd]['note']['note'] = 0;
        }
        $commande[$mail][$id_cmd]['note']['etat'] = true;
        file_put_contents("donnees/commande_passe.json", json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: profil.php");
        exit;
    }
?>
<!DOCTYPE html>
<html> 
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <<link id="theme-css" rel="stylesheet" href="css/variables.css">
        <link rel="stylesheet" href="css/notation.css">
        <link rel="stylesheet" href="css/client.css">
        <script src="js/charte.js></script>
        
        <title>Les Croquettes du Chef</title>
        <link href="assets/Logo projet.png" rel="icon">
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
                <li><a href="menu.php">La carte</a></li>
                <li><a href="menu.php" class="active">Avis</a></li>
                <li><a href="profil.php" class="btn">Profil</a></li>
            </ul>
        </nav>
        </header>
        <div class="rect_bleu">
            <form action="" method="post" id="formulaire_login" name="connexion">
                <img class="logo_login" src="assets/Logo projet.png" alt="logo de notre site de vente">
                <div class="Notez_votre_cmd">   
                    Notez votre commande
                </div>
                
                <?php foreach($cmd['plats'] as $idplat => $plat){ ?>
                    <div class="cmd_1">
                        <img class="img_cmd_1" src="assets/<?php echo $pla[$idplat]['image']; ?>" alt="image <?php echo $plat['name']; ?>">
                        <div class="cmd1_s_img">
                            <div class="l1_1">
                                <div class="nplat_1">
                                    <?php echo $plat['name']; ?>
                                </div>
                                <div class="radio_1">                               
                                    <?php $id_etoile = str_replace(" ", "_", $idplat); ?>
                                    <input type="radio" name="name<?php echo $id_etoile; ?>" id="idnote_<?php echo $id_etoile; ?>_5" class="note_1" value="5" required>
                                    <label for="idnote_<?php echo $id_etoile; ?>_5">★</label>                                
                                    <input type="radio" name="name<?php echo $id_etoile; ?>" id="idnote_<?php echo $id_etoile; ?>_4" class="note_1" value="4">
                                    <label for="idnote_<?php echo $id_etoile; ?>_4">★</label>                               
                                    <input type="radio" name="name<?php echo $id_etoile; ?>" id="idnote_<?php echo $id_etoile; ?>_3" class="note_1" value="3">
                                    <label for="idnote_<?php echo $id_etoile; ?>_3">★</label>                               
                                    <input type="radio" name="name<?php echo $id_etoile; ?>" id="idnote_<?php echo $id_etoile; ?>_2" class="note_1" value="2">
                                    <label for="idnote_<?php echo $id_etoile; ?>_2">★</label>
                                    <input type="radio" name="name<?php echo $id_etoile; ?>" id="idnote_<?php echo $id_etoile; ?>_1" class="note_1" value="1">
                                    <label for="idnote_<?php echo $id_etoile; ?>_1">★</label>
                                </div>
                            </div>
                            <textarea name="<?php echo "com_".str_replace(" ", "_", $idplat); ?>" class="com_1" placeholder="   Votre commentaire sur le plat"></textarea>
                        </div>
                    </div>
                <?php } ?>
                <textarea name="com" class="com_1" placeholder="   Votre commentaire sur la commande"></textarea>
                <button class="avis_submit" name="submit_btn">
                    Soumettre mes avis
                </button>
            </form>
        </div>
    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>
        
    </body>
</html>
