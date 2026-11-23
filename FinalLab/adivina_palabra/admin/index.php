<?php
/* Archivo: admin/index.php
 * Panel principal del usuario administrador (RF-18, RF-21, A-12).
 * Muestra el resumen del catalogo de categorias y palabras activas, y los
 * accesos a los ABM de categorias y palabras.
 */

require_once __DIR__ . '/../clases/Sesion.class.php';
require_once __DIR__ . '/../clases/Conexion.class.php';
require_once __DIR__ . '/../clases/Categoria.class.php';
require_once __DIR__ . '/../clases/Palabra.class.php';

// Acceso exclusivo para el administrador (RF-21)
Sesion::exigirAdmin('../index.php', '../menu.php');

$categoriasActivas = Categoria::listarActivasBD();
$palabrasActivas = Palabra::listarActivasBD();

$totalCategorias = count($categoriasActivas);
$totalPalabras = count($palabrasActivas);
$totalBaja = 0;
$totalMedia = 0;
$totalAlta = 0;

for ($i = 0; $i < $totalPalabras; $i++)
{
    $dif = $palabrasActivas[$i]->getDificultad();
    if ($dif === 'baja')
    {
        $totalBaja++;
    }
    else if ($dif === 'media')
    {
        $totalMedia++;
    }
    else
    {
        $totalAlta++;
    }
}

$totalJugadores = 0;
$totalPartidas = 0;

try
{
    $con = Conexion::conectar();

    $resuUsuarios = $con->query('SELECT COUNT(*) AS total FROM usuarios');
    if ($regi = $resuUsuarios->fetch_object())
    {
        $totalJugadores = (int) $regi->total;
    }
    $resuUsuarios->free();

    $resuPartidas = $con->query('SELECT COUNT(*) AS total FROM partidas');
    if ($regi = $resuPartidas->fetch_object())
    {
        $totalPartidas = (int) $regi->total;
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Adivine la palabra - Panel de Administracion</title>
</head>
<body>
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Panel de Administracion</p>
        </header>

        <nav class="barra-navegacion">
            <div class="enlaces-nav">
                <a href="index.php" class="activo">Inicio Admin</a>
                <a href="categorias.php">ABM Categorias</a>
                <a href="palabras.php">ABM Palabras</a>
            </div>
            <div class="enlaces-nav">
                <span class="info-usuario-nav">
                    Administrador: @<?php echo htmlspecialchars(Sesion::getNombreUsuario(), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <a href="../logout.php" class="enlace-salir">Cerrar sesion</a>
            </div>
        </nav>

        <section>
            <article>
                <h2>Resumen del sistema</h2>

                <div class="grilla-info-partida">
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Categorias activas</span>
                        <span class="valor-metrica"><?php echo $totalCategorias; ?></span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Palabras activas</span>
                        <span class="valor-metrica"><?php echo $totalPalabras; ?></span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Baja (3 a 5)</span>
                        <span class="valor-metrica"><?php echo $totalBaja; ?></span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Media (6 a 8)</span>
                        <span class="valor-metrica"><?php echo $totalMedia; ?></span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Alta (9 o mas)</span>
                        <span class="valor-metrica"><?php echo $totalAlta; ?></span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Jugadores</span>
                        <span class="valor-metrica"><?php echo $totalJugadores; ?></span>
                    </div>
                    <div class="tarjeta-metrica">
                        <span class="etiqueta-metrica">Partidas jugadas</span>
                        <span class="valor-metrica"><?php echo $totalPartidas; ?></span>
                    </div>
                </div>
            </article>

            <article>
                <h2>Gestion de datos del juego</h2>
                <p>
                    Desde este panel podes administrar las categorias y las palabras disponibles para las partidas.
                </p>

                <div class="acciones-formulario">
                    <a href="categorias.php" class="boton">Administrar Categorias</a>
                    <a href="palabras.php" class="boton boton-azul">Administrar Palabras</a>
                </div>
            </article>

            <article>
                <h3>Reglas de integridad aplicadas</h3>
                <ul>
                    <li><strong>Categorias obligatorias:</strong> para dar de alta una palabra, primero debe existir su categoria activa.</li>
                    <li><strong>Baja logica (A-15):</strong> una vez guardado un registro en la base de datos, las bajas siempre son logicas (se marca inactivo para preservar el historial de partidas). Antes de guardar podes cancelar o volver en cualquier momento.</li>
                    <li><strong>Restriccion de baja de categorias:</strong> solo se permite dar de baja una categoria si no tiene palabras activas asociadas.</li>
                    <li><strong>Formato de palabras (A-06 y A-07):</strong> solo letras de la A a la Z y Ñ, sin espacios ni tildes, con un minimo de 3 letras. La dificultad se calcula automaticamente por cantidad de letras.</li>
                </ul>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>
</body>
</html>
