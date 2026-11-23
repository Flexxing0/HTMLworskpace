<?php
/* Archivo: ranking.php
 * Pantalla del Top 10 de jugadores con mayor puntaje acumulado (RF-17, A-13).
 * Protegida por sesion de jugador (RF-21). Si existe un resumen de la ultima
 * partida finalizada en la sesion actual, lo muestra encima del ranking.
 */

require_once __DIR__ . '/clases/Sesion.class.php';
require_once __DIR__ . '/clases/Usuario.class.php';
require_once __DIR__ . '/clases/Partida.class.php';

// Acceso exclusivo para jugadores autenticados (RF-21)
Sesion::exigirJugador(false, 'index.php', 'admin/index.php');

$idUsuario = Sesion::getIdUsuario();

// Control de consistencia si habia una partida en curso con tiempo vencido (A-02 Opcion A)
$partidaEnCurso = Partida::cargarDesdeSession();
if ($partidaEnCurso !== null && $partidaEnCurso->estaFinalizada())
{
    Partida::limpiarSession();
    $partidaEnCurso = null;
}
if ($partidaEnCurso !== null && $partidaEnCurso->estaTiempoVencido())
{
    $partidaEnCurso->finalizarPorTiempo();
    $partidaEnCurso->guardarResultadoTransaccionBD();
    $_SESSION['ultima_partida_resumen'] = $partidaEnCurso->getEstadoPublico();
    Partida::limpiarSession();
    $partidaEnCurso = null;
}

$estadisticas = Usuario::obtenerEstadisticasBienvenidaBD($idUsuario);
$top10 = Usuario::obtenerTop10BD();
$ultimaPartida = isset($_SESSION['ultima_partida_resumen']) && is_array($_SESSION['ultima_partida_resumen'])
    ? $_SESSION['ultima_partida_resumen']
    : null;

$puestoUsuario = null;
for ($i = 0; $i < count($top10); $i++)
{
    if ($top10[$i]->id === $idUsuario)
    {
        $puestoUsuario = $top10[$i]->puesto;
        break;
    }
}

$puntajeUsuario = ($estadisticas !== null) ? (int) $estadisticas['puntajeAcumulado'] : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Adivine la palabra - Top 10</title>
</head>
<body>
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Tabla de posiciones - Top 10</p>
        </header>

        <nav class="barra-navegacion">
            <div class="enlaces-nav">
                <a href="menu.php">Menu principal</a>
                <a href="ranking.php" class="activo">Top 10</a>
                <?php
                if ($partidaEnCurso !== null)
                {
                    echo '<a href="partida.php">Partida en curso</a>';
                }
                ?>
            </div>
            <div class="enlaces-nav">
                <span class="info-usuario-nav">
                    Jugador: @<?php echo htmlspecialchars(Sesion::getNombreUsuario(), ENT_QUOTES, 'UTF-8'); ?>
                    (<?php echo $puntajeUsuario; ?> pts)
                </span>
                <a href="logout.php" class="enlace-salir">Cerrar sesion</a>
            </div>
        </nav>

        <section>
            <?php
            if ($ultimaPartida !== null)
            {
                $res = isset($ultimaPartida['resultado']) ? $ultimaPartida['resultado'] : '';
                if ($res === 'ganada')
                {
                    $etiquetaResultado = 'Ganada';
                }
                else if ($res === 'empate')
                {
                    $etiquetaResultado = 'Empate';
                }
                else if ($res === 'abandonada')
                {
                    $etiquetaResultado = 'Abandonada (perdida)';
                }
                else
                {
                    $etiquetaResultado = 'Perdida';
                }
            ?>
            <article class="panel-resultado-final">
                <h2>Resumen de tu ultima partida</h2>
                <div class="resumen-estadisticas">
                    <span class="insignia-dato">
                        <strong>Resultado:</strong> <?php echo htmlspecialchars($etiquetaResultado, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span class="insignia-dato">
                        <strong>Palabra:</strong>
                        <?php echo htmlspecialchars(mb_strtoupper((string) $ultimaPartida['palabraRevelada'], 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span class="insignia-dato">
                        <strong>Categoria:</strong>
                        <?php echo htmlspecialchars((string) $ultimaPartida['categoria'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span class="insignia-dato">
                        <strong>Dificultad:</strong>
                        <?php echo htmlspecialchars(ucfirst((string) $ultimaPartida['nivel']), ENT_QUOTES, 'UTF-8'); ?>
                        (x<?php echo (int) $ultimaPartida['multiplicador']; ?>)
                    </span>
                    <span class="insignia-dato">
                        <strong>Adivinadas / Pistas:</strong>
                        <?php echo (int) $ultimaPartida['letrasAcertadas']; ?> / <?php echo (int) $ultimaPartida['pistas']; ?>
                    </span>
                    <span class="insignia-dato">
                        <strong>Puntaje acumulado en esta partida:</strong>
                        +<?php echo (int) $ultimaPartida['puntajeSumado']; ?> pts
                    </span>
                </div>
            </article>
            <?php
            }
            ?>

            <article>
                <h2>Top 10 - Mejores jugadores</h2>
                <p class="texto-ayuda">
                    Ordenado por mayor puntaje acumulado. En caso de empate en puntaje, se ordena alfabeticamente por nombre de usuario.
                </p>

                <?php
                if (count($top10) === 0)
                {
                ?>
                    <div class="alerta alerta-info">
                        Todavia no hay jugadores registrados en el ranking.
                    </div>
                <?php
                }
                else
                {
                ?>
                    <div class="tabla-scroll-contenedor">
                        <table>
                            <caption>Top 10 de puntajes acumulados</caption>
                            <thead>
                                <tr>
                                    <th>Puesto</th>
                                    <th>Jugador</th>
                                    <th>Puntaje acumulado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($top10); $i++)
                                {
                                    $item = $top10[$i];
                                    $esActual = ($item->id === $idUsuario);
                                    $claseFila = $esActual ? ' class="fila-destacada"' : '';
                                    $marcaActual = $esActual ? ' (Vos)' : '';
                                    echo '<tr' . $claseFila . '>';
                                    echo '<td>#' . (int) $item->puesto . '</td>';
                                    echo '<td>@' . htmlspecialchars($item->nombreUsuario, ENT_QUOTES, 'UTF-8') . $marcaActual . '</td>';
                                    echo '<td>' . (int) $item->puntajeAcumulado . ' pts</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }
                ?>

                <p class="alerta alerta-info">
                    <?php
                    if ($puestoUsuario !== null)
                    {
                        echo 'Tu puntaje acumulado actual es ' . $puntajeUsuario . ' punto(s) y ocupas el puesto #' . (int) $puestoUsuario . ' en el Top 10.';
                    }
                    else
                    {
                        echo 'Tu puntaje acumulado actual es ' . $puntajeUsuario . ' punto(s) (actualmente fuera del Top 10).';
                    }
                    ?>
                </p>

                <div class="acciones-formulario">
                    <?php
                    if ($partidaEnCurso !== null)
                    {
                        echo '<a href="partida.php" class="boton boton-azul">Continuar partida en curso</a>';
                    }
                    ?>
                    <a href="menu.php" class="boton">Volver al menu principal</a>
                </div>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>
</body>
</html>
