<?php

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../modelo/Partida.php';
require_once __DIR__ . '/../dao/PartidaDAO.php';

Sesion::iniciar();

if (Sesion::esJugador())
{
    $partidaActiva = Partida::cargarDesdeSession();
    if ($partidaActiva !== null && !$partidaActiva->estaFinalizada())
    {
        $partidaActiva->abandonar();
        PartidaDAO::guardarResultadoTransaccion($partidaActiva);
    }
}

Sesion::cerrar();
header('Location: ../index.php?salir=ok');
exit;
?>
