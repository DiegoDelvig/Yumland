<?php
error_reporting(0);
if (!isset($_COOKIE["client"])) {
    header("Location: index.php");
    exit();
}

$client = json_decode($_COOKIE["client"], true);
$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);

if (
    isset($data[$client["email"]]) &&
    $data[$client["email"]]["role"]["bloque"] == true
) {
    setcookie("client", "", time() - 3600);
    header("Location: index.php?msg=bloque");
    exit();
}
$commande_data = file_get_contents("donnees/commande.json");
$commande = json_decode($commande_data, true);

$commande_passe_data = file_get_contents("donnees/commande_passe.json");
$commande_passe = json_decode($commande_passe_data, true);

function aff_num_cmd_sans_echo($num)
{
    if ($num < 10) {
        return "000" . $num;
    } elseif ($num < 100) {
        return "00" . $num;
    } elseif ($num < 1000) {
        return "0" . $num;
    } else {
        return $num;
    }
}

$client = json_decode($_COOKIE["client"], true);
$cmd = null;
$temps = 0;

foreach ($commande as $id_cmd => $detail) {
    if (
        $detail["etat"]["cuisinee"] == true &&
        $detail["livreur"] == $client["email"]
    ) {
        if ($detail["temps"] >= $temps) {
            $cmd = $detail;
            $temps = $detail["temps"];
        }
    }
}

if ($cmd != null) {
    $mail = $cmd["mail"];
    $new_tab = [
        "num" => $cmd["num"],
        "date" => $cmd["date"],
        "total" => $cmd["total"],
        "plats" => $cmd["plats"],
        "menus" => $cmd["menus"],
    ];
    $commande_passe[$mail][aff_num_cmd_sans_echo($cmd["num"])] = $new_tab;
    unset($commande[aff_num_cmd_sans_echo($cmd["num"])]);

    file_put_contents(
        "donnees/commande_passe.json",
        json_encode(
            $commande_passe,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE,
        ),
    );
    file_put_contents(
        "donnees/commande.json",
        json_encode($commande, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
    );

    echo json_encode(["succes" => true]);
} else {
    echo json_encode(["succes" => false]);
}
?>
