<?php
/* Archivo: admin/palabras.php
 * ABM de palabras para el administrador (RF-20, RF-21, A-06, A-07, A-14, A-15).
 * Permite dar de alta (exigiendo categoria activa existente), filtrar, modificar
 * y dar de baja logica palabras. Pagina autoprocesada.
 */

require_once __DIR__ . '/../clases/Sesion.class.php';
require_once __DIR__ . '/../clases/Categoria.class.php';
require_once __DIR__ . '/../clases/Palabra.class.php';

// Acceso exclusivo para el administrador (RF-21)
Sesion::exigirAdmin('../index.php', '../menu.php');

$mensajeExito = '';
$mensajeError = '';

$idEditar = 0;
$palabraForm = '';
$categoriaForm = 0;

// Filtros del listado (GET)
$filtroCategoria = isset($_GET['cmbFiltroCategoria']) ? (int) $_GET['cmbFiltroCategoria'] : 0;
$filtroDificultad = isset($_GET['cmbFiltroDificultad']) ? trim($_GET['cmbFiltroDificultad']) : '';
if (!in_array($filtroDificultad, array('', 'baja', 'media', 'alta'), true))
{
    $filtroDificultad = '';
}

// Procesamiento de acciones POST (alta, modificacion y baja logica)
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (isset($_POST['btnGuardarPalabra']))
    {
        $idForm = isset($_POST['hdnIdPalabra']) ? (int) $_POST['hdnIdPalabra'] : 0;
        $palabraIngresada = isset($_POST['txtPalabra']) ? trim($_POST['txtPalabra']) : '';
        $categoriaIngresada = isset($_POST['cmbCategoriaPalabra']) ? (int) $_POST['cmbCategoriaPalabra'] : 0;

        if ($idForm > 0)
        {
            $resultado = Palabra::modificarBD($idForm, $palabraIngresada, $categoriaIngresada);
            if ($resultado['ok'])
            {
                $mensajeExito = $resultado['mensaje'];
                $idEditar = 0;
                $palabraForm = '';
                $categoriaForm = 0;
            }
            else
            {
                $mensajeError = $resultado['mensaje'];
                $idEditar = $idForm;
                $palabraForm = $palabraIngresada;
                $categoriaForm = $categoriaIngresada;
            }
        }
        else
        {
            $resultado = Palabra::altaBD($palabraIngresada, $categoriaIngresada);
            if ($resultado['ok'])
            {
                $mensajeExito = $resultado['mensaje'];
                $idEditar = 0;
                $palabraForm = '';
                $categoriaForm = 0;
            }
            else
            {
                $mensajeError = $resultado['mensaje'];
                $idEditar = 0;
                $palabraForm = $palabraIngresada;
                $categoriaForm = $categoriaIngresada;
            }
        }
    }
    else if (isset($_POST['btnBajaPalabra']))
    {
        $idBaja = isset($_POST['hdnIdBaja']) ? (int) $_POST['hdnIdBaja'] : 0;
        $resultado = Palabra::bajaLogicaBD($idBaja);
        if ($resultado['ok'])
        {
            $mensajeExito = $resultado['mensaje'];
        }
        else
        {
            $mensajeError = $resultado['mensaje'];
        }
    }
}
else if (isset($_GET['editar']))
{
    $idSolicitado = (int) $_GET['editar'];
    $palEditar = Palabra::buscarActivaPorIdBD($idSolicitado);
    if ($palEditar !== null)
    {
        $idEditar = $palEditar->getId();
        $palabraForm = $palEditar->getPalabra();
        $categoriaForm = $palEditar->getIdCategoria();
    }
    else
    {
        $mensajeError = 'La palabra indicada para modificar no existe o esta dada de baja.';
    }
}

$categoriasActivas = Categoria::listarActivasBD();
$hayCategorias = (count($categoriasActivas) > 0);
$palabras = Palabra::listarActivasBD(
    ($filtroCategoria > 0) ? $filtroCategoria : null,
    ($filtroDificultad !== '') ? $filtroDificultad : null
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Adivine la palabra - ABM de Palabras</title>
</head>
<body onLoad="actualizarVistaDificultadPalabra();">
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Administracion - ABM de Palabras</p>
        </header>

        <nav class="barra-navegacion">
            <div class="enlaces-nav">
                <a href="index.php">Inicio Admin</a>
                <a href="categorias.php">ABM Categorias</a>
                <a href="palabras.php" class="activo">ABM Palabras</a>
            </div>
            <div class="enlaces-nav">
                <span class="info-usuario-nav">
                    Administrador: @<?php echo htmlspecialchars(Sesion::getNombreUsuario(), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <a href="../logout.php" class="enlace-salir">Cerrar sesion</a>
            </div>
        </nav>

        <section>
            <?php
            if ($mensajeExito !== '')
            {
                echo '<div class="alerta alerta-exito">' . htmlspecialchars($mensajeExito, ENT_QUOTES, 'UTF-8') . '</div>';
            }
            if ($mensajeError !== '')
            {
                echo '<div class="alerta alerta-error">' . htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') . '</div>';
            }
            if (!$hayCategorias)
            {
                echo '<div class="alerta alerta-aviso">Para dar de alta una palabra primero debe existir al menos una categoria activa. <a href="categorias.php">Ir a ABM de Categorias</a>.</div>';
            }
            ?>

            <article>
                <h2><?php echo ($idEditar > 0) ? 'Modificar palabra' : 'Nueva palabra'; ?></h2>

                <div id="errorClientePalabra" class="alerta alerta-error oculto"></div>

                <form id="frmPalabra" name="frmPalabra" method="post" action="palabras.php" onSubmit="return validarPalabra();">
                    <fieldset <?php echo (!$hayCategorias) ? 'disabled' : ''; ?>>
                        <legend><?php echo ($idEditar > 0) ? 'Editar datos de la palabra #' . $idEditar : 'Datos de la nueva palabra'; ?></legend>

                        <input type="hidden" id="hdnIdPalabra" name="hdnIdPalabra" value="<?php echo $idEditar; ?>">

                        <div class="campo-formulario">
                            <label for="cmbCategoriaPalabra">Categoria</label>
                            <select id="cmbCategoriaPalabra" name="cmbCategoriaPalabra" required>
                                <option value="0">-- Selecciona una categoria --</option>
                                <?php
                                for ($i = 0; $i < count($categoriasActivas); $i++)
                                {
                                    $cat = $categoriasActivas[$i];
                                    $selected = ($categoriaForm === $cat->getId()) ? ' selected' : '';
                                    echo '<option value="' . $cat->getId() . '"' . $selected . '>'
                                        . htmlspecialchars($cat->getNombre(), ENT_QUOTES, 'UTF-8')
                                        . '</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="campo-formulario">
                            <label for="txtPalabra">Palabra</label>
                            <input type="text"
                                   id="txtPalabra"
                                   name="txtPalabra"
                                   maxlength="50"
                                   required
                                   autofocus
                                   placeholder="Ej.: gaviota, montaña, algoritmo"
                                   onInput="actualizarVistaDificultadPalabra();"
                                   value="<?php echo htmlspecialchars($palabraForm, ENT_QUOTES, 'UTF-8'); ?>">
                            <span id="vistaDificultadPalabra" class="texto-ayuda">
                                Solo letras A-Z y Ñ (sin espacios ni tildes), entre 3 y 50 letras. Se guarda en minuscula.
                            </span>
                        </div>

                        <div class="acciones-formulario">
                            <input type="submit"
                                   id="btnGuardarPalabra"
                                   name="btnGuardarPalabra"
                                   value="<?php echo ($idEditar > 0) ? 'Guardar cambios' : 'Guardar palabra'; ?>">
                            <?php
                            if ($idEditar > 0)
                            {
                                echo '<a href="palabras.php" class="boton boton-secundario">Cancelar edicion</a>';
                            }
                            else
                            {
                                echo '<a href="index.php" class="boton boton-secundario">Cancelar / Volver</a>';
                            }
                            ?>
                        </div>
                    </fieldset>
                </form>
            </article>

            <article>
                <h2>Palabras activas (<?php echo count($palabras); ?>)</h2>

                <form id="frmFiltroPalabras" name="frmFiltroPalabras" method="get" action="palabras.php">
                    <fieldset>
                        <legend>Filtrar listado</legend>
                        <div class="grilla-info-partida">
                            <div class="campo-formulario">
                                <label for="cmbFiltroCategoria">Categoria</label>
                                <select id="cmbFiltroCategoria" name="cmbFiltroCategoria">
                                    <option value="0">Todas las categorias</option>
                                    <?php
                                    for ($i = 0; $i < count($categoriasActivas); $i++)
                                    {
                                        $cat = $categoriasActivas[$i];
                                        $selected = ($filtroCategoria === $cat->getId()) ? ' selected' : '';
                                        echo '<option value="' . $cat->getId() . '"' . $selected . '>'
                                            . htmlspecialchars($cat->getNombre(), ENT_QUOTES, 'UTF-8')
                                            . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="campo-formulario">
                                <label for="cmbFiltroDificultad">Dificultad</label>
                                <select id="cmbFiltroDificultad" name="cmbFiltroDificultad">
                                    <option value="" <?php echo ($filtroDificultad === '') ? 'selected' : ''; ?>>Todas</option>
                                    <option value="baja" <?php echo ($filtroDificultad === 'baja') ? 'selected' : ''; ?>>Baja (3 a 5 letras)</option>
                                    <option value="media" <?php echo ($filtroDificultad === 'media') ? 'selected' : ''; ?>>Media (6 a 8 letras)</option>
                                    <option value="alta" <?php echo ($filtroDificultad === 'alta') ? 'selected' : ''; ?>>Alta (9 o mas letras)</option>
                                </select>
                            </div>
                        </div>

                        <div class="acciones-formulario">
                            <input type="submit" id="btnFiltrar" value="Aplicar filtros">
                            <a href="palabras.php" class="boton boton-secundario">Limpiar filtros</a>
                        </div>
                    </fieldset>
                </form>

                <?php
                if (count($palabras) === 0)
                {
                ?>
                    <div class="alerta alerta-info">
                        No se encontraron palabras activas con los filtros seleccionados.
                    </div>
                <?php
                }
                else
                {
                ?>
                    <div class="tabla-scroll-contenedor">
                        <table>
                            <caption>Listado de palabras activas</caption>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Palabra</th>
                                    <th>Categoria</th>
                                    <th>Letras</th>
                                    <th>Dificultad</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($palabras); $i++)
                                {
                                    $pal = $palabras[$i];
                                    $textoPalabra = htmlspecialchars($pal->getPalabra(), ENT_QUOTES, 'UTF-8');
                                    $textoCategoria = htmlspecialchars($pal->getNombreCategoria(), ENT_QUOTES, 'UTF-8');
                                    $textoDif = ucfirst($pal->getDificultad());

                                    echo '<tr>';
                                    echo '<td>' . $pal->getId() . '</td>';
                                    echo '<td><strong>' . $textoPalabra . '</strong></td>';
                                    echo '<td>' . $textoCategoria . '</td>';
                                    echo '<td>' . $pal->getLongitud() . '</td>';
                                    echo '<td>' . htmlspecialchars($textoDif, ENT_QUOTES, 'UTF-8') . '</td>';
                                    echo '<td>';
                                    echo '<div class="acciones-formulario">';
                                    echo '<a href="palabras.php?editar=' . $pal->getId() . '" class="boton boton-azul boton-chico">Modificar</a>';
                                    echo '<form method="post" action="palabras.php" data-palabra="' . $textoPalabra . '" onSubmit="return confirmarBajaPalabra(this);">';
                                    echo '<input type="hidden" name="hdnIdBaja" value="' . $pal->getId() . '">';
                                    echo '<input type="submit" name="btnBajaPalabra" class="boton-peligro boton-chico" value="Dar de baja">';
                                    echo '</form>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php
                }
                ?>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>

    <script src="../js/utilidades.js"></script>
    <script src="../js/admin.js"></script>
</body>
</html>
