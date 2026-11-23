<?php
/* Archivo: logout.php
 * Cierra la sesion activa de forma segura (RF-04). Si un jugador cierra
 * sesion teniendo una partida sin finalizar, se registra como abandonada.
 */

require_once __DIR__ . '/clases/Sesion.class.php';
require_once __DIR__ . '/clases/Partida.class.php';

Sesion::iniciar();

if (Sesion::esJugador())
{
    $partidaActiva = Partida::cargarDesdeSession();
    if ($partidaActiva !== null && !$partidaActiva->estaFinalizada())
    {
        $partidaActiva->abandonar();
        $partidaActiva->guardarResultadoTransaccionBD();
    }
}

Sesion::cerrar();
header('Location: index.php?salir=ok');
exit;
?>
