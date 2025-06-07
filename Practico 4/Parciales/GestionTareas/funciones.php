<?php
require_once 'usuario.php';

function mensajeInicioSesion()
{
    echo "<p>Inicio de sesion exitoso!</p><br>";
    echo "<a href='pagTareas.php'>Ir a gestor tareas</a>";
}
function formularioRegistro()
{ ?>
    <form name="formRegistro" action="inicioSesion.php" method='POST'>
        <center>
            <label for="idnombre">Nombre: </label>
            <input type="text" id="idnombre" name="nombre" required>

            <input type="submit" value="Ingresar">
        </center>
    </form>
    <?php
}
function formIngresarTareas()
{ ?>
    <form name="formTareas" action="anadeTareas.php" method='POST'>
        <center>
            <p>Tarea</p>
            <label for="iddescripcion">Ingrese una descripcion: </label>
            <input type="text" id="iddescripcion" name="descripcion">

            <input type="submit" value="Agregar tarea">
        </center>
    </form>
    <?php
}
function muestraTablaTareas(Usuario $usuario)
{
    echo "<form name='tablaTareas' action='finalizarTareas.php' method='POST'>";
    echo "<center>";
    tablaPendientes($usuario->getPendientes());
    echo "<input type='submit' formaction='finalizarTareas.php' value='Finalizar Tareas'><br>";
    tablaFinalizadas($usuario->getFinalizadas());
    echo "<input type='submit' formaction='eliminarTareas.php' value='Eliminar Tareas'><br>";
    echo "</center>";
    echo "</form>";
    echo "<a href='index.php'>Volver a inicio</a>";
}
function tablaPendientes(array $pendientes)
{
    echo "<br><table>";
    echo "<caption>Tareas pendientes</caption>";
    echo "<thead><tr><th></th><th>Descripcion</th></tr></thead>";
    echo "<tbody>";
    if (count($pendientes) > 0) {
        for ($i = 0; $i < count($pendientes); $i++) {
            echo '<tr>';
            echo '<td> <input type="checkbox" id="idP' . (string) $i . '" name="pendiente[]" value="'.$i.'"> </td>';
            echo '<td> <label for="idP' . (string) $i . '"> ' . $pendientes[$i] . '</label> </td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td><p>Vacio</p></td></tr>";
    }
    echo "</tbody>";
    echo "</table><br>";
}
function tablaFinalizadas(array $finalizadas)
{
    echo "<br><table>";
    echo "<caption>Tareas finalizadas</caption>";
    echo "<thead><tr><th>Descripcion</th></tr></thead>";
    echo "<tbody>";
    if (count($finalizadas) > 0) {
        for ($i = 0; $i < count($finalizadas); $i++) {
            echo '<tr>';
            echo '<td>' . $finalizadas[$i] . '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td><p>Vacio</p></td></tr>";
    }
    echo "</tbody>";
    echo "</table><br>";
}
?>