<?php
 include 'ciudades.php';
class Paises
{
    private $id_pais;
    private $nombre_pais;
    
    private array $ciudades;

    public function __construct()
    {
        $this->ciudades=[];
    }

    public function getIdpais()
    {
        return $this->id_pais;
    }

    public function setIdpais($id)
    {
        $this->id_pais = $id;
    }

    public function getNombrepais()
    {
        return $this->nombre_pais;
    }

    public function setNombrepais($nombre_pais)
    {
        $this->nombre_pais = $nombre_pais;
    }

    public static function getPaisesBD()
    {
        $con = new mysqli('localhost', 'root', '', 'paises');
        $paises = [];
        $query = "SELECT * FROM paises ORDER BY id_pais";
        $resu = $con->query($query) or die("No se pudo realizar consulta de paises");
        while($registro = $resu->fetch_object())
        {
            $pais = new Paises();
            $pais->setIdpais($registro->id_pais);
            $pais->setNombrepais($registro->nombre_pais);
            $paises[] = $pais;
        }
        $resu->free();
        $con->close();
        return $paises;
    }

    public static function getPaisBD($idpais)
    {
        $con = new mysqli('localhost', 'root', '', 'paises');
        $query = "SELECT * FROM paises WHERE id_pais =".$idpais;
        $resu = $con->query($query);
        if($registro = $resu->fetch_object())
        {
            $pais = new Paises();
            $pais->setIdpais($registro->id_pais);
            $pais->setNombrepais($registro->nombre_pais);
        }
        $resu->free();
        $con->close();
        return $pais;
    }

    public static function getCiudadesBD($idpais)
    {
        
        $ciudades = [];
        $con = new mysqli('localhost', 'root', '', 'paises');
        $query = "SELECT id_ciudad,nombre_ciudad FROM ciudades as c JOIN paises as p ON p.id_pais=c.id_pais WHERE p.id_pais = ". $idpais;
        $resu = $con->query($query) or die("No se pudo realizar consulta de ciudades");
        while ($registro = $resu->fetch_object())
        {
            $ciudades[] = $registro->nombre_ciudad;
        }
        $resu->free();
        $con->close();
        return $ciudades;
    }

    public function getCiudades()
    {
        return $this->ciudades;
    }

}
?>