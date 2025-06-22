<?php
include 'cliente.php';
include 'compra.php';
include 'producto.php';
if (isset($_POST['documento'])){
    $cliente = cliente::getClienteBD($_POST['documento']);
    if(is_null($cliente))
    {

    }else{
        $compras = compra::getComprasBD($cliente->getID());
        $comprasCl = [];
        $objTemp = new stdClass();
        $objTemp->cliente = $cliente->toArray();
        $objTemp->compras = [];
        for($i=0;$i<count($compras);$i++)
        {
            $producto = producto::getProductosBD($compras[$i]->getidCompra());
            $cantidad = $compras[$i]->getCantidad();
            $compra = new stdClass();
            $compra->id_compra = $compras[$i]->getidCompra();
            $compra->producto = $producto->getNombreProdu();
            $compra->cantidad = $cantidad;
            $compra->total = $producto->getTotal($cantidad);
            $compra->fecha = $compras[$i]->getFechaCompra();
            $comprasCl[] = $compra;
            $objTemp->compras[] = $compra;
        }
    $json = json_encode($objTemp);
    }
    
    echo $json;
}

?>