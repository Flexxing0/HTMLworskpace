<?php

require_once __DIR__ . '/../modelo/Usuario.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

header('Content-Type: application/json; charset=utf-8');

$obj = new stdClass();
$obj->ok = false;
$obj->disponible = false;
$obj->mensaje = 'No se recibio el nombre de usuario.';

if (isset($_POST['nombreUsuario']))
{
    $nombre = $_POST['nombreUsuario'];
    $validacion = Usuario::validarNombreUsuario($nombre);

    if (!$validacion['ok'])
    {
        $obj->ok = true;
        $obj->disponible = false;
        $obj->mensaje = $validacion['mensaje'];
    }
    else
    {
        try
        {
            $existe = UsuarioDAO::existeNombreUsuario($validacion['valor']);
            $obj->ok = true;
            if ($existe)
            {
                $obj->disponible = false;
                $obj->mensaje = 'El nombre de usuario ya esta ocupado. Proba con otro.';
            }
            else
            {
                $obj->disponible = true;
                $obj->mensaje = 'Nombre de usuario disponible.';
            }
        }
        catch (Throwable $e)
        {
            $obj->ok = false;
            $obj->disponible = false;
            $obj->mensaje = 'No se pudo consultar la disponibilidad en este momento.';
        }
    }
}

echo json_encode($obj);
?>
