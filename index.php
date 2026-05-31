<?php 
    session_start();
    error_reporting(0);
    if(!isset($_SESSION['client'])){
        // Pas connecté
        $client = null;
    } else {
        $client = $_SESSION['client'];
        $mail = $client["email"];
    }
    $file=file_get_contents("donnees/data.json");
    $data=json_decode($file, true);
    $plats=json_decode(file_get_contents("donnees/plat.json"), true);
    if(isset($client) && $data[$client['email']]['role']['bloque']==true){
        unset($_SESSION['client']);
        header("Location: index.php");
        exit;
    } 
    $plat1=$plats["agneau roti aux herbes"];
    $plat2=$plats["agneau roti aux herbes"];
    $plat3=$plats["agneau roti aux herbes"];
    foreach($plats as $id => $plat){
        if($plat["vente"]>=$plat1["vente"] && $plat["prix"]>$plat1["prix"]){
            $plat1=$plat;
        }
        else if(($plat["vente"]>=$plat2["vente"] && $plat["prix"]>$plat2["prix"] && $plat!=$plat1) || $plat2==$plat1){
            $plat2=$plat;
        }
        else if(($plat["vente"]>=$plat3["vente"] && $plat["prix"]>$plat3["prix"] && $plat!=$plat1 && $plat!=$plat2) || $plat3==$plat1 || $plat3==$plat2){
            $plat3=$plat;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Les Croquettes du Chef - Pour Chiens</title>
    
    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/client.css">
    <link rel="stylesheet" href="css/accueil.css">
    <link href="assets/Logo projet.png" rel="icon">
    <script src="js/charte.js" defer></script>
</head>
<body>

    <header>
    <div class="logo">
        <img src="assets/Logo projet.png" alt="Logo Yumland" class="header-logo">
        Les croquettes du chef
    </div>
    <nav>
        <ul>
            <li><a href="index.php" class="active">Accueil</a></li>
            <li><a href="menu.php">La Carte</a></li>
            <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
            <li><a href="<?php if(isset($_SESSION['client'])){ echo "profil.php"; } else{ echo "login.php"; }  ?>" class="btn">
                <?php  
                    if(isset($_SESSION['client'])){
                        echo "Profil";
                    }
                    else{
                        echo "Connexion";
                    }
                ?>
            </a></li>
        </ul>
    </nav>
    </header>

    <main>
        <section class="banniere-centre">
            <div class="banniere-contenu">
                <h1>La gastronomie canine <br><span class="texte-principal">livrée dans sa gamelle</span></h1>
                <p class="sous-titre">Fini les croquettes industrielles. Offrez-lui des recettes fraîches, saines et validées par les vétérinaires.</p>
            </div>
        </section>

        <section class="zone-contenu">
            <div class="conteneur-gauche">
                
                <div class="conteneur-recherche">
                    <form action="menu.html" method="get" class="barre-recherche">
                        <span class="icone">🦴</span> <input type="text" name="search" placeholder="Race, âge, saveur (ex: Agneau, Senior...)">
                        <button type="submit">Wouf !</button>
                    </form>
                </div>
                <div class="capsule-liste" style="margin-top: 60px;">
                    <h3 class="titre-section">Nos meilleures recettes ⭐</h3>

                    <article class="capsule">
                        <div class="capsule-img">
                            <img src="assets/le prestige du chef.png" alt="Croc Premium">
                        </div>
                        <div class="capsule-info">
                            <h4>Le Prestige du Chef</h4>
                            <p>Bœuf Wagyu & Éclats de Truffe • L'excellence</p>
                        </div>
                        <div class="capsule-action">
                            <span class="prix">29.90€</span>
                        </div>
                    </article>

                    <article class="capsule">
                        <div class="capsule-img">
                            <img src="assets/couronne de gibier.png" alt="Croc Gibier">
                        </div>
                        <div class="capsule-info">
                            <h4>Couronne de Gibier</h4>
                            <p>Cerf Sauvage & Myrtilles • Vitalité Premium</p>
                        </div>
                        <div class="capsule-action">
                            <span class="prix">22.50€</span>
                        </div>
                    </article>

                    <article class="capsule">
                        <div class="capsule-img">
                            <img src="assets/perle de l'ocean.png" alt="Croc Saumon">
                        </div>
                        <div class="capsule-info">
                            <h4>Perle de l'Océan</h4>
                            <p>Saumon Fumé & Graines de Chia • Digestion douce</p>
                        </div>
                        <div class="capsule-action">
                            <span class="prix">27.90€</span>
                        </div>
                    </article>

                </div>
                <div class="capsule-liste">
                    <h3 class="titre-section">Les favoris</h3>

                    <article class="capsule">
                        <div class="capsule-img">
                            <img src="assets/le prestige du chef.png" alt="Croc Premium">
                        </div>
                        <div class="capsule-info">
                            <h4><?php echo $plat1["name"] ?></h4>
                            <p><?php echo $plat1["description"] ?></p>
                        </div>
                        <div class="capsule-action">
                            <span class="prix"><?php echo number_format($plat1['prix'], 2, ',', ' '); ?>€</span>
                        </div>
                    </article>

                    <article class="capsule">
                        <div class="capsule-img">
                            <img src="assets/le prestige du chef.png" alt="Croc Premium">
                        </div>
                        <div class="capsule-info">
                            <h4><?php echo $plat2["name"] ?></h4>
                            <p><?php echo $plat2["description"] ?></p>
                        </div>
                        <div class="capsule-action">
                            <span class="prix"><?php echo number_format($plat2['prix'], 2, ',', ' '); ?>€</span>
                        </div>
                    </article>

                    <article class="capsule">
                        <div class="capsule-img">
                            <img src="assets/le prestige du chef.png" alt="Croc Premium">
                        </div>
                        <div class="capsule-info">
                            <h4><?php echo $plat3["name"] ?></h4>
                            <p><?php echo $plat3["description"]; ?></p>
                        </div>
                        <div class="capsule-action">
                            <span class="prix"><?php echo number_format($plat3['prix'], 2, ',', ' '); ?>€</span>
                        </div>
                    </article>

                </div>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>

</body>
</html>
