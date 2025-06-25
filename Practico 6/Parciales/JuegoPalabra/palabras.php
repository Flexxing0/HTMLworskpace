<?php
class Palabras
{
    private $idPalabra;
    private $palabra;
    private $dificultadPalabra;
    private $acertada;

    public function __construct(){}

    public function getIdPalabra()
    {
        return $this->idPalabra;
    }
    public function getPalabra()
    {
        return $this->palabra;
    }
    public function getDificultadPalabra()
    {
        return $this->dificultadPalabra;
    }
    public function getAcertada()
    {
        return $this->acertada;
    }
    public function setIdPalabra($idPalabra)
    {
        $this->idPalabra = $idPalabra;
    }
    public function setPalabra($palabra)
    {
        $this->palabra = $palabra;
    }
    public function setDificultadPalabra($dificultadPalabra)
    {
        $this->dificultadPalabra = $dificultadPalabra;
    }

    public function setAcertada($acertada)
    {
        $this->acertada = $acertada;
    }

    public static function getPalabrasBD()
    {
        $con = new mysqli('localhost', 'root', '', 'juego_palabras_2024');
        $palabras = [];
        $query = "SELECT * FROM palabras";
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object()) {
            $palabra = new Palabras();
            $palabra->setIdPalabra($regi->idPalabra);
            $palabra->setPalabra($regi->palabra);
            $palabra->setDificultadPalabra($regi->dificultadPalabra);
            $palabra->setAcertada($regi->acertada);
            $palabras[] = $palabra;
        }
        $resu->free();
        $con->close();
        return $palabras;
    }

    public static function acertadaBD($idPalabra)
    {
        $con = new mysqli('localhost', 'root', '', 'juego_palabras_2024');
        $query = "UPDATE palabras SET acertada = acertada + 1 WHERE idPalabra = ".$idPalabra;
        $resu = $con->query($query);
        if ($resu->num_rows)
        {

        }

    }


}
?>