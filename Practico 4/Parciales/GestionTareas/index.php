<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
require_once 'funciones.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Gestor Tareas</title>
</head>
<body>
    <header><h3>Gestor tareas</h3></header>
    <main>
        <?php
        if ($sesion->exists('usuario')){
            $usuario = $sesion->get('usuario');
            echo "<strong>Bienvenido de vuelta  ".$usuario->getNombre()."</strong><br>";
            echo "<a href='pagTareas.php'>Ir a gestor tareas</a>";
            echo "<a href='cierreSesion.php'>Cerrar Sesion</a>";
        } else {
            formularioRegistro();
        }
        ?>
    </main>
    
</body>
</html>