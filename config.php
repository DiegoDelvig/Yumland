<?php
require_once __DIR__ . '/config.php';
$default = realpath(__DIR__ . "/donnees/");

if ($default === false) {
    throw new Exception("Répertoire donnees/ introuvalble.");
}

define("DATA_DIR", $default);

function data_path($filename) {
    return DATA_DIR . "/" . ltrim($filename, "/");
}
?>
