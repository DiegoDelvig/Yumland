<?php
if (!isset($_COOKIE["client"])) {
    header("Content-Type: application/json");
    echo json_encode(["bloque" => false]);
}

$client = json_decode($_COOKIE["client"], true);
$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);

if (
    isset($data[$client["email"]]) &&
    $data[$client["email"]]["role"]["bloque"] == true
) {
    setcookie("client", "", time() - 3600);
    header("Content-Type: application/json");
    echo json_encode(["bloque" => true, "message" => "Vous avez été bloqué"]);
    exit();
}

header("Content-Type: application/json");
echo json_encode(["bloque" => false]);
?>
