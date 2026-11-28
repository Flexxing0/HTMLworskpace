<?php

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../modelo/Partida.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

Sesion::exigirJugador(false, '../index.php', 'adminIndexView.php');

$partidaActiva = Partida::cargarDesdeSession();
if ($partidaActiva === null)
{
    header('Location: menuView.php');
    exit;
}

$estadisticasNav = UsuarioDAO::obtenerEstadisticasBienvenida(Sesion::getIdUsuario());
$puntajeNavInicial = ($estadisticasNav !== null) ? (int) $estadisticasNav['puntajeAcumulado'] : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Adivine la palabra - Partida en curso</title>
</head>
<body onLoad="inicializarPartida();">
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Partida en curso</p>
        </header>

        <nav class="barra-navegacion">
            <div class="enlaces-nav">
                <a href="menuView.php">Menu principal</a>
                <a href="partidaView.php" class="activo">Partida en curso</a>
                <a href="rankingView.php">Top 10</a>
            </div>
            <div class="enlaces-nav">
                <span class="info-usuario-nav">
                    Jugador: @<?php echo htmlspecialchars(Sesion::getNombreUsuario(), ENT_QUOTES, 'UTF-8'); ?>
                    (<span id="puntajeNav"><?php echo $puntajeNavInicial; ?></span> pts)
                </span>
                <a href="../controladores/logoutController.php" class="enlace-salir">Cerrar sesion</a>
            </div>
        </nav>

        <section>
            <!-- Informacion siempre visible de la partida (RF-12) -->
            <article>
                <h2>Informacion de la partida</h2>

                <div class="grilla-info-partida">
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Categoria</span>
                        <span id="infoCategoria" class="valor-metrica">---</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Dificultad</span>
                        <span id="infoDificultad" class="valor-metrica">---</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Cantidad de letras</span>
                        <span id="infoLongitud" class="valor-metrica">---</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Tiempo transcurrido</span>
                        <span id="infoTiempoTranscurrido" class="valor-metrica">---</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Tiempo restante</span>
                        <span id="infoTiempoRestante" class="valor-metrica">---</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Intentos</span>
                        <span id="infoIntentos" class="valor-metrica">0</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Letras adivinadas</span>
                        <span id="infoAdivinadas" class="valor-metrica">0</span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Pistas mostradas</span>
                        <span id="infoPistas" class="valor-metrica">0</span>
                    </div>
                </div>

                <div id="contenedorBarraTiempo" class="barra-tiempo-contenedor oculto">
                    <label for="barraTiempo" class="texto-ayuda">Progreso del tiempo de la partida:</label>
                    <progress id="barraTiempo" value="0" max="60"></progress>
                </div>

                <!-- Palabra enmascarada -->
                <div class="panel-palabra">
                    <div id="contenedorMascara" class="mascara-letras"></div>
                </div>

                <!-- Letras arriesgadas -->
                <div>
                    <strong>Letras arriesgadas:</strong>
                    <div id="listaLetrasArriesgadas" class="lista-letras-arriesgadas">
                        <span class="texto-ayuda">Ninguna todavia.</span>
                    </div>
                </div>
            </article>

            <!-- Retroalimentacion del ultimo movimiento -->
            <div id="mensajeMovimiento" class="alerta alerta-info oculto"></div>

            <!-- Controles activos durante la partida (arriesgar letras y abandonar) -->
            <article id="panelControlesJuego">
                <h3>Arriesgar una letra</h3>

                <form id="frmArriesgarLetra" name="frmArriesgarLetra" method="post" action="#" onSubmit="return arriesgarDesdeFormulario();">
                    <div class="ingreso-letra-fila">
                        <label for="txtLetra"><strong>Letra:</strong></label>
                        <input type="text"
                               id="txtLetra"
                               name="txtLetra"
                               maxlength="1"
                               autocomplete="off"
                               autofocus
                               placeholder="?">
                        <input type="submit" id="btnArriesgar" name="btnArriesgar" value="Arriesgar letra">
                        <input type="button"
                               id="btnAbandonar"
                               name="btnAbandonar"
                               class="boton-peligro"
                               value="Abandonar e iniciar una nueva"
                               onClick="abandonarPartida();">
                    </div>
                </form>

                <p class="texto-ayuda texto-centro">
                    Tambien podes hacer clic en cualquier letra del teclado (A-Z y Ñ):
                </p>
                <div id="tecladoVirtual" class="teclado-virtual"></div>
            </article>

            <!-- Resultado final (visible al terminar la partida) -->
            <article id="panelResultadoFinal" class="panel-resultado-final oculto">
                <h2 id="tituloResultadoFinal">Resultado de la partida</h2>
                <p id="detalleResultadoFinal"></p>
                <p>
                    <strong>Palabra revelada:</strong>
                    <span id="palabraReveladaFinal"></span>
                </p>
                <p id="puntajeResultadoFinal"></p>
                <div id="avisoPersistenciaFinal" class="alerta alerta-info oculto"></div>

                <div class="acciones-formulario">
                    <a href="menuView.php" class="boton">Iniciar una nueva partida</a>
                    <a href="rankingView.php" class="boton boton-azul">Ver pantalla completa del Top 10</a>
                </div>
            </article>

            <!-- Top 10 de jugadores mostrado automaticamente luego del resultado (RF-17) -->
            <article id="panelTop10Partida" class="oculto">
                <h2>Top 10 - Mejores jugadores</h2>
                <p id="resumenPosicionTop10" class="texto-ayuda"></p>
                <div id="estadoCargaTop10" class="alerta alerta-info oculto">
                    Cargando tabla de posiciones...
                </div>

                <div class="tabla-scroll-contenedor">
                    <table id="tablaTop10Partida">
                        <caption>Top 10 de puntajes acumulados</caption>
                        <thead>
                            <tr>
                                <th>Puesto</th>
                                <th>Jugador</th>
                                <th>Puntaje acumulado</th>
                            </tr>
                        </thead>
                        <tbody id="cuerpoTop10Partida"></tbody>
                    </table>
                </div>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>

    <script src="../js/utilidades.js"></script>
    <script src="../js/partida.js"></script>
</body>
</html>
