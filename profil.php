<?php 
    error_reporting(0);
    if(!isset($_COOKIE["client"])){
        header("Location: index.php");
        exit;
    }
    if(isset($_COOKIE["admin"])){
        $client_temp = json_decode($_COOKIE["admin"], true); 
    }
    else{
        $client_temp = json_decode($_COOKIE["client"], true);
    }
    $commande_data = file_get_contents("donnees/commande_passe.json");
    $commande = json_decode($commande_data, true); 
    $file = file_get_contents("donnees/data.json");
    $data = json_decode($file, true);
    $client = $data[$client_temp['email']];
    $mail = $client['email'];   
    if(!isset($_COOKIE["admin"])){
        setcookie("client", json_encode($client), time()-3600);  
        setcookie("client", json_encode($client), time()+3600);
    }
    if($client['role']['bloque'] == true && !isset($_COOKIE["admin"])){
        setcookie("client", json_encode($client), time()-3600);  
        header("Location: index.php");
        exit;
    }
    $mail=$client['email'];   
    if(!file_exists("donnees/panier_$mail.json")){
        $panier=array("total"=>0, "reduction"=>false);
        $panier_data="donnees/panier_$mail.json";
        file_put_contents($panier_data, json_encode($panier, JSON_PRETTY_PRINT));
    }
    function aff_num_cmd_ou_fidelite($num, $cmd_ou_fidelite){
        if($cmd_ou_fidelite==1){
            if($num<10){
                echo "000".$num;
            }
            else if($num<100){
                echo "00".$num;
            }
            else if($num<1000){
                echo "0".$num;
            }
            else{
                echo $num;
            }
        }
        else{
            if($num<10){
                echo "0000000".$num;
            }
            else if($num<100){
                echo "000000".$num;
            }
            else if($num<10000){
                echo "00000".$num;
            }
            else if($num<100000){
                echo "0000".$num;
            }
            else if($num<1000000){
                echo "000".$num;
            }
            else if($num<10000000){
                echo "00".$num;
            }
            else if($num<100000000){
                echo "0".$num;
            }
            else{
                echo $num;
            }
        }
    }
    function aff_num_cmd($num){
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
    function aff_temps($num){
        if($num<10){
            return "0".$num;
        }
        else{
            return $num;
        }
    }  
    foreach($commande[$mail] as $id_cmd => $details){
        if(isset($_REQUEST[aff_num_cmd($details['num'])])){
            foreach($details['plats'] as $id => $pla){
                $panier['total'] += ($pla['quantite'] * $pla['prix']);
                if(isset($panier['plats'][$id])){
                    $panier['plats'][$id]['quantite'] += $pla['quantite'];
                }
                else{
                    $panier['plats'][$id] = $pla;
                }
            }
            file_put_contents("donnees/panier_$mail.json", json_encode($panier, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header("Location: panier.php");
            exit; 
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
    <script src="js/charte.js></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="assets/Logo projet.png" alt="Logo" class="header-logo" style="height: 35px;">
            Espace client
        </div>
        <nav>
            <ul>
                <?php if(isset($client['role']['restaurateur']) && $client['role']['restaurateur'] == true && !isset($_COOKIE["admin"])){ ?>
                    <li><a href="commandes.php">Commande</a></li>
                <?php } ?>
                <?php if(isset($client['role']['livreur']) && $client['role']['livreur'] == true && !isset($_COOKIE["admin"])){ ?>
                    <li><a href="livraisons.php">Livraison</a></li>
                <?php } ?>
                <?php if(isset($client['role']['admin']) && $client['role']['admin'] == true && !isset($_COOKIE["admin"])){ ?>
                    <li><a href="admin.php">Administration</a></li>
                <?php } ?>
                <?php if(!isset($_COOKIE["admin"])){ ?>
                    <li><a href="menu.php">La Carte</a></li>
                <?php } ?>
                <?php if(!isset($_COOKIE["admin"])){ ?>
                    <li><a href="panier.php">🛒</a></li>
                <?php } ?>
                <?php if(!isset($_COOKIE["admin"])){ ?>
                    <li><a href="profil.php" class="active">Mon profil</a></li>
                <?php } ?>
                <?php if(isset($_COOKIE["admin"])){ ?>
                    <li><a href="admin.php" class="btn">Retour page admin</a></li>
                <?php }else{ ?>
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
            <button type="button" class="carre-crayon" id="btn-edit-profile" aria-label="Modifier le profil">✏️</button>
        </div>
        <div class="info-details">
            <p>NOM :</p>
            <p><strong><?php echo $client['fname']; ?></strong></p>
            <p>PRÉNOM :</p>
            <p><strong><?php echo $client['name']; ?></strong></p>
            <p>ADRESSE :</p>
            <p><strong><?php echo $client['adr']; ?></strong></p>
            <p>TÉLÉPHONE :</p>
            <p><strong><?php echo $client['tel']; ?></strong></p>
            <?php if($client['infocomp']!=""){ ?>
                <p>INFOS COMPLÉMENTAIRES :</p>
                <p><strong><?php echo $client['infocomp']; ?></strong></p>
            <?php } ?>
        </div>
    </aside>

    <section class="contenu-profil">
        <h1>Mon Profil</h1>

        <div class="carte-info">
            <h3>Programme De Fidélité</h3>
            <p>
                N° de carte de fidélité :<br />
                <span class="numero-carte"><?php aff_num_cmd_ou_fidelite($client['numero_fidelite'], 2); ?></span>
            </p>
            <?php $pourcentage=(100*$client['point_fidelite'])/300;
                if($pourcentage>=100){
                    $pourcentage=100;
                }
            ?>
            <p>Vous avez <strong><?php  echo $client['point_fidelite']; ?> points</strong></p>
            <div class="barre">
                <div class="avancee" style="width:<?php echo $pourcentage; ?>%;"></div>
            </div>
            <?php if($pourcentage<100){ ?>
                <p><small>Encore <?php echo 300-($client['point_fidelite']); ?> points avant la réduction de 25% sur la commande suivante !</small></p>
            <?php }else{ ?>
                <p><small>🎉 Félicitations ! Vous avez débloqué une réduction de 25% sur votre prochaine commande !</small></p>
            <?php } ?>
            
        </div>
        <div class="carte-info">
            <h3>Anciennes commandes 🛍️</h3>
                <?php 
                    $email = $client['email'];
                    if(!empty($commande[$email])){
                        foreach($commande[$email] as $id_cmd => $details){ ?>
                            <div class="commande">
                                <div class="numero">
                                    <strong>Commande n°<?php aff_num_cmd_ou_fidelite($details['num'], 1); ?> (<?php echo aff_temps($details['date']['jour'])."/".aff_temps($details['date']['mois'])."/".aff_temps($details['date']['annee'])." à ".aff_temps($details['date']['heure']).":".aff_temps($details['date']['minute']); ?>)</strong>
                                    <form method="POST"><button name="<?php aff_num_cmd_ou_fidelite($details['num'], 1) ; ?>" class="bouton-recommande">Recommander</button></form>
                                </div>
                                <?php foreach($details['plats'] as $produit){ ?>
                                <p><?php echo $produit['quantite']."x       -".$produit['name']; ?> - <?php echo number_format($produit['quantite']*$produit['prix'], 2, ',', ' '); ?>€</p>
                                <?php } ?>
                                <?php foreach($details['menus'] as $produit){ ?>
                                <p><?php echo $produit['quantite']."x       -".$produit['name']; ?> - <?php echo number_format($produit['quantite']*$produit['prix'], 2, ',', ' '); ?>€</p>
                                <?php } ?>
                            </div>
                        <?php } 
                    } else { ?>
                        <div class="commande">
                            <div class="numero">
                                <strong><br>Vous n'avez pas encore passé de commande.</strong>
                                <button name="<?php aff_num_cmd_ou_fidelite($details['num'], 1) ; ?>" class="bouton-recommande">Commander</button>
                            </div>
                        </div>
                <?php } ?>
        </div>        
    </section>

    <!-- 🔴 INSÉRER LE MODAL ICI (AVANT </main>) -->
    <!-- Modal de modification AJAX -->
    <div id="modal-edit-profile" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Modifier mon profil</h2>
                <button type="button" class="modal-close" id="close-modal" aria-label="Fermer">&times;</button>
            </div>
            
            <form id="form-edit-profile" class="modal-form">
                <div class="form-group">
                    <label for="edit-name">NOM *</label>
                    <input type="text" id="edit-name" name="name" required maxlength="100" value="<?php echo htmlspecialchars($client['name']); ?>">
                    <small class="error-message" id="error-name"></small>
                </div>
                
                <div class="form-group">
                    <label for="edit-fname">PRÉNOM *</label>
                    <input type="text" id="edit-fname" name="fname" required maxlength="100" value="<?php echo htmlspecialchars($client['fname']); ?>">
                    <small class="error-message" id="error-fname"></small>
                </div>
                
                <div class="form-group">
                    <label for="edit-adr">ADRESSE *</label>
                    <input type="text" id="edit-adr" name="adr" required maxlength="200" value="<?php echo htmlspecialchars($client['adr']); ?>">
                    <small class="error-message" id="error-adr"></small>
                </div>
                
                <div class="form-group">
                    <label for="edit-tel">TÉLÉPHONE *</label>
                    <input type="tel" id="edit-tel" name="tel" required pattern="[0-9+ ]+" maxlength="20" value="<?php echo htmlspecialchars($client['tel']); ?>">
                    <small class="error-message" id="error-tel"></small>
                </div>
                
                <div class="form-group">
                    <label for="edit-infocomp">INFOS COMPLÉMENTAIRES</label>
                    <textarea id="edit-infocomp" name="infocomp" maxlength="500"><?php echo htmlspecialchars($client['infocomp']); ?></textarea>
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
            
            <!-- Fallback HTML (affiche si JS échoue) -->
            <noscript>
                <p style="text-align: center; margin-top: 20px;">
                    <a href="modification.php" class="btn">Aller à la page de modification</a>
                </p>
            </noscript>
        </div>
    </div>

    <!-- Fallback overlay (pour fermer au clic en dehors du modal) -->
    <div id="modal-overlay" class="modal-overlay" style="display: none;"></div>

</main>
<footer>
    <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
</footer>

<script src="js/edit-profile.js" defer></script>
</body>
</html>
