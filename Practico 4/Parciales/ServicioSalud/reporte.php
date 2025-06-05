<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
require_once 'paciente.php';
require_once 'funciones.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Reporte</title>
</head>
<body>
    <main>
        <?php
        if($sesion->exists('usuario')){
            $datos = $sesion->get('usuario');
            reporte($datos);
        }
        ?>
        <a href="index.php">Volver</a>
    </main>
    
</body>
</html>