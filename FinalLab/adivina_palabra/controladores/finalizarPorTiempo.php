<?php

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../modelo/Partida.php';
require_once __DIR__ . '/../dao/PartidaDAO.php';

Sesion::exigirJugador(true);

header('Content-Type: application/json; charset=utf-8');

$obj = new stdClass();
$obj->ok = false;
$obj->tiempoVencido = false;
$obj->mensaje = 'No hay una partida activa en curso.';

$partida = Partida::cargarDesdeSession();
if ($partida === null)
{
    $obj->sinPartida = true;
    echo json_encode($obj);
    exit;
}

try
{
    if (!$partida->estaTiempoVencido())
    {
        $obj->ok = true;
        $obj->tiempoVencido = false;
        $obj->mensaje = 'Aun queda tiempo en el servidor.';
        $obj->partida = $partida->getEstadoPublico();
        echo json_encode($obj);
        exit;
    }

    $partida->finalizarPorTiempo();
    $guardada = PartidaDAO::guardarResultadoTransaccion($partida);
    $estadoFinal = $partida->getEstadoPublico();
    $_SESSION['ultima_partida_resumen'] = $estadoFinal;
    Partida::limpiarSession();

    $obj->ok = true;
    $obj->tiempoVencido = true;
    $obj->guardadaBD = $guardada;
    $obj->mensaje = 'Se agoto el tiempo de la partida.';
    $obj->partida = $estadoFinal;
}
catch (Throwable $e)
{
    $obj->ok = false;
    $obj->mensaje = 'No se pudo finalizar la partida por tiempo.';
}

echo json_encode($obj);
?>
