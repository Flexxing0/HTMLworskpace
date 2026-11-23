<?php
/* Archivo: index.php
 * Pagina de inicio y formulario de login autoprocesado para jugadores y
 * administrador (RF-03, RF-21, A-12).
 */

require_once __DIR__ . '/clases/Sesion.class.php';
require_once __DIR__ . '/clases/Usuario.class.php';

Sesion::iniciar();

// Si ya existe sesion valida, redirige al panel correspondiente al rol
if (Sesion::esJugador())
{
    header('Location: menu.php');
    exit;
}
if (Sesion::esAdmin())
{
    header('Location: admin/index.php');
    exit;
}

$mensajeError = '';
$mensajeAviso = Sesion::obtenerMensajeSesion();
$usuarioIngresado = '';

if (isset($_GET['registro']) && $_GET['registro'] === 'ok')
{
    $mensajeAviso = 'Cuenta creada con exito. Ya podes iniciar sesion con tus credenciales.';
}
else if (isset($_GET['salir']) && $_GET['salir'] === 'ok')
{
    $mensajeAviso = 'Cerraste sesion correctamente.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnIngresar']))
{
    $usuarioIngresado = isset($_POST['txtUsuario']) ? trim($_POST['txtUsuario']) : '';
    $passwordIngresada = isset($_POST['txtPassword']) ? $_POST['txtPassword'] : '';

    try
    {
        $resultado = Usuario::autenticarBD($usuarioIngresado, $passwordIngresada);
        if ($resultado['ok'])
        {
            if ($resultado['rol'] === 'admin')
            {
                Sesion::iniciarSesionAdmin();
                header('Location: admin/index.php');
                exit;
            }
            else
            {
                Sesion::iniciarSesionUsuario($resultado['usuario']);
                header('Location: menu.php');
                exit;
            }
        }
        else
        {
            $mensajeError = $resultado['mensaje'];
        }
    }
    catch (Throwable $e)
    {
        $mensajeError = 'Ocurrio un problema al iniciar sesion. Intenta nuevamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Adivine la palabra - Iniciar sesion</title>
</head>
<body>
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Juego de adivinanza de palabras en linea</p>
        </header>

        <section class="caja-centrada">
            <article>
                <h2>Iniciar sesion</h2>

                <?php
                if ($mensajeAviso !== '')
                {
                    echo '<div class="alerta alerta-info">' . htmlspecialchars($mensajeAviso, ENT_QUOTES, 'UTF-8') . '</div>';
                }
                if ($mensajeError !== '')
                {
                    echo '<div class="alerta alerta-error">' . htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') . '</div>';
                }
                ?>

                <div id="errorClienteLogin" class="alerta alerta-error oculto"></div>

                <form id="frmLogin" name="frmLogin" method="post" action="index.php" onSubmit="return validarLogin();">
                    <fieldset>
                        <legend>Acceso a la aplicacion</legend>

                        <div class="campo-formulario">
                            <label for="txtUsuario">Nombre de usuario</label>
                            <input type="text"
                                   id="txtUsuario"
                                   name="txtUsuario"
                                   maxlength="30"
                                   required
                                   autofocus
                                   placeholder="Ingresa tu nombre de usuario"
                                   value="<?php echo htmlspecialchars($usuarioIngresado, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="campo-formulario">
                            <label for="txtPassword">Contraseña</label>
                            <input type="password"
                                   id="txtPassword"
                                   name="txtPassword"
                                   maxlength="64"
                                   required
                                   placeholder="Ingresa tu contraseña">
                        </div>

                        <div class="acciones-formulario">
                            <input type="submit" id="btnIngresar" name="btnIngresar" value="Ingresar">
                        </div>
                    </fieldset>
                </form>

                <p class="texto-centro">
                    ¿Todavia no tenes cuenta? <a href="registro.php">Registrate para jugar</a>
                </p>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>

    <script src="js/utilidades.js"></script>
</body>
</html>
