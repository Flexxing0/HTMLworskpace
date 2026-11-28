<?php
/* Archivo: controladores/loginController.php
 * Controlador POST de autenticacion para jugadores y administrador (RF-03, A-12).
 */

require_once __DIR__ . '/../modelo/Sesion.php';
require_once __DIR__ . '/../dao/UsuarioDAO.php';

Sesion::iniciar();

if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
    header('Location: ../index.php');
    exit;
}

$usuarioIngresado = isset($_POST['txtUsuario']) ? trim($_POST['txtUsuario']) : '';
$passwordIngresada = isset($_POST['txtPassword']) ? $_POST['txtPassword'] : '';

try
{
    $resultado = UsuarioDAO::autenticar($usuarioIngresado, $passwordIngresada);
    if ($resultado['ok'])
    {
        if ($resultado['rol'] === 'admin')
        {
            Sesion::iniciarSesionAdmin();
            header('Location: ../vistas/adminIndexView.php');
            exit;
        }
        else
        {
            Sesion::iniciarSesionUsuario($resultado['usuario']);
            header('Location: ../vistas/menuView.php');
            exit;
        }
    }
    else
    {
        Sesion::guardarFlash('login_error', $resultado['mensaje']);
        Sesion::guardarFlash('login_usuario', $usuarioIngresado);
        header('Location: ../index.php');
        exit;
    }
}
catch (Throwable $e)
{
    Sesion::guardarFlash('login_error', 'Ocurrio un problema al iniciar sesion. Intenta nuevamente.');
    Sesion::guardarFlash('login_usuario', $usuarioIngresado);
    header('Location: ../index.php');
    exit;
}
?>
