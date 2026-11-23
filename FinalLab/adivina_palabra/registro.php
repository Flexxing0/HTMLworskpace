<?php
/* Archivo: registro.php
 * Formulario autoprocesado de registro de nuevos jugadores con validacion
 * cliente, verificacion AJAX de disponibilidad de usuario y validacion
 * completa en servidor (RF-01, RF-02, A-11, A-16).
 */

require_once __DIR__ . '/clases/Sesion.class.php';
require_once __DIR__ . '/clases/Usuario.class.php';

Sesion::iniciar();

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
$nombreUsuario = '';
$correo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnRegistrar']))
{
    $nombreUsuario = isset($_POST['txtUsuario']) ? trim($_POST['txtUsuario']) : '';
    $correo = isset($_POST['txtCorreo']) ? trim($_POST['txtCorreo']) : '';
    $password = isset($_POST['txtPassword']) ? $_POST['txtPassword'] : '';
    $password2 = isset($_POST['txtPassword2']) ? $_POST['txtPassword2'] : '';

    if ($password !== $password2)
    {
        $mensajeError = 'Las contraseñas ingresadas no coinciden.';
    }
    else
    {
        try
        {
            $resultado = Usuario::registrarBD($nombreUsuario, $correo, $password);
            if ($resultado['ok'])
            {
                header('Location: index.php?registro=ok');
                exit;
            }
            else
            {
                $mensajeError = $resultado['mensaje'];
            }
        }
        catch (Throwable $e)
        {
            $mensajeError = 'No se pudo procesar el registro. Intenta nuevamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/estilos.css">
    <title>Adivine la palabra - Registro de jugador</title>
</head>
<body>
    <div class="contenedor-principal">
        <header class="cabecera-sitio">
            <h1>Adivine la palabra</h1>
            <p class="subtitulo">Registro de nuevo jugador</p>
        </header>

        <section class="caja-centrada">
            <article>
                <h2>Crear cuenta</h2>

                <?php
                if ($mensajeError !== '')
                {
                    echo '<div class="alerta alerta-error">' . htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8') . '</div>';
                }
                ?>

                <div id="errorClienteRegistro" class="alerta alerta-error oculto"></div>

                <form id="frmRegistro" name="frmRegistro" method="post" action="registro.php" onSubmit="return validarRegistro();">
                    <fieldset>
                        <legend>Datos del jugador</legend>

                        <div class="campo-formulario">
                            <label for="txtUsuario">Nombre de usuario (unico)</label>
                            <input type="text"
                                   id="txtUsuario"
                                   name="txtUsuario"
                                   maxlength="30"
                                   required
                                   autofocus
                                   placeholder="Ej: jugador_2026"
                                   onBlur="verificarUsuarioDisponible();"
                                   onChange="verificarUsuarioDisponible();"
                                   value="<?php echo htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="texto-ayuda">Entre 3 y 30 caracteres (letras sin tilde, numeros o guion bajo).</span>
                            <span id="estadoUsuarioAjax" class="estado-ajax"></span>
                        </div>

                        <div class="campo-formulario">
                            <label for="txtCorreo">Correo electronico</label>
                            <input type="email"
                                   id="txtCorreo"
                                   name="txtCorreo"
                                   maxlength="120"
                                   required
                                   placeholder="ejemplo@correo.com"
                                   value="<?php echo htmlspecialchars($correo, ENT_QUOTES, 'UTF-8'); ?>">
                        </div>

                        <div class="campo-formulario">
                            <label for="txtPassword">Contraseña</label>
                            <input type="password"
                                   id="txtPassword"
                                   name="txtPassword"
                                   maxlength="64"
                                   required
                                   placeholder="Minimo 6 caracteres">
                            <span class="texto-ayuda">Minimo <?php echo PASSWORD_MIN_LARGO; ?> caracteres.</span>
                        </div>

                        <div class="campo-formulario">
                            <label for="txtPassword2">Confirmar contraseña</label>
                            <input type="password"
                                   id="txtPassword2"
                                   name="txtPassword2"
                                   maxlength="64"
                                   required
                                   placeholder="Repeti la contraseña">
                        </div>

                        <div class="acciones-formulario">
                            <input type="submit" id="btnRegistrar" name="btnRegistrar" value="Registrarme">
                            <a href="index.php" class="boton boton-secundario">Volver al inicio</a>
                        </div>
                    </fieldset>
                </form>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>

    <script src="js/utilidades.js"></script>
    <script src="js/registro.js"></script>
</body>
</html>
