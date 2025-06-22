<?php

function listadoProductos()
{
   ?>
    <div id="listadoProductos"></div>
    <div id="cantidadComprar"></div>
    <div id="datosEnvio"></div>
   <?php
}

function selectPaises()
{
    include 'paises.php';
    $paises = paises::getPaisesBD();
    echo "<select id='idselect-paises' name='select-paises' required onChange='muestraCiudades();'>";
    echo "<option value=''>--Selecciona un pais--</option>";
    if (!empty($paises)){
        foreach($paises as $pais){
            $id = $pais->getIdpais();
            $nombre = $pais->getNombrepais();
            echo "<option value='".$id."'>".$nombre."</option>";
        }
    }
    echo "</select><br>";
    echo "<select id='idselect-ciudades' name='select-ciudades' onChange='muestraImporte();'>" ;
    echo "<option value=''>--Selecciona una ciudad--</option>";
    echo"</select>";

}

function selectProductos()
{
    include 'productos.php';
    $productos = productos::getProductosBD();
    echo "<select id='idselect-productos' name='select-productos' required onChange='muestraProducto();'>";
    echo "<option value=''>--Selecciona un producto--</option>";
    if (!empty($productos)){
        foreach($productos as $producto){
            $id = $producto->getIdproducto();
            $nombre = $producto->getNombreProdu();
            echo "<option value='".$id."'>".$nombre."</option>";
        }
    }
    echo "</select><br>";
}


?>