<?php
error_reporting(0);

// Vérification du blocage UNIQUEMENT si l'utilisateur est connecté
if (isset($_COOKIE["client"])) {
    $client = json_decode($_COOKIE["client"], true);
    $file = file_get_contents("donnees/data.json");
    $data = json_decode($file, true);

    if (isset($data[$client["email"]]) && $data[$client["email"]]["role"]["bloque"] == true) {
        setcookie("client", "", time() - 3600);
        header("Location: index.php?msg=bloque");
        exit();
    }
}

$client = isset($_COOKIE["client"]) ? json_decode($_COOKIE["client"], true) : null;
$mail = $client ? $client["email"] : null;

$plat_data = file_get_contents("donnees/plat.json");
$plat = json_decode($plat_data, true);

$menu_data = file_get_contents("donnees/menu.json");
$menu = json_decode($menu_data, true);

$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);
if (isset($_COOKIE["client"])) {
    $commande_data = file_get_contents("donnees/panier_$mail.json");
    $commande = json_decode($commande_data, true);

    foreach ($plat as $index => $detail) {
        $btn_name = "btn_plus_" . str_replace(" ", "_", $index);
        
        if (isset($_REQUEST[$btn_name])) {
            $commande["total"] = round($commande["total"] + $detail["prix"], 2);
            
            if (isset($commande["plats"][$index])) {
                $commande["plats"][$index]["quantite"]++;
            } else {
                $commande["plats"][$index] = [
                    "quantite" => 1,
                    "prix" => $detail["prix"],
                    "name" => $detail["name"]
                ];
            }
            
            file_put_contents("donnees/panier_$mail.json", json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header("Location: menu.php");
            exit();
        }
    }

    // Ajout d'un menu
    foreach ($menu as $cle => $detail_menu) {
        $btn_name = "btn_menu_" . str_replace(" ", "_", $cle);
        
        if (isset($_REQUEST[$btn_name])) {
            $commande["total"] = round($commande["total"] + $detail_menu["prix"], 2);
            
            if (isset($commande["menus"][$cle])) {
                $commande["menus"][$cle]["quantite"]++;
            } else {
                $commande["menus"][$cle] = [
                    "quantite" => 1,
                    "prix" => $detail_menu["prix"],
                    "name" => $detail_menu["name"],
                    "plats" => $detail_menu["plats"]
                ];
            }
            
            file_put_contents("donnees/panier_$mail.json", json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header("Location: menu.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>La Carte - Les Croquettes du Chef</title>
    <link id="theme-css" rel="stylesheet" href="css/variables.css">
    <link rel="stylesheet" href="css/client.css">
    <link rel="stylesheet" href="css/accueil.css">
    <link rel="stylesheet" href="css/menu.css">
    <link href="assets/Logo projet.png" rel="icon">
    <script src="js/charte.js" defer></script>
    <script>
        function appliquer_filtre() {
            const filtres = document.querySelectorAll("input[type=checkbox]");
            
            const conteneurMenus = document.querySelector(".grille-menus");
            const menus = Array.from(document.querySelectorAll(".grille-menus article"));
            const conteneurPlats = document.querySelector(".grille-plats");
            const plats = Array.from(document.querySelectorAll(".grille-plats article"));

            menus.forEach(menu => menu.style.display = "");
            plats.forEach(plat => plat.style.display = "");

            let sectionMenus = document.querySelector(".section-menus");
            let banniereMenu = document.querySelector(".banniere-menu");
            if(sectionMenus) sectionMenus.style.display = "";
            if(banniereMenu) banniereMenu.style.display = "";

            let isCroissant = document.querySelector("input[name='croissant']").checked;
            let isDecroissant = document.querySelector("input[name='decroissant']").checked;

            for (let filtre of filtres) {
                let nomFiltre = filtre.name;

                if (nomFiltre === "croissant" || nomFiltre === "decroissant") {
                    continue;
                }

                if (!filtre.checked) {
                    if (nomFiltre === "pack") {
                        if(sectionMenus) sectionMenus.style.display = "none";
                        if(banniereMenu) banniereMenu.style.display = "none";
                    }
                    else if (nomFiltre === "produit_unique") {
                        plats.forEach(plat => plat.style.display = "none");
                    }
                    else {
                        plats.forEach(plat => {
                            let p = plat.querySelector('p[name="' + nomFiltre + '"]');
                            if (p && p.innerText.trim() === "1") {
                                plat.style.display = "none";
                            }
                        });

                        menus.forEach(menu => {
                            let p = menu.querySelector('p[name="' + nomFiltre + '"]');
                            if (p && p.innerText.trim() === "1") {
                                menu.style.display = "none";
                            }
                        });
                    }
                }
            }

            function extrairePrix(element) {
                let textePrix = element.querySelector(".prix").innerText;
                textePrix = textePrix.replace("€", "").replace(/\s/g, "").replace(",", ".");
                return parseFloat(textePrix);
            }

            if (isCroissant || isDecroissant) {
                let multiplicateur = isCroissant ? 1 : -1;

                plats.sort((a, b) => (extrairePrix(a) - extrairePrix(b)) * multiplicateur);
                menus.sort((a, b) => (extrairePrix(a) - extrairePrix(b)) * multiplicateur);

                if (conteneurPlats) {
                    plats.forEach(plat => conteneurPlats.appendChild(plat));
                }
                if (conteneurMenus) {
                    menus.forEach(menu => conteneurMenus.appendChild(menu));
                }
            }
        }

        window.onload = function() {
            const btn = document.querySelector(".btn-filtres");
            if (btn) {
                btn.addEventListener("click", function(event) {
                    event.preventDefault();
                    appliquer_filtre();
                });
            }

            const cbCroissant = document.querySelector("input[name='croissant']");
            const cbDecroissant = document.querySelector("input[name='decroissant']");

            if (cbCroissant && cbDecroissant) {
                cbCroissant.addEventListener('change', function() {
                    if (this.checked) cbDecroissant.checked = false;
                });
                cbDecroissant.addEventListener('change', function() {
                    if (this.checked) cbCroissant.checked = false;
                });
            }
        };
    </script>
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
                <li><a href="menu.php" class="active">La Carte</a></li>
                <li><a href="panier.php">🛒</a></li>
                <li><button id="btn-theme" onclick="changerTheme();">🌙</button></li>
                <li>
                    <a href="<?php echo isset($_COOKIE["client"]) ? "profil.php" : "login.php"; ?>" class="btn">
                        <?php echo isset($_COOKIE["client"]) ? "Profil" : "Connexion"; ?>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="banniere-menu">
            <h1>Notre Carte <span class="text-primary">Gourmande</span> 🦴</h1>
            <p>Sélectionnez le meilleur pour sa santé : sans céréales, frais et équilibré.</p>
        </section>

        <section class="section-menus">
            <h2>Nos Menus</h2>
            <form method="POST" action="menu.php">
                <div class="grille-menus">
                    <?php foreach ($menu as $cle => $detail_menu) { ?>
                        <article class="carte-menu">
                            <div class="carte-menu-header">
                                <h3><?php echo $detail_menu["name"]; ?></h3>
                                <p class="filtres" name="junior"><?php echo $detail_menu["age"]["junior"]; ?></p>
                                <p class="filtres" name="adulte"><?php echo $detail_menu["age"]["adulte"]; ?></p>
                                <p class="filtres" name="senior"><?php echo $detail_menu["age"]["senior"]; ?></p>
                                <p class="filtres" name="volaille"><?php echo $detail_menu["saveur"]["volaille"]; ?></p>
                                <p class="filtres" name="boeuf/gibier"><?php echo $detail_menu["saveur"]["boeuf/gibier"]; ?></p>
                                <p class="filtres" name="poisson"><?php echo $detail_menu["saveur"]["poisson"]; ?></p>
                                <p class="filtres" name="veggie"><?php echo $detail_menu["saveur"]["veggie"]; ?></p>
                                <p class="filtres" name="sans cereale"><?php echo $detail_menu["specifique"]["sans cereale"]; ?></p>
                                <p class="filtres" name="hypoallergenique"><?php echo $detail_menu["specifique"]["hypoallergenique"]; ?></p>
                                <p class="filtres" name="digestion sensible"><?php echo $detail_menu["specifique"]["digestion sensible"]; ?></p>
                            </div>
                            
                            <div class="carte-menu-plats">
                                <ul>
                                    <?php foreach ($detail_menu["plats"] as $nom_plat) { ?>
                                        <li><?php echo ucfirst($nom_plat); ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                            
                            <div class="carte-menu-footer">
                                <span class="prix"><?php echo number_format($detail_menu["prix"], 2, ",", " "); ?>€</span>
                                <button name="btn_menu_<?php echo str_replace(" ", "_", $cle); ?>" class="btn-ajout-menu">Ajouter</button>
                            </div>
                        </article>
                    <?php } ?>
                </div>
            </form>
        </section>

        <section class="conteneur-menu">
            <aside class="colonne-filtres">
                <div class="groupe-filtres">
                    <h3>Trier 💵💰🪙</h3>
                    <label><input type="checkbox" name="croissant"> Ordre Croissant</label>
                    <label><input type="checkbox" name="decroissant"> Ordre Décroissant</label>
                </div>
                <div class="groupe-filtres">
                    <h3>🐕 Âge</h3>
                    <label><input type="checkbox" name="junior" checked> Chiots (Junior)</label>
                    <label><input type="checkbox" name="adulte" checked> Adultes</label>
                    <label><input type="checkbox" name="senior" checked> Seniors</label>
                </div>
                <div class="groupe-filtres">
                    <h3>🥩 Saveurs</h3>
                    <label><input type="checkbox" name="volaille" checked> Volaille</label>
                    <label><input type="checkbox" name="boeuf/gibier" checked> Bœuf / Gibier</label>
                    <label><input type="checkbox" name="poisson" checked> Poisson</label>
                    <label><input type="checkbox" name="veggie" checked> Végétarien</label>
                </div>
                <div class="groupe-filtres">
                    <h3>⚠️ Spécifique</h3>
                    <label><input type="checkbox" name="sans cereale" checked> Sans Céréales</label>
                    <label><input type="checkbox" name="hypoallergenique" checked> Hypoallergénique</label>
                    <label><input type="checkbox" name="digestion sensible" checked> Digestion Sensible</label>
                </div>
                <div class="groupe-filtres">
                    <h3>🛒 Pack</h3>
                    <label><input type="checkbox" name="pack" checked> Pack</label>
                    <label><input type="checkbox" name="produit_unique" checked> Produit Unique</label>
                </div>
                <button class="btn-filtres">Appliquer les filtres</button>
            </aside>

            <div class="zone-plats">
                <div class="barre-recherche-menu">
                    <input type="text" placeholder="Rechercher une croquette précise...">
                    <button>🔍</button>
                </div>
                
                <form method="POST" action="menu.php">
                    <div class="grille-plats">
                        <?php foreach ($plat as $index => $detail) { ?>
                            <article class="carte-plat">
                                <div class="carte-img">
                                    <img src="assets/<?php echo $detail["image"]; ?>" alt="<?php echo htmlspecialchars($detail["name"]); ?>">
                                    <?php if ($detail["new"] == true) { ?>
                                        <span class="etiquette-nouveau">Nouveau</span>
                                    <?php } ?>
                                </div>
                                
                                <div class="carte-contenu">
                                    <h4><?php echo $detail["name"]; ?></h4>
                                    <p class="description"><?php echo $detail["description"]; ?></p>
                                    
                                    <p class="filtres" name="junior"><?php echo $detail["age"]["junior"]; ?></p>
                                    <p class="filtres" name="adulte"><?php echo $detail["age"]["adulte"]; ?></p>
                                    <p class="filtres" name="senior"><?php echo $detail["age"]["senior"]; ?></p>
                                    <p class="filtres" name="volaille"><?php echo $detail["saveur"]["volaille"]; ?></p>
                                    <p class="filtres" name="boeuf/gibier"><?php echo $detail["saveur"]["boeuf/gibier"]; ?></p>
                                    <p class="filtres" name="poisson"><?php echo $detail["saveur"]["poisson"]; ?></p>
                                    <p class="filtres" name="veggie"><?php echo $detail["saveur"]["veggie"]; ?></p>
                                    <p class="filtres" name="sans cereale"><?php echo $detail["specifique"]["sans cereale"]; ?></p>
                                    <p class="filtres" name="hypoallergenique"><?php echo $detail["specifique"]["hypoallergenique"]; ?></p>
                                    <p class="filtres" name="digestion sensible"><?php echo $detail["specifique"]["digestion sensible"]; ?></p>
                                    
                                    <div class="carte-footer">
                                        <span class="prix"><?php echo number_format($detail["prix"], 2, ",", " "); ?>€</span>
                                        <button name="btn_plus_<?php echo str_replace(" ", "_", $index); ?>" class="btn-carte">+</button>
                                    </div>
                                </div>
                            </article>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 Les Croquettes du Chef - Espace Client</p>
    </footer>

</body>
</html>
