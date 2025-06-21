<?php
require_once 'sessionManager.php';
require_once 'funciones.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>LabNro2</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header><h3>Encuentra el centro numerico</h3></header>
    <section>
        <?php
        if($sesion->exists('usuario')){
            $usuario = $sesion->get('usuario');
            generaCuadro($usuario);
            $sesion->set('usuario',$usuario);
        } else {
            $usuario = new Usuario(0,0);
            $usuario->setCentro();
            generaCuadro($usuario);
            $sesion->set('usuario',$usuario);
        }
        ?>
    </section>
<footer></footer>
</body>
</html>