<?php
require_once 'funciones.php';
require_once 'usuario.php';
require_once 'sessionManager.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Gestor Tareas</title>
</head>
<body>
    <header><h3>Gestor Tareas</h3></header>
    <main>
        <?php
        if($sesion->exists('usuario')){
            $usuario = $sesion->get('usuario');
            formIngresarTareas();
            muestraTablaTareas($usuario);
            echo "<a href='cierreSesion.php'>Cerrar Sesion</a>";
        } else {
            echo "No estas logeado XD";
            echo "<a href='index.php'>Volver a inicio</a>";
        }


        ?>
    </main>
    
</body>
</html>