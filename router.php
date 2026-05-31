<?php
// Bloque l'accées à tous les urls ayant "donnees/"

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
if (preg_match('#^/donnees(/|$)#', $uri)) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    header('Content-Type: text/plain; charset=UTF-8');
    echo "403 Forbidden - Accès aux fichiers de données refusé.";
    return true; //
}

$requested = __DIR__ . $uri;
if ($uri !== '/' && file_exists($requested) && is_file($requested)) {
    return false;
}

require __DIR__ . '/index.php';
