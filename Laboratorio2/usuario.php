<?php
class Usuario{
    private int $nroJuego;
    private int $nroIntento;
    private int $centroNumerico;
    private array $nrosIngresados;
    private array $nrosGenerados;
    private int $puntaje;
    
    function __construct(int $nroJuego=0)
    {
        $this->nroJuego = $nroJuego;
        $this->generaNumeros();
        $this->puntaje = 10;
        $this->nroIntento = 0;
        //$this->centroNumerico = $this->buscactroNumerico();
        $this->nrosIngresados = [];
    }

    public function getNrojuego()
    {
        return $this->nroJuego;
    }

    public function setNrojuego(int $nro)
    {
        $this->nroJuego = $nro;
    }

    public function getIngresados(){
        return $this->nrosIngresados;
    }

    public function setIngresados(int $num)
    {
        array_push($this->nrosIngresados, $num);
    }

    public function getPuntaje()
    {
        return $this->puntaje;
    }
    
    public function restaPuntaje()
    {
        $this->puntaje -= 1;
    }

    public function incrementaNroIntento()
    {
        $this->nroIntento += 1;
    }

    public function getNroIntento()
    {
        return $this->nroJuego;
    }
    public function getNrosGenerados()
    {
        return $this->nrosGenerados;
    }

    public function getCentroNumerico()
    {
        return $this->centroNumerico;
    }

    public function setCentro(){
        $this->centroNumerico = $this->buscactroNumerico();
    }

    private function generaNumeros()
    {
        $this->nrosGenerados = [];
        $cantidadNros = mt_rand(1,100);
        for($i=1 ; $i<=$cantidadNros; $i++)
        {
            array_push($this->nrosGenerados, $i);
        }
    }

    private function sumatoria(array $nros)
    {
    $sumatoria = 0;
    
    for ( $i=0 ; $i< count($nros) ; $i++)
    {
        $sumatoria += $nros[$i];     
    }
    return $sumatoria;
    }

    private function buscactroNumerico()
    {
        $encontrado = 0;
        for($i=0 ; $i<count($this->nrosGenerados) ; $i++)
        {
            $array = $this->divideArray($this->nrosGenerados, $this->nrosGenerados[$i]);
            if($this->sumatoria($array['1']) === $this->sumatoria($array['2']))
            {
                $encontrado = $i; 
            }
        }
        return $this->nrosGenerados[$encontrado];
    }

    private function divideArray(array $nros, $nroIngresado)
    {
    $array1 = [];
    $array2 = [];
    for ($i=0 ; $i<count($nros); $i++ ){
        if ($nros[$i]<$nroIngresado)
        {
            array_push($array1, $nros[$i]);
        } else if ($nros[$i]>$nroIngresado)
        {
            array_push($array2, $nros[$i]);
        }
    }
    return ['1'=>$array1,'2'=>$array2];
    }

}

?>