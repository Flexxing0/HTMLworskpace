<?php
require_once 'sessionManager.php';
require_once 'cookieManager.php';

$cookieManager = new CookieManager();
$sesion = new SessionManager();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre']) && isset($_POST['contrasena'])) {
    $nombre = $_POST['nombre'];
    $contrasena = $_POST['contrasena'];
    $visitas = 0;
    if ($cookieManager->exists($nombre)) {
        $datos = $cookieManager->getJson($nombre);
        if (password_verify($contrasena, $datos['contrasena'])) {
            $visitas = $datos['visitas'] + 1;
        } else {
            echo "<p>Credenciales incorrectas, vuelva a ingresar</p><br>";
            echo "<a href='index.php'>Volver a ingresar</a>";
            return;
        }
    }
    if (isset($_POST['recordarme'])) {
        $cookieManager->setConToken($nombre);
    }
    $usuario = ['nombre' => $nombre, 'contrasena' => password_hash($contrasena, PASSWORD_DEFAULT), 'visitas' => $visitas];
    $cookieManager->setJson($nombre, $usuario);
    $sesion->set('usuario', $usuario);
    echo "<p>Inicio de sesion exitoso</p>";
    echo "<a href='index.php'>Continuar </a>";
}

?>