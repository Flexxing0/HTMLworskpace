<?php

function creaTabla(object $registros)
{
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>ID</th>";
    echo "<th>Tipo</th>";
    echo "<th>Domicilio</th>";
    echo "<th>Cantidad Dormitorios</th>";
    echo "<th>Mejoras</th>";
    echo "<th>Baños</th>";
    echo "<th>Observacion</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    while ($registro = $registros->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>".$registro['idInmueble']."</td>";
        echo "<td>".$registro['tipoInmueble']."</td>";
        echo "<td>".$registro['domicilio']."</td>";
        echo "<td>".$registro['cantidadDormitorios']."</td>";
        echo "<td>".$registro['mejoras']."</td>";
        echo "<td>".$registro['cantidadBanios']."</td>";
        echo "<td>".$registro['observacion']."</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
}

?>