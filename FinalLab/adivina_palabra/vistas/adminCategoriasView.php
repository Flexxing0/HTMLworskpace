<?php
/* Archivo: vistas/adminCategoriasView.php
 * Vista del ABM de categorias para el administrador (RF-19, RF-21, A-15).
 * Envia acciones POST a controladores/categoriaController.php.
 */

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../dao/CategoriaDAO.php';

Sesion::exigirAdmin('../index.php', 'menuView.php');

$mensajeExito = (string) Sesion::obtenerFlash('cat_exito', '');
$mensajeError = (string) Sesion::obtenerFlash('cat_error', '');
$idEditar = (int) Sesion::obtenerFlash('cat_id_editar', 0);
$nombreForm = (string) Sesion::obtenerFlash('cat_nombre', '');

if ($idEditar === 0 && $nombreForm === '' && isset($_GET['editar']))
{
    $idSolicitado = (int) $_GET['editar'];
    $catEditar = CategoriaDAO::buscarActivaPorId($idSolicitado);
    if ($catEditar !== null)
    {
        $idEditar = $catEditar->getId();
        $nombreForm = $catEditar->getNombre();
    }
    else
    {
        $mensajeError = 'La categoria indicada para modificar no existe o esta dada de baja.';
    }
}

$categorias = CategoriaDAO::listarActivas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
    <title>Adivine la palabra - ABM de Categorias</title>
</head>
<body>
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Administracion - ABM de Categorias</p>
        </header>

        <nav class="barra-navegacion">
            <div class="enlaces-nav">
                <a href="adminIndexView.php">Inicio Admin</a>
                <a href="adminCategoriasView.php" class="activo">ABM Categorias</a>
                <a href="adminPalabrasView.php">ABM Palabras</a>
            </div>
            <div class="enlaces-nav">
                <span class="info-usuario-nav">
                    Administrador: @<?php echo htmlspecialchars(Sesion::getNombreUsuario(), ENT_QUOTES, 'UTF-8'); ?>
                </span>
                <a href="../controladores/logoutController.php" class="enlace-salir">Cerrar sesion</a>
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
            ?>

            <article>
                <h2><?php echo ($idEditar > 0) ? 'Modificar categoria' : 'Nueva categoria'; ?></h2>

                <div id="errorClienteCategoria" class="alerta alerta-error oculto"></div>

                <form id="frmCategoria" name="frmCategoria" method="post" action="../controladores/categoriaController.php" onSubmit="return validarCategoria();">
                    <fieldset>
                        <legend><?php echo ($idEditar > 0) ? 'Editar datos de la categoria #' . $idEditar : 'Datos de la nueva categoria'; ?></legend>

                        <input type="hidden" id="hdnIdCategoria" name="hdnIdCategoria" value="<?php echo $idEditar; ?>">

                        <div class="campo-formulario">
                            <label for="txtNombreCategoria">Nombre de la categoria</label>
                            <input type="text"
                                   id="txtNombreCategoria"
                                   name="txtNombreCategoria"
                                   maxlength="50"
                                   required
                                   autofocus
                                   placeholder="Ej.: Geografia, Ciencia, Cine"
                                   value="<?php echo htmlspecialchars($nombreForm, ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="texto-ayuda">
                                Entre 2 y 50 caracteres. Si ingresas una categoria previamente dada de baja, se reactivara automaticamente.
                            </span>
                        </div>

                        <div class="acciones-formulario">
                            <input type="submit"
                                   id="btnGuardarCategoria"
                                   name="btnGuardarCategoria"
                                   value="<?php echo ($idEditar > 0) ? 'Guardar cambios' : 'Guardar categoria'; ?>">
                            <?php
                            if ($idEditar > 0)
                            {
                                echo '<a href="adminCategoriasView.php" class="boton boton-secundario">Cancelar edicion</a>';
                            }
                            else
                            {
                                echo '<a href="adminIndexView.php" class="boton boton-secundario">Cancelar / Volver</a>';
                            }
                            ?>
                        </div>
                    </fieldset>
                </form>
            </article>

            <article>
                <h2>Categorias activas (<?php echo count($categorias); ?>)</h2>
                <p class="texto-ayuda">
                    Solo se permite dar de baja logica una categoria cuando tiene 0 palabras activas asociadas.
                </p>

                <?php
                if (count($categorias) === 0)
                {
                ?>
                    <div class="alerta alerta-aviso">
                        No hay categorias activas cargadas. Crea una categoria para poder dar de alta palabras.
                    </div>
                <?php
                }
                else
                {
                ?>
                    <div class="tabla-scroll-contenedor">
                        <table>
                            <caption>Listado de categorias activas</caption>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Palabras activas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                for ($i = 0; $i < count($categorias); $i++)
                                {
                                    $cat = $categorias[$i];
                                    $cantPalabras = $cat->getCantidadPalabrasActivas();
                                    $nombreEscapado = htmlspecialchars($cat->getNombre(), ENT_QUOTES, 'UTF-8');
                                    echo '<tr>';
                                    echo '<td>' . $cat->getId() . '</td>';
                                    echo '<td>' . $nombreEscapado . '</td>';
                                    echo '<td>' . $cantPalabras . '</td>';
                                    echo '<td>';
                                    echo '<div class="acciones-formulario">';
                                    echo '<a href="adminCategoriasView.php?editar=' . $cat->getId() . '" class="boton boton-azul boton-chico">Modificar</a>';
                                    echo '<form method="post" action="../controladores/categoriaController.php" data-nombre="' . $nombreEscapado . '" onSubmit="return confirmarBajaCategoria(this);">';
                                    echo '<input type="hidden" name="hdnIdBaja" value="' . $cat->getId() . '">';
                                    if ($cantPalabras > 0)
                                    {
                                        echo '<input type="submit" name="btnBajaCategoria" class="boton-peligro boton-chico" value="Dar de baja" disabled title="Tiene palabras activas asociadas">';
                                    }
                                    else
                                    {
                                        echo '<input type="submit" name="btnBajaCategoria" class="boton-peligro boton-chico" value="Dar de baja">';
                                    }
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
