<?php
require_once 'sessionManager.php';
require_once 'tablero.php';
$sesion = new SessionManager();
if($_SERVER['REQUEST_METHOD'] === "POST")
{
    $tablero = $sesion->get('tablero');
    $tablero->generaCasillas();
    $sesion->set('tablero',$tablero);
    header("Location: index.php");
    exit;
}

?>