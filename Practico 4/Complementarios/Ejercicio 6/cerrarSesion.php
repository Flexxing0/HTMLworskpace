<?php
require_once 'sessionManager.php';
$sesion = new SessionManager();
if ($sesion->exists('usuario')){
    $sesion->clearAll();
    setcookie('recordarme', '', time() - 3600, '/');
    header("Location: index.php");
} else{
    echo "<p>Logeate wacho</p>";
    echo "<a href='index.php'>Inicio</a>";
}

?>