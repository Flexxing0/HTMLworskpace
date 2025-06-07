<?php
require_once 'usuario.php';
function generaCuadro(Usuario $usuario)
{
    echo "<p>Puntaje: " . $usuario->getPuntaje() . "</p>";
    echo "<p>Partida: " . $usuario->getNroIntento() . "</p>";
    echo "<form name='formNroIngresado' action='validaNro.php' method='POST'> ";
    echo "<label for='idNroIngresado'>Ingresa el centro numerico</label>";
    echo "<input id='idNroIngresado' name='nroIngresado' type='number' required placeholder='Ej: 10'><br><br>";
    echo "<input type='submit' value='Validar'><br><br>";
    echo "</form>";
    echo "<form name='formNuevoJuego' method='POST' action='nuevoJuego.php'>";
    echo "<input type='submit' value='Nuevo juego'><br>";
    echo "</form>";
    
    muestraIngresados($usuario->getIngresados());
}

function muestraIngresados(array $ingresados)
{
    echo "<p> Ingresados: ";
    if (count($ingresados) > 0) {
        for ($i = 0; $i < count($ingresados); $i++) {
            echo $ingresados[$i].", ";
        }
    } else {
        echo "vacio";
    }
    echo " </p>";
}

function estaEnRango($nro1, $nro2)
{
    if ($nro1 > $nro2) {
        if (($nro1 - $nro2) <= 5) {
            return true;
        } else {
            return false;
        }
    }
}

?>