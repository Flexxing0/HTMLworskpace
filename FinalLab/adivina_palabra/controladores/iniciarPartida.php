<?php
/* Archivo: controladores/iniciarPartida.php
 * Controlador POST del menu del jugador: gestiona el abandono de una partida
 * activa pendiente (A-19) o el sorteo e inicio de una nueva partida (RF-06, RF-07, A-18).
 */

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../modelo/Partida.php';
require_once __DIR__ . '/../dao/CategoriaDAO.php';
require_once __DIR__ . '/../dao/PalabraDAO.php';
require_once __DIR__ . '/../dao/PartidaDAO.php';

Sesion::exigirJugador(false, '../index.php', '../vistas/adminIndexView.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ../vistas/menuView.php');
    exit;
}

$idUsuario = Sesion::getIdUsuario();

// Caso 1: Abandono explicito de una partida activa desde el menu (A-19)
if (isset($_POST['btnAbandonarActiva']))
{
    $partidaActiva = Partida::cargarDesdeSession();
    if ($partidaActiva !== null && !$partidaActiva->estaFinalizada())
    {
        $partidaActiva->abandonar();
        PartidaDAO::guardarResultadoTransaccion($partidaActiva);
        Partida::limpiarSession();
        Sesion::guardarFlash('menu_info', 'La partida en curso fue abandonada y registrada como perdida (0 puntos). Ya podes iniciar una nueva.');
    }
    else
    {
        Partida::limpiarSession();
    }

    header('Location: ../vistas/menuView.php');
    exit;
}

// Caso 2: Inicio de una nueva partida (RF-06, RF-07, A-18)
$categoriaElegida = isset($_POST['cmbCategoria']) ? (int) $_POST['cmbCategoria'] : 0;
$nivelElegido = isset($_POST['rdnDificultad']) ? trim($_POST['rdnDificultad']) : '';
$tiempoElegido = isset($_POST['rdnTiempo']) ? trim($_POST['rdnTiempo']) : '0';

$partidaEnCurso = Partida::cargarDesdeSession();
if ($partidaEnCurso !== null && $partidaEnCurso->estaFinalizada())
{
    Partida::limpiarSession();
    $partidaEnCurso = null;
}

$error = '';
if ($partidaEnCurso !== null)
{
    $error = 'Tenes una partida en curso. Continuala o abandonala antes de iniciar una nueva.';
}
else if (!in_array($nivelElegido, array('baja', 'media', 'alta'), true))
{
    $error = 'Selecciona un nivel de dificultad valido (baja, media o alta).';
}
else if (!in_array($tiempoElegido, array('0', '60', '180', '300'), true))
{
    $error = 'Selecciona una opcion de tiempo valida (sin tiempo, 1, 3 o 5 minutos).';
}
else if ($categoriaElegida <= 0 || CategoriaDAO::buscarActivaPorId($categoriaElegida) === null)
{
    $error = 'Selecciona una categoria valida.';
}

if ($error !== '')
{
    Sesion::guardarFlash('menu_error', $error);
    Sesion::guardarFlash('menu_cat', $categoriaElegida);
    Sesion::guardarFlash('menu_nivel', $nivelElegido);
    Sesion::guardarFlash('menu_tiempo', $tiempoElegido);
    header('Location: ../vistas/menuView.php');
    exit;
}

try
{
    $palabraSorteada = PalabraDAO::obtenerAleatoria($categoriaElegida, $nivelElegido);
    if ($palabraSorteada === null)
    {
        // Decision A-18: combinacion categoria + dificultad sin palabras disponibles
        Sesion::guardarFlash('menu_error', 'No hay palabras disponibles para esa categoria y dificultad. Proba otra combinacion.');
        Sesion::guardarFlash('menu_cat', $categoriaElegida);
        Sesion::guardarFlash('menu_nivel', $nivelElegido);
        Sesion::guardarFlash('menu_tiempo', $tiempoElegido);
        header('Location: ../vistas/menuView.php');
        exit;
    }

    $tiempoLimiteSeg = ($tiempoElegido === '0') ? null : (int) $tiempoElegido;
    $nuevaPartida = new Partida();
    $nuevaPartida->iniciarNueva($idUsuario, $palabraSorteada, $nivelElegido, $tiempoLimiteSeg);
    $nuevaPartida->guardarEnSession();

    header('Location: ../vistas/partidaView.php');
    exit;
}
catch (Throwable $e)
{
    Sesion::guardarFlash('menu_error', 'No se pudo iniciar la partida. Intenta nuevamente.');
    header('Location: ../vistas/menuView.php');
    exit;
}
?>
