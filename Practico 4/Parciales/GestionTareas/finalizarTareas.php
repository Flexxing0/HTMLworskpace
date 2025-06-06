<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
$sesion = new SessionManager();

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pendiente']) && $sesion->exists('usuario')){
        $usuario = $sesion->get('usuario');
        $pendientes = $usuario->getPendientes();
        $finalizadas = $usuario->getFinalizadas();
        $checks = $_POST['pendiente'];
        for ($i = 0;$i<count($checks) ; $i++){
            $indice = $checks[$i];
            $elemento = $pendientes[$indice];
            array_push($finalizadas,$elemento);
            unset($pendientes[$indice]);
        } 
        $pendientes = array_values($pendientes);
        $usuario->setArrayTarea($pendientes,"Pendiente");
        $usuario->setArrayTarea($finalizadas,"Finalizada");
        $sesion->set('usuario',$usuario);
        header("Location: pagTareas.php");
        exit;
    }

?>
