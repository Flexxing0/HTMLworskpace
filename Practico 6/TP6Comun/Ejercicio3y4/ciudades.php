<?php
class Ciudades
{
    private $id_ciudad;
    private $nombre_ciudad;

    public function __construct()
    {
    }

    public function getIdciudad()
    {
        return $this->id_ciudad;
    }

    public function setIdciudad($id)
    {
        $this->id_ciudad = $id;
    }

    public function getNombreciudad()
    {
        return $this->nombre_ciudad;
    }

    public function setNombreciudad($nombre_ciudad)
    {
        $this->nombre_ciudad = $nombre_ciudad;
    }

    public static function getciudadesBD($idpais)
    {
        $con = new mysqli('localhost', 'root', '', 'productos');
        $ciudades = [];
        $query = "SELECT id_ciudad, nombre_ciudad FROM ciudades WHERE id_pais = ".$idpais."ORDER BY id_ciudad";
        $resu = $con->query($query) or die("No se pudo realizar consulta de ciudades");
        while($registro = $resu->fetch_object())
        {
            $ciudad = new Ciudades();
            $ciudad->setIdciudad($registro->id_ciudad);
            $ciudad->setNombreciudad($registro->nombre_ciudad);
            $ciudades[] = $ciudad;
        }
        $resu->free();
        $con->close();
        return $ciudades;
    }

    public static function getciudadBD($idciudad)
    {
        $con = new mysqli('localhost', 'root', '', 'productos');
        $query = "SELECT * FROM ciudades WHERE id_ciudad =".$idciudad;
        $resu = $con->query($query);
        if($registro = $resu->fetch_object())
        {
            $ciudad = new Ciudades();
            $ciudad->setIdciudad($registro->id_ciudad);
            $ciudad->setNombreciudad($registro->nombre_ciudad);
        }
        $resu->free();
        $con->close();
        return $ciudad;
    }

    public function getTarifa()
    {
        $con = new mysqli('localhost', 'root', '', 'productos');
        $query = "SELECT costo_envio FROM tarifas_envio WHERE id_ciudad =". $this->getIdciudad();
        $resu = $con->query($query);
        if($registro = $resu->fetch_object())
        {
            $tarifa = $registro->costo_envio;
        }
        $resu->free();
        $con->close();
        return $tarifa;
    }
}

?>