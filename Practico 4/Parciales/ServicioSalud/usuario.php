<?php
require_once 'sessionManager.php';
require_once 'paciente.php';

class Usuario extends Paciente{

    private SessionManager $sesion;
    private array $integrantes;
    private int $dni;
    
    function __construct(string $dni, string $nombre, int $peso, float $estatura)
    {
        $this->sesion = new SessionManager();
        if($this->sesion->exists($dni)){
            $this->dni= $this->sesion->get($dni);
            $this->nombre = $usuario['nombre'];
            $this->peso = (int)$usuario['peso'];
            $this->estatura= (float)$usuario['estatura'];
            $this->imc = (float)$usuario['imc'];
            $this->resultado = $usuario['resultado'];
            $this->integrantes = (array)$usuario['integrantes'];
        }else{
            
        }

    }

    public function getIntegrantes(){
        return $this->integrantes;
    }

    public function setIntegrantes(){

    }


}

?>