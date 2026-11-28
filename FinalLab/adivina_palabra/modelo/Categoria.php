<?php
/* Archivo: modelo/Categoria.php
 * Entidad Categoria y validacion de formato del modelo.
 * Las operaciones de base de datos se ubican en dao/CategoriaDAO.php.
 */

class Categoria
{
    private $id;
    private $nombre;
    private $activa;
    private $cantidadPalabrasActivas;

    public function __construct()
    {
        $this->id = 0;
        $this->nombre = '';
        $this->activa = 1;
        $this->cantidadPalabrasActivas = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    }

    public function getActiva()
    {
        return $this->activa;
    }

    public function setActiva($activa)
    {
        $this->activa = (int) $activa;
    }

    public function getCantidadPalabrasActivas()
    {
        return $this->cantidadPalabrasActivas;
    }

    public function setCantidadPalabrasActivas($cantidad)
    {
        $this->cantidadPalabrasActivas = (int) $cantidad;
    }

    // Valida el formato del nombre de categoria en el servidor
    public static function validarNombre($nombreRaw)
    {
        $nombre = trim($nombreRaw);
        if ($nombre === '')
        {
            return array('ok' => false, 'mensaje' => 'El nombre de la categoria es obligatorio.');
        }

        $largo = mb_strlen($nombre, 'UTF-8');
        if ($largo < 2 || $largo > 50)
        {
            return array('ok' => false, 'mensaje' => 'El nombre de la categoria debe tener entre 2 y 50 caracteres.');
        }

        return array('ok' => true, 'valor' => $nombre);
    }
}
?>
