<?php
/* Archivo: ajax/estadoPartida.php
 * Endpoint AJAX (POST) que devuelve el estado publico seguro de la partida
 * activa en $_SESSION (sin revelar la palabra mientras esta en curso).
 * Si el tiempo vencio en el servidor, cierra y persiste la partida en BD.
 */

require_once __DIR__ . '/../clases/Sesion.class.php';
require_once __DIR__ . '/../clases/Partida.class.php';

Sesion::exigirJugador(true);

header('Content-Type: application/json; charset=utf-8');

$obj = new stdClass();
$obj->ok = false;
$obj->mensaje = '';

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
    // Verifica en el servidor si el tiempo se agoto (A-02 Opcion A)
    if (!$partida->estaFinalizada() && $partida->estaTiempoVencido())
    {
        $partida->finalizarPorTiempo();
        $guardada = $partida->guardarResultadoTransaccionBD();
        $estadoFinal = $partida->getEstadoPublico();
        $_SESSION['ultima_partida_resumen'] = $estadoFinal;
        Partida::limpiarSession();

        $obj->ok = true;
        $obj->guardadaBD = $guardada;
        $obj->mensaje = 'Se agoto el tiempo de la partida.';
        $obj->partida = $estadoFinal;
        echo json_encode($obj);
        exit;
    }

    $obj->ok = true;
    $obj->mensaje = 'Estado de partida cargado.';
    $obj->partida = $partida->getEstadoPublico();
}
catch (Throwable $e)
{
    $obj->ok = false;
    $obj->mensaje = 'No se pudo obtener el estado de la partida.';
}

echo json_encode($obj);
?>
