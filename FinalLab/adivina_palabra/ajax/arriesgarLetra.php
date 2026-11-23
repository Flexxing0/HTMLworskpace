<?php
/* Archivo: ajax/arriesgarLetra.php
 * Endpoint AJAX (POST) que procesa una letra arriesgada por el jugador.
 * Actualiza mascara, aciertos, pistas y, si la partida termina, guarda el
 * resultado y acumula el puntaje en una transaccion InnoDB (RF-09 a RF-16).
 */

require_once __DIR__ . '/../clases/Sesion.class.php';
require_once __DIR__ . '/../clases/Partida.class.php';

Sesion::exigirJugador(true);

header('Content-Type: application/json; charset=utf-8');

$obj = new stdClass();
$obj->ok = false;
$obj->mensaje = 'No se recibio ninguna letra.';

if (!isset($_POST['letra']))
{
    echo json_encode($obj);
    exit;
}

$partida = Partida::cargarDesdeSession();
if ($partida === null)
{
    $obj->sinPartida = true;
    $obj->mensaje = 'No hay una partida activa en curso.';
    echo json_encode($obj);
    exit;
}

try
{
    $resultadoJugada = $partida->arriesgarLetra($_POST['letra']);

    $obj->ok = $resultadoJugada['ok'];
    $obj->accion = $resultadoJugada['accion'];
    $obj->letraPista = $resultadoJugada['letraPista'];
    $obj->mensaje = $resultadoJugada['mensaje'];

    if ($partida->estaFinalizada())
    {
        $guardada = $partida->guardarResultadoTransaccionBD();
        $estadoFinal = $partida->getEstadoPublico();
        $_SESSION['ultima_partida_resumen'] = $estadoFinal;
        Partida::limpiarSession();

        $obj->guardadaBD = $guardada;
        $obj->partida = $estadoFinal;
    }
    else
    {
        $partida->guardarEnSession();
        $obj->partida = $partida->getEstadoPublico();
    }
}
catch (Throwable $e)
{
    $obj->ok = false;
    $obj->mensaje = 'Ocurrio un error al procesar la letra.';
}

echo json_encode($obj);
?>
