<?php
header("Content-Type: application/json");

if (!isset($_COOKIE["client"])) {
    echo json_encode(["success" => false, "message" => "Non authentifié"]);
    exit();
}

$client = json_decode($_COOKIE["client"], true);
$file = file_get_contents("donnees/data.json");
$data = json_decode($file, true);

if ($data[$client["email"]]["role"]["admin"] != true) {
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    exit();
}

$email_a_bloquer = $_POST["email"] ?? null;

if (!$email_a_bloquer || !isset($data[$email_a_bloquer])) {
    echo json_encode([
        "success" => false,
        "message" => "Utilisateur introuvable",
    ]);
    exit();
}

$nouveau_statut = !$data[$email_a_bloquer]["role"]["bloque"];
$data[$email_a_bloquer]["role"]["bloque"] = $nouveau_statut;

file_put_contents(
    "donnees/data.json",
    json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
);

echo json_encode([
    "success" => true,
    "message" => $nouveau_statut
        ? "Utilisateur bloqué"
        : "Utilisateur débloqué",
    "bloque" => $nouveau_statut,
    "nom" =>
        $data[$email_a_bloquer]["name"] .
        " " .
        $data[$email_a_bloquer]["fname"],
]);
?>
