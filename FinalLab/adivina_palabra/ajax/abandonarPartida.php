<?php
/* Archivo: ajax/abandonarPartida.php
 * Endpoint AJAX (POST) que registra el abandono voluntario de la partida:
 * se considera perdida, acumula 0 puntos y guarda en BD (RF-13).
 */

require_once __DIR__ . '/../clases/Sesion.class.php';
require_once __DIR__ . '/../clases/Partida.class.php';

Sesion::exigirJugador(true);

header('Content-Type: application/json; charset=utf-8');

$obj = new stdClass();
$obj->ok = false;
$obj->mensaje = 'No hay una partida activa para abandonar.';

$partida = Partida::cargarDesdeSession();
if ($partida === null)
{
    $obj->sinPartida = true;
    echo json_encode($obj);
    exit;
}

try
{
    $partida->abandonar();
    $guardada = $partida->guardarResultadoTransaccionBD();
    $estadoFinal = $partida->getEstadoPublico();
    $_SESSION['ultima_partida_resumen'] = $estadoFinal;
    Partida::limpiarSession();

    $obj->ok = true;
    $obj->guardadaBD = $guardada;
    $obj->mensaje = 'Abandonaste la partida. Se registro como perdida y no acumula puntos.';
    $obj->partida = $estadoFinal;
}
catch (Throwable $e)
{
    $obj->ok = false;
    $obj->mensaje = 'No se pudo registrar el abandono de la partida.';
}

echo json_encode($obj);
?>
