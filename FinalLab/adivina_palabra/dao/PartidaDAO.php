<?php
/* Archivo: dao/PartidaDAO.php
 * Data Access Object para la tabla partidas: persistencia atomica de cierre
 * de partida + acumulacion de puntaje en una sola transaccion InnoDB (RF-16)
 * y estadisticas generales para el panel de administracion.
 */

require_once __DIR__ . '/../db/conexion.php';
require_once __DIR__ . '/../modelo/Partida.php';

class PartidaDAO
{
    // Guarda la partida y acumula el puntaje del jugador en una sola transaccion (Unidad 5, RF-16)
    public static function guardarResultadoTransaccion($partida)
    {
        if (!$partida->estaFinalizada() || $partida->estaGuardadaEnBD())
        {
            return false;
        }

        $con = Conexion::conectar();
        $idUsuario = $partida->getIdUsuario();
        $idPalabra = $partida->getIdPalabra();
        $nivel = $partida->getNivel();
        $tiempoLimiteSeg = $partida->getTiempoLimiteSeg();
        $fechaInicio = $partida->getFechaInicioStr();
        $fechaFin = $partida->getFechaFinStr();
        $intentos = $partida->getIntentos();
        $letrasAcertadas = $partida->getLetrasAcertadas();
        $pistas = $partida->getPistas();
        $letrasTexto = implode(',', $partida->getLetrasArriesgadas());
        $resultado = $partida->getResultado();
        $puntajePartida = $partida->getPuntajePartida();
        $puntajeSumado = $partida->getPuntajeSumado();

        try
        {
            $con->begin_transaction();

            $queryPartida = 'INSERT INTO partidas (
                                id_usuario, id_palabra, nivel, tiempo_limite_seg,
                                fecha_inicio, fecha_fin, intentos, letras_acertadas,
                                pistas, letras_arriesgadas, resultado,
                                puntaje_partida, puntaje_sumado
                             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmtPartida = $con->prepare($queryPartida);
            $stmtPartida->bind_param(
                'iisissiiissii',
                $idUsuario,
                $idPalabra,
                $nivel,
                $tiempoLimiteSeg,
                $fechaInicio,
                $fechaFin,
                $intentos,
                $letrasAcertadas,
                $pistas,
                $letrasTexto,
                $resultado,
                $puntajePartida,
                $puntajeSumado
            );
            $stmtPartida->execute();
            $stmtPartida->close();

            if ($puntajeSumado > 0)
            {
                $queryUsuario = 'UPDATE usuarios
                                 SET puntaje_acumulado = puntaje_acumulado + ?
                                 WHERE id = ?';
                $stmtUsuario = $con->prepare($queryUsuario);
                $stmtUsuario->bind_param('ii', $puntajeSumado, $idUsuario);
                $stmtUsuario->execute();
                $stmtUsuario->close();
            }

            $con->commit();
            $partida->marcarGuardadaEnBD();
            $con->close();
            return true;
        }
        catch (Throwable $e)
        {
            $con->rollback();
            $con->close();
            return false;
        }
    }

    // Devuelve totales generales de jugadores y partidas para el tablero del administrador
    public static function obtenerTotalesSistema()
    {
        $totales = array('jugadores' => 0, 'partidas' => 0);

        try
        {
            $con = Conexion::conectar();

            $resuUsuarios = $con->query('SELECT COUNT(*) AS total FROM usuarios');
            if ($regi = $resuUsuarios->fetch_object())
            {
                $totales['jugadores'] = (int) $regi->total;
            }
            $resuUsuarios->free();

            $resuPartidas = $con->query('SELECT COUNT(*) AS total FROM partidas');
            if ($regi = $resuPartidas->fetch_object())
            {
                $totales['partidas'] = (int) $regi->total;
            }
            $resuPartidas->free();

            $con->close();
        }
        catch (Throwable $e)
        {
            if (isset($con))
            {
                $con->close();
            }
        }

        return $totales;
    }
}
?>
