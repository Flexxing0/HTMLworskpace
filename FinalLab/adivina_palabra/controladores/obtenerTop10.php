<?php

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

Sesion::exigirJugador(true);

header('Content-Type: application/json; charset=utf-8');

$obj = new stdClass();
$obj->ok = false;
$obj->mensaje = '';
$obj->top10 = array();

try
{
    $idUsuarioActual = Sesion::getIdUsuario();
    $ranking = UsuarioDAO::obtenerTop10();
    $estadisticas = UsuarioDAO::obtenerEstadisticasBienvenida($idUsuarioActual);

    $lista = array();
    $estaEnTop10 = false;
    $puestoActual = null;

    for ($i = 0; $i < count($ranking); $i++)
    {
        $fila = $ranking[$i];
        $esActual = ($fila->id === $idUsuarioActual);
        if ($esActual)
        {
            $estaEnTop10 = true;
            $puestoActual = $fila->puesto;
        }

        $item = new stdClass();
        $item->puesto = $fila->puesto;
        $item->id = $fila->id;
        $item->nombreUsuario = $fila->nombreUsuario;
        $item->puntajeAcumulado = $fila->puntajeAcumulado;
        $item->esUsuarioActual = $esActual;
        $lista[] = $item;
    }

    $obj->ok = true;
    $obj->mensaje = 'Top 10 obtenido correctamente.';
    $obj->idUsuarioActual = $idUsuarioActual;
    $obj->nombreUsuarioActual = Sesion::getNombreUsuario();
    $obj->puntajeAcumuladoActual = ($estadisticas !== null) ? (int) $estadisticas['puntajeAcumulado'] : 0;
    $obj->estaEnTop10 = $estaEnTop10;
    $obj->puestoUsuarioActual = $puestoActual;
    $obj->top10 = $lista;
}
catch (Throwable $e)
{
    $obj->ok = false;
    $obj->mensaje = 'No se pudo cargar el ranking Top 10.';
}

echo json_encode($obj);
?>
