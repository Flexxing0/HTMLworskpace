<?php
class Compra
{

    private $id_compra;
    private $fecha_compra;
    private $cantidad;

    public function __construct()
    {
    }

    public function getidCompra()
    {
        return $this->id_compra;
    }
    public function setidCompra($id)
    {
        $this->id_compra = $id;
    }

    public function getFechaCompra()
    {
        return $this->fecha_compra;
    }
    public function setFechaCompra($fecha)
    {
        $this->fecha_compra = $fecha;
    }

    public function getCantidad()
    {
        return $this->cantidad;
    }
    public function setCantidad($can)
    {
        $this->cantidad = $can;
    }

    public static function getComprasBD($idcliente)
    {
        $con = new mysqli('localhost', 'root', '', 'clientes');
        $compras = [];
        $query = "SELECT co.id_compra,co.id_producto,co.fecha_compra,co.cantidad FROM compras as co JOIN clientes as cl ON cl.id_cliente=co.id_cliente WHERE cl.id_cliente=".$idcliente;
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object())
        {
            $compra = new Compra();
            $compra->setidCompra($regi->id_compra);
            $compra->setFechaCompra($regi->fecha_compra);
            $compra->setCantidad($regi->cantidad);
            $compras[] = $compra;
        }
        $resu->free();
        $con->close();
        return $compras;
    }

}
?>