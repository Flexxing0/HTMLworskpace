<?php
/* Archivo: controladores/registroController.php
 * Controlador POST de registro de nuevos jugadores (RF-01, A-11, A-16).
 */

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

Sesion::iniciar();

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ../vistas/registroView.php');
    exit;
}

$nombreUsuario = isset($_POST['txtUsuario']) ? trim($_POST['txtUsuario']) : '';
$correo = isset($_POST['txtCorreo']) ? trim($_POST['txtCorreo']) : '';
$password = isset($_POST['txtPassword']) ? $_POST['txtPassword'] : '';
$password2 = isset($_POST['txtPassword2']) ? $_POST['txtPassword2'] : '';

if ($password !== $password2)
{
    Sesion::guardarFlash('registro_error', 'Las contraseñas ingresadas no coinciden.');
    Sesion::guardarFlash('registro_usuario', $nombreUsuario);
    Sesion::guardarFlash('registro_correo', $correo);
    header('Location: ../vistas/registroView.php');
    exit;
}

try
{
    $resultado = UsuarioDAO::registrar($nombreUsuario, $correo, $password);
    if ($resultado['ok'])
    {
        header('Location: ../index.php?registro=ok');
        exit;
    }
    else
    {
        Sesion::guardarFlash('registro_error', $resultado['mensaje']);
        Sesion::guardarFlash('registro_usuario', $nombreUsuario);
        Sesion::guardarFlash('registro_correo', $correo);
        header('Location: ../vistas/registroView.php');
        exit;
    }
}
catch (Throwable $e)
{
    Sesion::guardarFlash('registro_error', 'No se pudo procesar el registro. Intenta nuevamente.');
    Sesion::guardarFlash('registro_usuario', $nombreUsuario);
    Sesion::guardarFlash('registro_correo', $correo);
    header('Location: ../vistas/registroView.php');
    exit;
}
?>
