<?php
class Cliente
{

    private $idCliente;
    private $documento;
    private $nombre;
    private $apellido;
    private $fechaNac;
    private $domicilio;

    public function __construct(){
      
    }
    public function getID()
    {
        return $this->idCliente;
    }
    public function setID($id)
    {
        $this->idCliente = $id;
    }

    public function getDocCliente()
    {
        return $this->documento;
    }
    public function setDocCliente($doc)
    {
        $this->documento = $doc;
    }

    public function getNombreCliente()
    {   
        return $this->nombre;
    }
    public function setNombreCliente($nombreNuevo)
    {
        $this->nombre = $nombreNuevo;
    }

    public function getApellidoCliente()
    {
        return $this->apellido;
    }
    public function setApellidoCliente($apellidoNuevo)
    {
        $this->apellido = $apellidoNuevo;
    }

    public function getFechaNac()
    {
        return $this->fechaNac;
    }
    public function setFechaNac($fecha)
    {
        $this->fechaNac = $fecha;
    }

    public function getDomicilio()
    {
        return $this->domicilio;
    }
    public function setDomicilio($dom)
    {
        $this->domicilio = $dom;
    }

    public static function getClienteBD($documento)
    {
        $con = new mysqli('localhost', 'root', '', 'clientes') or die("Error al conectar");
        $cliente = NULL;
        
        $query = "SELECT * FROM clientes WHERE documento = ".$documento;
        $resu = $con->query($query);
        while ($regi = $resu->fetch_object()) 
        {
            $cliente = new Cliente();
            $cliente->setID($regi->id_cliente);
            $cliente->setDocCliente($regi->documento);
            $cliente->setApellidoCliente($regi->apellido);
            $cliente->setNombreCliente($regi->nombre);
            $cliente->setFechaNac($regi->fecha_nacimiento);
            $cliente->setDomicilio($regi->domicilio);
        }
        $resu->free();
        $con->close();
        return $cliente;
    }

    public function toArray()
    {
        return ['id_cliente'=>$this->idCliente,
        'documento'=>$this->documento,
        'apellido'=>$this->apellido,
        'nombre'=>$this->nombre,
        'fecha_nacimiento'=>$this->fechaNac,
        'domicilio'=>$this->domicilio
    ];
    }


}
?>
