<?php
require_once 'cookieManager.php';

$cookieManager = new CookieManager();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = trim($_POST['usuario']);
    $cantidad = $_POST['cantidad'];

    if (!empty($usuario) && is_numeric($cantidad) && $cantidad > 0) {
        $cookieManager->set("usuario", $usuario);
        $cookieManager->set("cantidad", (string)$cantidad);
    }

    header("Location: index.php");
    exit;
}

?>