<?php

class Paciente
{

    protected string $nombre;
    protected float $peso;
    protected float $estatura;
    protected float $imc;
    protected string $resultado;

    function __construct(string $nombre, float $peso, float $estatura)
    {
        $this->nombre = $nombre;
        $this->peso = $peso;
        $this->estatura = $estatura;
    }

    public function getNombre()
    {
        return $this->nombre;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function getEstatura()
    {
        return $this->estatura;
    }

    public function getIMC()
    {
        return $this->imc;
    }

    public function setIMC(float $imc)
    {
        $this->imc = $imc;
    }

    public function setResultado(string $resultado)
    {
        $this->resultado = $resultado;
    }

    public function getResultado()
    {
        return $this->resultado;
    }

}

?>