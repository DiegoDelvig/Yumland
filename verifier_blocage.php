<?php
session_start();
if (!isset($_SESSION['client'])) {
    header("Content-Type: application/json");
    echo json_encode(["bloque" => false]);
    exit();
}

$client = $_SESSION['client'];
$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);

if (
    isset($data[$client["email"]]) &&
    $data[$client["email"]]["role"]["bloque"] == true
) {
    unset($_SESSION['client']);
    header("Content-Type: application/json");
    echo json_encode(["bloque" => true, "message" => "Vous avez été bloqué"]);
    exit();
}

header("Content-Type: application/json");
echo json_encode(["bloque" => false]);
?>
