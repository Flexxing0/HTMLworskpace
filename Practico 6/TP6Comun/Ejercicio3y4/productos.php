<?php
class Productos
{
    private $id_producto;
    private $nombre_producto;
    private $precio;
    private $stock;

    public function __construct(){}

    public function getIdproducto()
    {
        return $this->id_producto;
    }
    public function setIdproducto($id)
    {
        $this->id_producto = $id;
    }

    public function getNombreProdu()
    {
        return $this->nombre_producto;
    }
    public function setNombreProdu($nombre)
    {
        $this->nombre_producto = $nombre;
    }
    
    public function getPrecioProdu()
    {
        return $this->precio;
    }
    public function setPrecioProdu($pre)
    {
        $this->precio = $pre;
    }
    public function getStockProdu()
    {
        return $this->stock;
    }
    public function setStockProdu($s)
    {
        $this->stock = $s;
    }
    public static function getProductoBD($idproducto)
    {
        $con = new mysqli('localhost', 'root', '', 'productos');
        $producto = null;
        $query = "SELECT id_producto, nombre_producto, precio, stock FROM productos WHERE id_producto=".$idproducto;
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object())
        {
            $producto = new Productos();
            $producto->setIdproducto($regi->id_producto);
            $producto->setNombreProdu($regi->nombre_producto);
            $producto->setPrecioProdu($regi->precio);
            $producto->setStockProdu($regi->stock);
        }
        $resu->free();
        $con->close();
        return $producto;
    }

    public static function getProductosBD()
    {
        $con = new mysqli('localhost', 'root', '', 'productos');
        $productos = [];
        $query = "SELECT id_producto, nombre_producto, precio, stock FROM productos";
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object())
        {
            $producto = new Productos();
            $producto->setIdproducto($regi->id_producto);
            $producto->setNombreProdu($regi->nombre_producto);
            $producto->setPrecioProdu($regi->precio);
            $producto->setStockProdu($regi->stock);
            $productos[] = $producto;
        }
        $resu->free();
        $con->close();
        return $productos;        
    }

    public function getTotal($cantidad)
    {
        return $this->precio * $cantidad;
    }

}
?>