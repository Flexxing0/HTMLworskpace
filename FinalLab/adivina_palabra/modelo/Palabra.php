<?php

class Palabra
{
    private $id;
    private $palabra;
    private $longitud;
    private $idCategoria;
    private $nombreCategoria;
    private $activa;

    public function __construct()
    {
        $this->id = 0;
        $this->palabra = '';
        $this->longitud = 0;
        $this->idCategoria = 0;
        $this->nombreCategoria = '';
        $this->activa = 1;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function getPalabra()
    {
        return $this->palabra;
    }

    public function setPalabra($palabra)
    {
        $this->palabra = $palabra;
    }

    public function getLongitud()
    {
        return $this->longitud;
    }

    public function setLongitud($longitud)
    {
        $this->longitud = (int) $longitud;
    }

    public function getIdCategoria()
    {
        return $this->idCategoria;
    }

    public function setIdCategoria($idCategoria)
    {
        $this->idCategoria = (int) $idCategoria;
    }

    public function getNombreCategoria()
    {
        return $this->nombreCategoria;
    }

    public function setNombreCategoria($nombreCategoria)
    {
        $this->nombreCategoria = $nombreCategoria;
    }

    public function getActiva()
    {
        return $this->activa;
    }

    public function setActiva($activa)
    {
        $this->activa = (int) $activa;
    }

    public function getDificultad()
    {
        return self::calcularDificultad($this->longitud);
    }

    public static function normalizar($texto)
    {
        return mb_strtolower(trim($texto), 'UTF-8');
    }

    public static function validarFormato($palabraRaw)
    {
        $palabra = self::normalizar($palabraRaw);
        if ($palabra === '')
        {
            return array('ok' => false, 'mensaje' => 'La palabra es obligatoria.');
        }

        if (!preg_match('/^[a-zñ]+$/u', $palabra))
        {
            return array('ok' => false, 'mensaje' => 'La palabra solo puede contener letras de la A a la Z y Ñ, sin espacios ni tildes.');
        }

        $longitud = mb_strlen($palabra, 'UTF-8');
        if ($longitud < 3)
        {
            return array('ok' => false, 'mensaje' => 'La palabra debe tener al menos 3 letras.');
        }

        if ($longitud > 50)
        {
            return array('ok' => false, 'mensaje' => 'La palabra no puede superar las 50 letras.');
        }

        return array('ok' => true, 'valor' => $palabra, 'longitud' => $longitud);
    }

    public static function calcularDificultad($longitud)
    {
        $len = (int) $longitud;
        if ($len <= 5)
        {
            return 'baja';
        }
        else if ($len <= 8)
        {
            return 'media';
        }
        else
        {
            return 'alta';
        }
    }

    public static function getRangoLongitudPorNivel($nivel)
    {
        switch ($nivel)
        {
            case 'baja':
                return array(3, 5);
            case 'media':
                return array(6, 8);
            case 'alta':
                return array(9, 50);
            default:
                return null;
        }
    }
}
?>
