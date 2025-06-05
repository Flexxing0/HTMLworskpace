<?php
require_once 'sessionManager.php';
$sesion = new SessionManager();
require_once 'funciones.php';
require_once 'cookieManager.php';
require_once 'usuario.php';

$cookieManager = new CookieManager();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Registro</title>
</head>

<body>
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $dniIngresado = trim($_POST['dni']);
        $nombreIngresado = $_POST['nombre'];
        $claveIngresada = trim($_POST['clave']);

        $usuario = new Usuario($dniIngresado, $nombreIngresado, $claveIngresada);

        $cookieManager->setJson($dniIngresado, $usuario);
        $sesion->set('usuario', $usuario);
        header("Location: index.php");
        exit;
    }else{
        formularioRegistro();
    }

    ?>
</body>

</html>