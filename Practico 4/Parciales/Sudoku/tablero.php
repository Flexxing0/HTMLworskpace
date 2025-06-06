<?php
class Tablero
{
    private const NUMEROS = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];

    private array $casillas;


    function __construct()
    {
        $this->casillas = [];
    }

    private function generaNumsCasilla()
    {
        $copiaNumeros = self::NUMEROS;
        $array = $this->generaArrayVacio();
        $indiceAleatorio = mt_rand(2, 4);
        for ($i = 0; $i < $indiceAleatorio; $i++) {
            $indice1 = array_rand($copiaNumeros);
            $indice2 = array_rand($array);
            $elemento = $copiaNumeros[$indice1];
            unset($copiaNumeros[$indice1]);
            $copiaNumeros = array_values($copiaNumeros);
            $array[$indice2] = $elemento;
        }
        return $array;
    }

    public function generaCasillas()
    {
        $this->casillas = [];
        for ($i = 0; $i < 9; $i++) {
            array_push($this->casillas, $this->generaNumsCasilla());
        }

    }

    private function generaArrayVacio()
    {
        $array = [];
        for ($i = 0; $i < 9; $i++) {
            array_push($array, ' ');
        }
        return $array;
    }

    public function eliminaCasillas()
    {
        $this->casillas = [];
        for ($i = 0; $i < 9; $i++) {
            array_push($this->casillas, $this->generaArrayVacio());
        }
        return $this->casillas;
    }

    public function getCasillas()
    {
        return $this->casillas;
    }

}
?>