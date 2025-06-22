<?php
class Paises
{
    private $id_pais;
    private $nombre_pais;

    public function __construct()
    {
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
        $con = new mysqli('localhost', 'root', '', 'productos');
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
        $con = new mysqli('localhost', 'root', '', 'productos');
        $pais = NULL;
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
}
?>