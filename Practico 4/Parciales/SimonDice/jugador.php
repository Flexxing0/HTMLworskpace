<?php
class Jugador{

    private int $vidas = 3;
    function __construct(int $vidas=3){
        $this->vidas = $vidas;
    }

    public function getVidas(){
        return $this->vidas;
    }

    public function restaVidas(){
        $this->vidas -= 1;
    }

    public function vidas0(){
        if($this->vidas===0){
            return true;
        }
        else{
            return false;
        }
    }

    public function setVidas(int $vidas){
        $this->vidas = $vidas;
    }

}
?>

