<?php
class Usuario
{
    private string $nombre;
    private array $tareasPendientes;
    private array $tareasFinalizadas;
    function __construct(string $nombre)
    {
        $this->nombre = $nombre;
        $this->tareasPendientes = [];
        $this->tareasFinalizadas = [];
    }
    public function setTarea(string $descripcion, string $estado)
    {
        switch($estado){
            case 'Pendiente':
                array_push($this->tareasPendientes, $descripcion);
                break;
            case 'Finalizada':
                array_push($this->tareasFinalizadas, $descripcion);
                break;
        }
    }
    public function setArrayTarea(array $tarea,string $estado)
    {
        switch($estado){
            case "Pendiente":
                $this->tareasPendientes = [];
                $this->tareasPendientes = $tarea;
                break;
            case "Finalizada":
                $this->tareasFinalizadas = [];
                $this->tareasFinalizadas = $tarea;
                break;
        }
    }
    public function getPendientes()
    {
        return $this->tareasPendientes;
    }
    public function getFinalizadas()
    {
        return $this->tareasFinalizadas;
    }
    public function getNombre()
    {
        return $this->nombre;
    }

    public function eliminaTareas()
    {
        $this->tareasFinalizadas = [];
        $this->tareasPendientes = [];
    }

    public function toArray()
    {
        return [
            'nombre' => $this->nombre,
            'tareasPendientes' => $this->tareasPendientes,
            'tareasFinalizadas' => $this->tareasFinalizadas
        ];
    }
}

?>