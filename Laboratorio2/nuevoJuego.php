<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
require_once 'funciones.php';
$sesion = new SessionManager();

if($_SERVER['REQUEST_METHOD']==='POST' )
{
    if($sesion->exists('usuario')){
    $usuario = $sesion->get('usuario');
    $partidas = $usuario->getNrojuego() + 1;
    $nuevoJuego = new Usuario($partidas);
    $sesion->set('usuario',$nuevoJuego);
    } else {
        $usuario = new Usuario(0);
        $sesiont->set('usuario', $usuario);
    }
    header("Location: index.php");
    exit;
    
} 
?>

