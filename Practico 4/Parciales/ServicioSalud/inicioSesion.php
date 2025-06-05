<?php
require_once 'cookieManager.php';
require_once 'usuario.php';
require_once 'sessionManager.php';

$cookieManager = new CookieManager();
$sesion = new SessionManager();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dniIngresado = trim($_POST['dni']);
    $claveIngresada = trim($_POST['clave']);

    if ($cookieManager->exists($dniIngresado)) {
        $usuario = $cookieManager->getJson($dniIngresado);
        if ($dniIngresado === $usuario->getDni() && $claveIngresada === $usuario->getNombre()) {
            $sesion->set('usuario', $usuario);
            header("Location: index.php");
            exit;
        }
    }

}

?>