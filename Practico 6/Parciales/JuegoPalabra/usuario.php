<?php
class Usuario
{
    private $puntaje;
    private $pistasDisponibles;

    public function __construct(){
        $this->puntaje = 80;
        $this->pistasDisponibles = 5;
    }

    public function getPuntaje()
    {
        return $this->puntaje;
    }
    public function setPuntaje()
    {
        $this->puntaje -= 15;
    }

    public function getPistasDisponibles()
    {
        return $this->pistasDisponibles;
    }
    public function setPistasDisponibles()
    {
        $this->pistasDisponibles -= 1;
    }
}
?>