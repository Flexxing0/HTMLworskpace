<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
$sesion = new SessionManager();

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['descripcion']) && $sesion->exists('usuario')){
        $usuario = $sesion->get('usuario');
        $descripcion = $_POST['descripcion'];
        $usuario->setTarea($descripcion,'Pendiente');
        header("Location: pagTareas.php");
        exit;
    }

?>
