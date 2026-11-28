<?php
/* Archivo: controladores/logoutController.php
 * Cierra la sesion activa de forma segura (RF-04). Si un jugador cierra
 * sesion teniendo una partida sin finalizar, se registra como abandonada.
 */

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
