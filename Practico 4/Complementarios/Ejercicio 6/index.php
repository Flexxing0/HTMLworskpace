<?php
require_once 'sessionManager.php';
require_once 'cookieManager.php';
require_once 'funciones.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Recordarme</title>
</head>
<body>
    <main>
        <?php
        $cookieManager = new CookieManager();
        if (!$sesion->exists('usuario') && $cookieManager->exists('recordarme')) 
        {
            list($nombre, $token) = explode(':',$cookieManager->get('recordarme'));
            if ($cookieManager->exists($nombre))
            {
                $datos = $cookieManager->getJson($nombre);
                $conteo = $datos['visitas'] + 1;
                $datos['visitas'] = $conteo;
                $cookieManager->setJson($datos['nombre'],$datos);
                $sesion->set('usuario',$datos);
                echo "<p>Bienvenido ".$datos['nombre']."</p><br>";
                echo "<p>Es tu visita ".$datos['visitas']."</p><br>";
                echo "<a href='cerrarSesion.php'>Cerrar sesion</a>";
            }
        } else if($sesion->exists('usuario')){
            $datos = $sesion->get('usuario');
            echo "<p>Bienvenido ".$datos['nombre']."</p><br>";
            echo "<p>Es tu visita ".$datos['visitas']."</p><br>";
            echo "<p>No se aumentaron las visitas</p>";
            echo "<a href='cerrarSesion.php'>Cerrar sesion</a>";
        } else{
            formularioInicioSesion();
        }
        ?>
    </main>
    
</body>
</html>