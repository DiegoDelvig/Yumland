<?php 
require_once __DIR__ . '/config.php';
    error_reporting(0);
    if(!isset($_COOKIE["client"])){
        header("Location: index.php");
        exit;
    }
    $client=json_decode($_COOKIE["client"], true);
    $file=file_get_contents(DATA_DIR . '/data.json');
    $data=json_decode($file, true);
    $mail=$client['email'];   
    $commande_data =file_get_contents(DATA_DIR . '/panier_' . $mail . '.json', true);
    $commande = json_decode($commande_data, true); 
    if(file_exists(DATA_DIR . '/panier.json')){
        $panier=json_decode(file_get_contents(DATA_DIR . '/panier.json'), true);
        $panier[$mail]=$commande;
    }
    else{
        $panier[$mail]=$commande;
    }
    file_put_contents(DATA_DIR . '/panier.json', json_encode($panier, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    if(file_exists(DATA_DIR . '/panier_' . $mail . '.json')){
        unlink(DATA_DIR . '/panier_' . $mail . '.json');
    }
    setcookie("client", json_encode($data[$mail]), time()-3600);  
    header("Location: index.php");
    exit;
?>
