<?php
require_once 'tablero.php';
function generaTablero(array $casillas)
{
    $arrays = $casillas;
    echo '<form name="formTablero" action="nuevoTablero.php" method="POST">';
    echo '<input type="submit" value="Nuevo tablero"><br>';
    echo '<input type="submit" value="Eliminar tablero" formaction="eliminaTablero.php"><br>';
    echo '</form>';
    echo '<div class="grid-container">';
    for($i=0 ; $i<count($arrays) ; $i++){
        generaCasilla($arrays[$i]);
    }
   echo '</div>';
}

function generaCasilla(array $numeros)
{
    $array = array_chunk($numeros,3);
    echo "<table>";
    for($i=0 ; $i<3 ; $i++){
        echo "<tr>";
        $otro = $array[$i];
        for ($e=0; $e<count($otro); $e++)
        {
            echo "<td>".$otro[$e]."</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
}

?>
