<?php

function formularioIngreso()
{
    ?>
    <form name="formularioIngreso" action="index.php" method="POST">

        <label for="tipos">Seleccion Inmueble</label>
        <select name="tipoInmueble" id="tipos" required>
            <option value="Casa" selected>Casa</option>
            <option value="Departamento">Departamento</option>
            <option value="Terreno">Terreno</option>
            <option value="Quinta">Quinta</option>
        </select><br>

        <label for="iddomicilio">Domicilio:</label>
        <input type="text" name="domicilio" id="iddomicilio" required><br>

        <label for="iddormitorio">Cantidad de Dormitorios</label>
        <input type="number" name="dormitorio" id="iddormitorio" min="1" max="100" required><br>

        <label for="mejora">Seleccione una mejora</label>
        <select name="mejoras" id="mejora" required>
            <option value="Jardin" selected>Jardin</option>
            <option value="Piscina">Piscina</option>
            <option value="Garage">Garage</option>
            <option value="Cercado">Cercado</option>
            <option value="Sin cerco">Sin Cerco</option>
        </select><br>

        <label for="idb">Cantidad de banios:</label>
        <input type="number" name="banios" id="idb" min="1" max="10" required><br>

        <label for="idobs">Observaciones</label>
        <textarea id="idobs" name="obs" maxlength="200" placeholder="Escribe alguna observacion..."></textarea>

        <input type="submit" value="cargar">
    </form>
    <?php
}

function cargaDatos($tipo,$domicilio,$cantDorm,$mejoras,$cantBanios,$obs)
{
    require 'conexion.php';
    $query = "INSERT INTO inmueble (tipoInmueble, domicilio, cantidadDormitorios, mejoras, cantidadBanios, observacion) VALUES ";
    $query.= "('".(string)$tipo."', '".(string)$domicilio."',".$cantDorm.",'".(string)$mejoras."',".$cantBanios.",'".$obs."')";
    $connection->begin_transaction();
    $resu = $connection->query($query);
    if ($resu)
    {
        echo "<p>La consulta se realizo con exito</p>";
        
        $connection->commit();
    } else {
        echo "<p>La consulta no se realizo</p>";
        $connection->rollback();
    }
    
    $connection->close();

}

function muestraTablas(){
    require 'conexion.php';
    $query = "SELECT * FROM inmueble";
    $resu = $connection->query($query);
    if ($resu->num_rows>0)
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
    while ($registro = $resu->fetch_assoc())
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
    $resu->free();
    echo "</tbody>";
    echo "</table>";
    }
    
    $connection->close();
}

function botonMostrarTablas(){

}



?>