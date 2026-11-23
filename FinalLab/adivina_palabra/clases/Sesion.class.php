<?php
/* Archivo: clases/Sesion.class.php
 * Gestion de sesiones PHP: inicio seguro, regeneracion de ID, control de
 * expiracion por inactividad/absoluta y restriccion de acceso por rol (RF-21).
 */

require_once __DIR__ . '/../config/config.php';

class Sesion
{
    public static function iniciar()
    {
        if (session_status() === PHP_SESSION_NONE)
        {
            session_set_cookie_params(array(
                'lifetime' => 0,
                'path' => '/',
                'httponly' => true,
                'samesite' => 'Lax'
            ));
            session_start();
        }

        // Control de expiracion por inactividad y tiempo absoluto (Unidad 7)
        if (isset($_SESSION['rol']))
        {
            $ahora = time();
            $inactivo = isset($_SESSION['ultima_actividad'])
                && (($ahora - $_SESSION['ultima_actividad']) > SESION_TIMEOUT_INACTIVIDAD);
            $expiradoAbsoluto = isset($_SESSION['inicio_sesion'])
                && (($ahora - $_SESSION['inicio_sesion']) > SESION_TIMEOUT_ABSOLUTO);

            if ($inactivo || $expiradoAbsoluto)
            {
                self::cerrar();
                session_start();
                $_SESSION['mensaje_sesion'] = 'Tu sesion expiro por seguridad. Volve a iniciar sesion.';
                return;
            }

            $_SESSION['ultima_actividad'] = $ahora;
        }
    }

    public static function iniciarSesionUsuario($usuario)
    {
        self::iniciar();
        session_regenerate_id(true);
        $_SESSION = array();
        $_SESSION['id_usuario'] = $usuario->getId();
        $_SESSION['nombre_usuario'] = $usuario->getNombreUsuario();
        $_SESSION['rol'] = 'jugador';
        $_SESSION['inicio_sesion'] = time();
        $_SESSION['ultima_actividad'] = time();
    }

    public static function iniciarSesionAdmin()
    {
        self::iniciar();
        session_regenerate_id(true);
        $_SESSION = array();
        $_SESSION['id_usuario'] = 0;
        $_SESSION['nombre_usuario'] = ADMIN_USUARIO;
        $_SESSION['rol'] = 'admin';
        $_SESSION['inicio_sesion'] = time();
        $_SESSION['ultima_actividad'] = time();
    }

    public static function estaLogueado()
    {
        self::iniciar();
        return isset($_SESSION['rol']) && isset($_SESSION['nombre_usuario']);
    }

    public static function esJugador()
    {
        self::iniciar();
        return isset($_SESSION['rol']) && $_SESSION['rol'] === 'jugador' && isset($_SESSION['id_usuario']);
    }

    public static function esAdmin()
    {
        self::iniciar();
        return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin';
    }

    public static function getIdUsuario()
    {
        self::iniciar();
        return isset($_SESSION['id_usuario']) ? (int) $_SESSION['id_usuario'] : 0;
    }

    public static function getNombreUsuario()
    {
        self::iniciar();
        return isset($_SESSION['nombre_usuario']) ? $_SESSION['nombre_usuario'] : '';
    }

    public static function obtenerMensajeSesion()
    {
        self::iniciar();
        $mensaje = '';
        if (isset($_SESSION['mensaje_sesion']))
        {
            $mensaje = $_SESSION['mensaje_sesion'];
            unset($_SESSION['mensaje_sesion']);
        }
        return $mensaje;
    }

    // Protege paginas y endpoints AJAX exclusivos del jugador (RF-21)
    public static function exigirJugador($esAjax = false, $rutaLogin = 'index.php', $rutaAdmin = 'admin/index.php')
    {
        self::iniciar();

        if (!self::estaLogueado())
        {
            if ($esAjax)
            {
                header('Content-Type: application/json; charset=utf-8');
                $obj = new stdClass();
                $obj->ok = false;
                $obj->sesionExpirada = true;
                $obj->mensaje = 'Sesion no valida o expirada. Inicia sesion nuevamente.';
                echo json_encode($obj);
                exit;
            }
            header('Location: ' . $rutaLogin);
            exit;
        }

        if (!self::esJugador())
        {
            if ($esAjax)
            {
                header('Content-Type: application/json; charset=utf-8');
                $obj = new stdClass();
                $obj->ok = false;
                $obj->sesionExpirada = true;
                $obj->mensaje = 'Acceso denegado: esta seccion es exclusiva para jugadores.';
                echo json_encode($obj);
                exit;
            }
            header('Location: ' . $rutaAdmin);
            exit;
        }
    }

    // Protege paginas exclusivas del administrador (RF-21)
    public static function exigirAdmin($rutaLogin = '../index.php', $rutaMenuJugador = '../menu.php')
    {
        self::iniciar();

        if (!self::estaLogueado())
        {
            header('Location: ' . $rutaLogin);
            exit;
        }

        if (!self::esAdmin())
        {
            header('Location: ' . $rutaMenuJugador);
            exit;
        }
    }

    // Limpia $_SESSION, invalida la cookie y destruye la sesion (Unidad 4)
    public static function cerrar()
    {
        if (session_status() === PHP_SESSION_NONE)
        {
            session_start();
        }

        $_SESSION = array();

        if (ini_get('session.use_cookies'))
        {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}
?>
