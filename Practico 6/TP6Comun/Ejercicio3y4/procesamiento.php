<?php
include 'ciudades.php';
include 'paises.php';
include 'productos.php';
$json = null;
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'importe':
            if (isset($_POST['idcantidad']) && isset($_POST['idproducto'])) {
                $producto = productos::getProductoBD($_POST['idproducto']);
                $cantidad = (int) $_POST['idcantidad'];
                if (is_null($producto)) {

                } else {
                    $obj = new stdClass();
                    $obj->total = $producto->getTotal($cantidad);
                    $json = json_encode($obj);
                }
            }
            break;
        case 'producto':
            if (isset($_POST['idproducto'])) {
                $producto = productos::getProductoBD($_POST['idproducto']);
                if (is_null($producto)) {

                } else {
                    $obj = new stdClass();
                    $obj->nombre_producto = $producto->getNombreProdu();
                    $obj->precio = $producto->getPrecioProdu();
                    $obj->stock = $producto->getStockProdu();
                    $json = json_encode($obj);
                }
            }
            break;
        case 'tarifa':
            if (isset($_POST['idciudad']) && isset($_POST['idpais'])) {
                $ciudad = ciudades::getciudadBD($_POST['idciudad']);
                if (is_null($ciudad)) {

                } else {
                    $obj = new stdClass();
                    $obj->importe = $ciudad->getTarifa();
                }
                $json = json_encode($obj);
            }
            break;
        case 'ciudad':
            if (isset($_POST['idpais'])) {
                $pais = paises::getPaisBD($_POST['idpais']);
                if (is_null($pais)) {

                } else {
                    $obj = new stdClass();
                    $obj->ciudades = [];
                    $ciudades = ciudades::getciudadesBD($pais->getIdpais());
                    for ($i = 0; $i < count($ciudades); $i++) {
                        $ciudad = new stdClass();
                        $ciudad->id_ciudad = $ciudades[$i]->getIdciudad();
                        $ciudad->nombre_ciudad = $ciudades[$i]->getNombreciudad();
                        $obj->ciudades[] = $ciudad;
                    }
                    $json = json_encode($obj);
                }
            }
            break;
        default:
            break;
    }
}
echo $json;

?>