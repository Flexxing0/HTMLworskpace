<?php
require_once 'sessionManager.php';
//require_once 'calculadora.php';
require_once 'funciones.php';
require_once 'usuario.php';
require_once 'paciente.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Calculadora</title>
</head>
<body>
    <main>
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $sesion->exists('usuario')) 
        {
            $usuario = $sesion->get('usuario');
            $nombre = $_POST['nombreimc'];
            $peso = (float)$_POST['pesoimc'];
            $altura = (float)$_POST['alturaimc'];
            $paciente = new Paciente($nombre, $peso, $altura);
            $usuario->setIntegrante(calculaIMC($paciente));
            $sesion->set('usuario',$usuario);
        }
        formulaioIMC();
        ?>
        <a href="index.php">Volver a inicio</a><br>
        <a href="reporte.php">Ver reporte</a>
    </main>
    
</body>
</html>