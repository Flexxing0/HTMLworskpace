<?php
/* Archivo: vistas/registroView.php
 * Vista de registro de nuevos jugadores con validacion en cliente y verificacion
 * AJAX de disponibilidad del nombre de usuario (RF-01, RF-02, A-11, A-16).
 */

require_once __DIR__ . '/../modelo/Sesion.php';

Sesion::iniciar();

if (Sesion::esJugador())
{
    header('Location: menuView.php');
    exit;
}
if (Sesion::esAdmin())
{
    header('Location: adminIndexView.php');
    exit;
}

$mensajeError = (string) Sesion::obtenerFlash('registro_error', '');
$nombreUsuario = (string) Sesion::obtenerFlash('registro_usuario', '');
$correo = (string) Sesion::obtenerFlash('registro_correo', '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos.css">
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

                <form id="frmRegistro" name="frmRegistro" method="post" action="../controladores/registroController.php" onSubmit="return validarRegistro();">
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
                            <a href="../index.php" class="boton boton-secundario">Volver al inicio</a>
                        </div>
                    </fieldset>
                </form>
            </article>
        </section>

        <footer>
            Laboratorio de Programacion y Lenguajes (UNPSJB) - Titos Felix Aldo
        </footer>
    </div>

    <script src="../js/utilidades.js"></script>
    <script src="../js/registro.js"></script>
</body>
</html>
