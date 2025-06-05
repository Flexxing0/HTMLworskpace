<?php
require_once 'paciente.php';

class Usuario
{

    private string $dni;
    private string $nombre;
    private string $clave;
    private array $integrantes;

    function __construct(string $dni, string $nombre, string $clave)
    {
        $this->dni = $dni;
        $this->nombre = $nombre;
        $this->clave = $clave;
        $this->integrantes = [];
    }

    public function getIntegrantes()
    {
        return $this->integrantes;
    }

    public function setIntegrante(Paciente $paciente)
    {
        array_push($this->integrantes, $paciente);
    }

    public function getDni()
    {
        return $this->dni;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getClave()
    {
        return $this->clave;
    }

    public function getUsuarioMain(string $nombre)
    //Busca el dueno de la cuenta
    {
        $p = null;
        for ($i=0;$i<count($this->integrantes);$i++){
            if(trim($nombre) === trim($this->integrantes[$i]->getNombre())){
                $p = $this->integrantes[$i];
            }
        }
        return $p;
    }

}

?>