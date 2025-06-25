<?php
class Pistas
{
    private $idpista;
    private $ordenPista;
    private $pista;

    public function __construct(){}

    public function getIdpista()
    {
        return $this->idpista;
    }

    public function setIdpista($idpista)
    {
        $this->idpista = $idpista;
    }

    public function getOrdenPista()
    {
        return $this->ordenPista;
    }

    public function setOrdenPista($ordenPista)
    {
        $this->ordenPista = $ordenPista;
    }

    public function getPista()
    {
        return $this->pista;
    }

    public function setPista($pista)
    {
        $this->pista = $pista;
    }

    public static function getPistasPalabra($idPalabra)
    {
        $con = new mysqli('localhost', 'root', '', 'juego_palabras_2024');
        $pistas = [];
        $query = "SELECT * FROM pistas WHERE idPalabra = ".$idPalabra;
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object()) {
            $pista = new Pistas();
            $pista->setIdpista($regi->idpista);
            $pista->setOrdenPista($regi->ordenPista);
            $pista->setPista($regi->pista);
            $pistas[] = $pista;
        }
        $resu->free();
        $con->close();
        return $pistas;
    }
}
?>