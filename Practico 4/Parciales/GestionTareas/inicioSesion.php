<?php
require_once 'cookieManager.php';
require_once 'usuario.php';
require_once 'sessionManager.php';
require_once 'funciones.php';

$cookieManager = new CookieManager();
$sesion = new SessionManager();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'])) {
    $nombre = trim($_POST['nombre']);

    if ($cookieManager->exists($nombre)) {
        $datos = $cookieManager->getJson($nombre);
        if ($nombre === $datos['nombre']) {
            $usuario = new Usuario($datos['nombre']);
            $usuario->setArrayTarea($datos['tareasPendientes'],"Pendiente");
            $usuario->setArrayTarea($datos['tareasFinalizadas'],"Finalizada");
            $sesion->set('usuario', $usuario);
            mensajeInicioSesion();
        } else {
            echo "<p>Usuario invalido, vuelva a ingresar el nombre</p>";
            echo "<a href='index.php'>Volver a ingresar</a>";
        }
    } else {
        $usuario = new Usuario($nombre);
        $sesion->set('usuario', $usuario);
        $cookieManager->setJson($nombre, $usuario->toArray());
        mensajeInicioSesion();
    }

}

?>