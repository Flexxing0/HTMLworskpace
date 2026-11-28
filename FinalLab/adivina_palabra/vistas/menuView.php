<?php
/* Archivo: vistas/menuView.php
 * Vista principal del jugador autenticado: mensaje personalizado de bienvenida
 * (RF-05), control de partida activa (A-19) y configuracion de nueva partida (RF-06).
 */

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../modelo/Partida.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';
require_once __DIR__ . '/../dao/CategoriaDAO.php';
require_once __DIR__ . '/../dao/PartidaDAO.php';

// Acceso exclusivo para jugadores autenticados (RF-21)
Sesion::exigirJugador(false, '../index.php', 'adminIndexView.php');

$idUsuario = Sesion::getIdUsuario();
$mensajeError = (string) Sesion::obtenerFlash('menu_error', '');
$mensajeInfo = (string) Sesion::obtenerFlash('menu_info', '');
$categoriaElegida = (int) Sesion::obtenerFlash('menu_cat', 0);
$nivelElegido = (string) Sesion::obtenerFlash('menu_nivel', 'baja');
$tiempoElegido = (string) Sesion::obtenerFlash('menu_tiempo', '0');

// Revisa si queda una partida activa sin finalizar en sesion (A-19)
$partidaEnCurso = Partida::cargarDesdeSession();
if ($partidaEnCurso !== null && $partidaEnCurso->estaFinalizada())
{
    Partida::limpiarSession();
    $partidaEnCurso = null;
}

// Si la partida en curso tenia tiempo y ya vencio en servidor, se cierra automaticamente (A-02 Opcion A)
if ($partidaEnCurso !== null && $partidaEnCurso->estaTiempoVencido())
{
    $partidaEnCurso->finalizarPorTiempo();
    PartidaDAO::guardarResultadoTransaccion($partidaEnCurso);
    $resVencida = $partidaEnCurso->getResultado();
    $ptsVencida = $partidaEnCurso->getPuntajeSumado();
    Partida::limpiarSession();
    $partidaEnCurso = null;
    $mensajeInfo = 'Tu partida anterior finalizo porque se agoto el tiempo (resultado: ' . $resVencida . ', puntos acumulados: ' . $ptsVencida . ').';
}

$estadisticas = UsuarioDAO::obtenerEstadisticasBienvenida($idUsuario);
$categorias = CategoriaDAO::listarActivas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Adivine la palabra - Menu principal</title>
</head>
<body onLoad="cargarPreferenciasMenu();">
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Panel del jugador</p>
        </header>

        <nav class="barra-navegacion">
            <div class="enlaces-nav">
                <a href="menuView.php" class="activo">Menu principal</a>
                <a href="rankingView.php">Top 10</a>
                <?php
                if ($partidaEnCurso !== null)
                {
                    echo '<a href="partidaView.php">Partida en curso</a>';
                }
                ?>
            </div>
            <div class="enlaces-nav">
                <span class="info-usuario-nav">
                    Jugador: @<?php echo htmlspecialchars(Sesion::getNombreUsuario(), ENT_QUOTES, 'UTF-8'); ?>
                    (<?php echo $estadisticas ? (int) $estadisticas['puntajeAcumulado'] : 0; ?> pts)
                </span>
                <a href="../controladores/logoutController.php" class="enlace-salir">Cerrar sesion</a>
            </div>
        </nav>

        <div class="disposicion-dos-columnas">
            <div class="columna-principal">
                <section>
                    <!-- Mensaje personalizado de bienvenida (RF-05) -->
                    <?php if ($estadisticas !== null) { ?>
                    <article class="tarjeta-bienvenida">
                        <p class="saludo-principal">
                            <?php echo htmlspecialchars($estadisticas['lineaSaludo'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <p class="ultimo-juego">
                            <?php echo htmlspecialchars($estadisticas['lineaUltimoJuego'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <p class="texto-aliento">
                            <?php echo htmlspecialchars($estadisticas['lineaAliento'], ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                        <div class="resumen-estadisticas">
                            <span class="insignia-dato">
                                <strong>Puntaje acumulado:</strong> <?php echo (int) $estadisticas['puntajeAcumulado']; ?> pts
                            </span>
                            <span class="insignia-dato">
                                <strong>Partidas jugadas:</strong> <?php echo (int) $estadisticas['totalPartidas']; ?>
                            </span>
                            <span class="insignia-dato">
                                <strong>Ganadas:</strong> <?php echo (int) $estadisticas['ganadas']; ?>
                                <?php
                                if ($estadisticas['tienePartidas'])
                                {
                                    echo ' (' . (int) $estadisticas['porcentajeGanadas'] . '%)';
                                }
                                ?>
                            </span>
                        </div>
                    </article>
                    <?php } ?>

                    <!-- Aviso de partida en curso (decision A-19) -->
                    <?php if ($partidaEnCurso !== null) { ?>
                    <article>
                        <div class="alerta alerta-aviso">
                            Tenes una partida en curso sin finalizar. Podes retomarla o abandonarla antes de empezar una nueva.
                        </div>
                        <div class="acciones-formulario">
                            <a href="partidaView.php" class="boton boton-azul">Continuar partida en curso</a>
                            <form method="post" action="../controladores/iniciarPartida.php" onSubmit="return confirmarAbandonoDesdeMenu();">
                                <input type="submit"
                                       id="btnAbandonarActiva"
                                       name="btnAbandonarActiva"
                                       class="boton-peligro"
                                       value="Abandonar partida actual">
                            </form>
                        </div>
                    </article>
                    <?php } ?>

                    <!-- Configuracion e inicio de nueva partida (RF-06) -->
                    <article>
                        <h2>Configurar nueva partida</h2>

                        <?php
                        if ($mensajeInfo !== '')
                        {
                            echo '<div class="alerta alerta-info">' . htmlspecialchars($mensajeInfo, ENT_QUOTES, 'UTF-8') . '</div>';
                        }
                        if ($mensajeError !== '')
                        {
                            echo '<div class="alerta alerta-error">' . htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') . '</div>';
                        }
                        ?>

                        <div id="errorClienteMenu" class="alerta alerta-error oculto"></div>

                        <form id="frmNuevaPartida" name="frmNuevaPartida" method="post" action="../controladores/iniciarPartida.php" onSubmit="return validarNuevaPartida();">
                            <fieldset <?php echo ($partidaEnCurso !== null) ? 'disabled' : ''; ?>>
                                <legend>Elegi los parametros de juego</legend>

                                <div class="campo-formulario">
                                    <label for="cmbCategoria">Categoria de la palabra</label>
                                    <select id="cmbCategoria" name="cmbCategoria" required>
                                        <option value="0">-- Selecciona una categoria --</option>
                                        <?php
                                        for ($i = 0; $i < count($categorias); $i++)
                                        {
                                            $cat = $categorias[$i];
                                            $selected = ($categoriaElegida === $cat->getId()) ? ' selected' : '';
                                            echo '<option value="' . $cat->getId() . '"' . $selected . '>'
                                                . htmlspecialchars($cat->getNombre(), ENT_QUOTES, 'UTF-8')
                                                . ' (' . $cat->getCantidadPalabrasActivas() . ' palabras)</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="campo-formulario">
                                    <label>Nivel de dificultad</label>
                                    <div class="grupo-radios">
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnDifBaja" name="rdnDificultad" value="baja"
                                                   <?php echo ($nivelElegido === 'baja') ? 'checked' : ''; ?>>
                                            Baja (3 a 5 letras - x1)
                                        </label>
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnDifMedia" name="rdnDificultad" value="media"
                                                   <?php echo ($nivelElegido === 'media') ? 'checked' : ''; ?>>
                                            Media (6 a 8 letras - x2)
                                        </label>
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnDifAlta" name="rdnDificultad" value="alta"
                                                   <?php echo ($nivelElegido === 'alta') ? 'checked' : ''; ?>>
                                            Alta (9 o mas letras - x3)
                                        </label>
                                    </div>
                                </div>

                                <div class="campo-formulario">
                                    <label>Limite de tiempo</label>
                                    <div class="grupo-radios">
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnTiempoSin" name="rdnTiempo" value="0"
                                                   <?php echo ($tiempoElegido === '0') ? 'checked' : ''; ?>>
                                            Sin tiempo
                                        </label>
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnTiempo1" name="rdnTiempo" value="60"
                                                   <?php echo ($tiempoElegido === '60') ? 'checked' : ''; ?>>
                                            1 minuto
                                        </label>
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnTiempo3" name="rdnTiempo" value="180"
                                                   <?php echo ($tiempoElegido === '180') ? 'checked' : ''; ?>>
                                            3 minutos
                                        </label>
                                        <label class="opcion-radio">
                                            <input type="radio" id="rdnTiempo5" name="rdnTiempo" value="300"
                                                   <?php echo ($tiempoElegido === '300') ? 'checked' : ''; ?>>
                                            5 minutos
                                        </label>
                                    </div>
                                </div>

                                <div class="acciones-formulario">
                                    <input type="submit" id="btnIniciarPartida" name="btnIniciarPartida" value="Comenzar partida">
                                </div>
                            </fieldset>
                        </form>
                    </article>
                </section>
            </div>

            <aside class="panel-lateral">
                <h3>Como se juega</h3>
                <ul>
                    <li>Arriesga una letra por vez (A-Z y Ñ, sin tildes).</li>
                    <li><strong>Acierto:</strong> se revelan todas las ocurrencias de la letra y sumas <strong>1 punto</strong>.</li>
                    <li><strong>Error:</strong> el sistema descubre una <strong>pista</strong> (la primera letra aun oculta, en todas sus posiciones) y suma 1 pista.</li>
                    <li><strong>Resultado:</strong> ganas si adivinadas &gt; pistas; empate si son iguales; perdes en otro caso.</li>
                    <li><strong>Puntaje acumulado:</strong> en victoria o empate se multiplica por <strong>x1</strong> (baja), <strong>x2</strong> (media) o <strong>x3</strong> (alta).</li>
                    <li>Si abandonas la partida, cuenta como perdida y acumula 0 puntos.</li>
                </ul>
            </aside>
        </div>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>

    <script src="../js/utilidades.js"></script>
    <script src="../js/menu.js"></script>
</body>
</html>
