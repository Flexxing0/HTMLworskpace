<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
$sesion = new SessionManager();

    if($_SERVER['REQUEST_METHOD'] === 'POST' && $sesion->exists('usuario')){
        $usuario = $sesion->get('usuario');
        $usuario->eliminaTareas();
        header("Location: pagTareas.php");
        exit;
    }

?>