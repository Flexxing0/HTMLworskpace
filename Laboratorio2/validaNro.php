<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
require_once 'funciones.php';
$sesion = new SessionManager();
    if($sesion->exists('usuario') && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nroIngresado']))
    {
        $nroIngresado = (int)$_POST['nroIngresado'];
        $usuario = $sesion ->get('usuario');

        if($usuario->getPuntaje() > 1){
        $usuario->setIngresados($nroIngresado);
        $usuario->setCentro();
        $centro = $usuario->getCentroNumerico();
        $usuario->restaPuntaje();
        $usuario->incrementaNroIntento(); //si inicias nuevo juego esto se reinicia
        if($nroIngresado === $centro)
        {
            echo "Ingresaste el centro correcto, ganaste";
            echo "El centro numerico era: " . $centro = $usuario->getCentroNumerico();
            echo "<form name='formIntentar' method='POST' action='nuevoJuego.php'>";
            echo "<input type='submit' value='Nuevo juego'></form>";
        }        
        else if( estaEnRango($nroIngresado, $centro) || estaEnRango($centro, $nroIngresado)){
            echo "Estas cercar!<br>";
            echo "<a href='index.php'>Volver a ingresar</a>";
        } else {
            echo "Estas lejos!<br>";
            echo "<a href='index.php'>Volver a ingresar</a>";
        }
    } else {
        echo "<p>Perdiste!<br></p>";
        echo "El centro numerico era: " . $centro = $usuario->getCentroNumerico();
        echo "<form name='formIntentar' method='POST' action='nuevoJuego.php'>";
        echo "<input type='submit' value='Nuevo juego'></form>";
    }
    }

?>