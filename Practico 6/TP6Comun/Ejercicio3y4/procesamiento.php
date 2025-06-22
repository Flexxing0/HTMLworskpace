<?php
include 'ciudades.php';
include 'paises.php';
include 'productos.php';

if (isset($_POST['idproducto'])){
    $producto = productos::getProductoBD($_POST['idproducto']);
    if (is_null($producto)){

    } else{
        $obj = new stdClass();
        $obj->nombre_producto = $producto->getNombreProdu();
        $obj->precio = $producto->getPrecioProdu();        
        $obj->stock = $producto->getStockProdu();        
        $json = json_encode($obj);
    }
    echo $json;
}
if (isset($_POST['idcantidad']) && isset($_POST['idproducto'])){
    $producto = productos::getProductoBD($_POST['idproducto']);
    $cantidad = (int)$_POST['idcantidad'];
    if (is_null($producto)){

    } else{
        $obj = new stdClass();
        $obj->total = $producto->getTotal($cantidad);
    }
    echo $json;
}
if (isset($_POST['idpais'])){
    $pais = paises::getPaisBD($_POST['idpais']);
    if (is_null($pais)){

    }else {
        $obj = new stdClass();
        $obj->ciudades = ciudades::getciudadesBD($pais->getIdpais());
        $json = json_encode($obj);
    }
    echo $json;
}
if (isset($_POST['idciudad']) && isset($_POST['idpais'])){
    $ciudad = ciudades::getciudadBD($_POST['idciudad']);
    if (is_null($ciudad)){

    } else{
        $obj = new stdClass();
        $obj->importe = $ciudad->getTarifa();
        $json = json_encode($obj);
    }
    echo $json;
}
?>