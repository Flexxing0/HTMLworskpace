<?php
class Producto
{
    private $id_producto;
    private $nombre_producto;
    private $precio;

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

    public static function getProductosBD($idcompra)
    {
        $con = new mysqli('localhost', 'root', '', 'clientes');
        $producto = null;
        $query = "SELECT p.id_producto, p.nombre_producto, p.precio FROM productos as p JOIN compras as c ON p.id_producto=c.id_producto WHERE c.id_compra=".$idcompra;
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object())
        {
            $producto = new Producto();
            $producto->setIdproducto($regi->id_producto);
            $producto->setNombreProdu($regi->nombre_producto);
            $producto->setPrecioProdu($regi->precio);
        }
        $resu->free();
        $con->close();
        return $producto;
    }

    public function getTotal($cantidad)
    {
        return $this->precio * $cantidad;
    }
}
?>