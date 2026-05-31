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
    $commande_data =file_get_contents("donnees/panier_$mail.json");
    $commande = json_decode($commande_data, true); 
    $cmd_data=file_get_contents("donnees/commande_passe.json");
    $cmd=json_decode($cmd_data, true);
    $new_cmd_data=file_get_contents("donnees/commande.json");
    $new_cmd=json_decode($new_cmd_data, true);
    $panier_data=file_get_contents("donnees/panier.json");
    $panier=json_decode($panier_data, true);
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
    include("getapikey/getapikey.php");
    $vendeur = "MI-1_I";
    $api_key = getAPIKey($vendeur);
    $transaction_recue = $_REQUEST['transaction'];
    $montant_recu = $_REQUEST['montant'];
    $statut = $_REQUEST['status'];
    $control_banque = $_REQUEST['control'];
    $mon_control = md5($api_key . "#" . $transaction_recue . "#" . $montant_recu . "#" . $vendeur . "#" . $statut . "#");
    if ($mon_control !== $control_banque) {
        header("Location: annulation.php");
        exit;
    }
    if($_REQUEST['status']=='accepted'){
        $livreur=0;
        foreach($data as $index => $pers){
            if($pers['role']['livreur']==true){
                $livreur++;
            }
        }
        $liv=rand(1, $livreur);
        $livreur_cmd = "";
        for($i=0; $i<$liv; $i++){
            foreach($data as $index => $pers){
                if($pers['role']['livreur']==true&&$livreur_cmd!=$pers['email']&&$livreur_cmd!=$client['email']){
                    $livreur_cmd=$pers['email'];
                    break;
                }
            }
        }
        $num = 1;
        if (!empty($cmd)) {
            foreach($cmd as $index => $pers){
                foreach($pers as $id => $cmd_nb){
                    if($cmd_nb['num'] >= $num){
                        $num = $cmd_nb['num'] + 1; 
                    }
                }
            }
        }
        if (!empty($new_cmd)) {
            foreach($new_cmd as $id => $cmd_nb){
                if($cmd_nb['num'] >= $num){
                    $num = $cmd_nb['num'] + 1; 
                }
            }
        }
        $new_cmd[aff_num_cmd($num)]=$commande;
        $new_cmd[aff_num_cmd($num)]['livreur']=$livreur_cmd;
        $new_cmd[aff_num_cmd($num)]['note']=array('etat' => false, 'note' => 0);
        $new_cmd[aff_num_cmd($num)]['num']=$num;
        $new_cmd[aff_num_cmd($num)]['mail']=$mail;
        $new_cmd[aff_num_cmd($num)]['etat']=array('cuisinee' => false, 'livree' => false);
        $new_cmd[aff_num_cmd($num)]['temps']=0;
        $new_cmd[aff_num_cmd($num)]['date'] = array("jour" => (int)date('d'), "mois" => (int)date('m'), "annee" => (int)date('Y'), "heure" => (int)date('H'), "minute" => (int)date('i'), "seconde" => (int)date('s'));
        if($commande['reduction']==true){
            $data[$mail]['point_fidelite'] -= 300;
        }
        $data[$mail]['point_fidelite'] += intval($montant_recu);
        foreach($new_cmd[aff_num_cmd($num)]['plats'] as $id => $name){
            $new_cmd[aff_num_cmd($num)]['plats'][$id]['note'] = 0;
        }
        foreach($new_cmd[aff_num_cmd($num)]['menus'] as $id => $name){
            $new_cmd[aff_num_cmd($num)]['menus'][$id]['note'] = 0;
        }
        file_put_contents("donnees/data.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        file_put_contents("donnees/commande.json", json_encode($new_cmd, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        unlink("donnees/panier_$mail.json");
        unset($panier[$mail]);
        file_put_contents("donnees/panier.json", json_encode($panier, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        header("Location: confirmation.php");
        exit;
    }
    else{
        header("Location: annulation.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Les Croquettes du Chef</title>
</head>
    <body>
    </body>
</html>
