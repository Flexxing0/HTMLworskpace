<?php
/* Archivo: clases/Usuario.class.php
 * Entidad Usuario y operaciones de registro, autenticacion, estadisticas
 * de bienvenida (ultimo juego y porcentaje de ganadas) y ranking Top 10.
 */

require_once __DIR__ . '/Conexion.class.php';

class Usuario
{
    private $id;
    private $nombreUsuario;
    private $correo;
    private $passwordHash;
    private $puntajeAcumulado;
    private $fechaRegistro;

    public function __construct()
    {
        $this->id = 0;
        $this->nombreUsuario = '';
        $this->correo = '';
        $this->passwordHash = '';
        $this->puntajeAcumulado = 0;
        $this->fechaRegistro = '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = (int) $id;
    }

    public function getNombreUsuario()
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario($nombreUsuario)
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    public function getCorreo()
    {
        return $this->correo;
    }

    public function setCorreo($correo)
    {
        $this->correo = $correo;
    }

    public function getPasswordHash()
    {
        return $this->passwordHash;
    }

    public function setPasswordHash($passwordHash)
    {
        $this->passwordHash = $passwordHash;
    }

    public function getPuntajeAcumulado()
    {
        return $this->puntajeAcumulado;
    }

    public function setPuntajeAcumulado($puntajeAcumulado)
    {
        $this->puntajeAcumulado = (int) $puntajeAcumulado;
    }

    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;
    }

    // Validacion de formato del nombre de usuario en el servidor (A-11)
    public static function validarNombreUsuario($nombreUsuario)
    {
        $nombre = trim($nombreUsuario);
        if ($nombre === '')
        {
            return array('ok' => false, 'mensaje' => 'El nombre de usuario es obligatorio.');
        }

        $largo = mb_strlen($nombre, 'UTF-8');
        if ($largo < 3 || $largo > 30)
        {
            return array('ok' => false, 'mensaje' => 'El nombre de usuario debe tener entre 3 y 30 caracteres.');
        }

        if (!preg_match('/^[a-zA-Z0-9_]+$/', $nombre))
        {
            return array('ok' => false, 'mensaje' => 'El nombre de usuario solo puede contener letras sin tilde, numeros y guion bajo (_).');
        }

        if (mb_strtolower($nombre, 'UTF-8') === mb_strtolower(ADMIN_USUARIO, 'UTF-8'))
        {
            return array('ok' => false, 'mensaje' => 'Ese nombre de usuario esta reservado y no puede registrarse.');
        }

        return array('ok' => true, 'valor' => $nombre);
    }

    // Verifica si un nombre de usuario ya existe en la BD o esta reservado
    public static function existeNombreUsuarioBD($nombreUsuario)
    {
        $nombre = trim($nombreUsuario);
        if ($nombre === '')
        {
            return false;
        }

        if (mb_strtolower($nombre, 'UTF-8') === mb_strtolower(ADMIN_USUARIO, 'UTF-8'))
        {
            return true;
        }

        $con = Conexion::conectar();
        $query = 'SELECT id FROM usuarios WHERE nombre_usuario = ? LIMIT 1';
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $resu = $stmt->get_result();
        $existe = ($resu->num_rows > 0);
        $resu->free();
        $stmt->close();
        $con->close();

        return $existe;
    }

    // Alta de nuevo jugador en BD (RF-01, A-11, A-16)
    public static function registrarBD($nombreUsuario, $correo, $passwordPlano)
    {
        $valUsuario = self::validarNombreUsuario($nombreUsuario);
        if (!$valUsuario['ok'])
        {
            return $valUsuario;
        }
        $nombreLimpio = $valUsuario['valor'];

        $correoLimpio = trim($correo);
        if ($correoLimpio === '' || mb_strlen($correoLimpio, 'UTF-8') > 120 || !filter_var($correoLimpio, FILTER_VALIDATE_EMAIL))
        {
            return array('ok' => false, 'mensaje' => 'Ingresá una direccion de correo electronico valida.');
        }

        if (mb_strlen($passwordPlano, 'UTF-8') < PASSWORD_MIN_LARGO)
        {
            return array('ok' => false, 'mensaje' => 'La contraseña debe tener al menos ' . PASSWORD_MIN_LARGO . ' caracteres.');
        }

        if (mb_strlen($passwordPlano, 'UTF-8') > 64)
        {
            return array('ok' => false, 'mensaje' => 'La contraseña no puede superar los 64 caracteres.');
        }

        if (self::existeNombreUsuarioBD($nombreLimpio))
        {
            return array('ok' => false, 'mensaje' => 'El nombre de usuario ya esta en uso. Elegi otro.');
        }

        $hash = password_hash($passwordPlano, PASSWORD_DEFAULT);

        try
        {
            $con = Conexion::conectar();
            $query = 'INSERT INTO usuarios (nombre_usuario, correo, password_hash) VALUES (?, ?, ?)';
            $stmt = $con->prepare($query);
            $stmt->bind_param('sss', $nombreLimpio, $correoLimpio, $hash);
            $stmt->execute();
            $stmt->close();
            $con->close();

            return array('ok' => true, 'mensaje' => 'Registro completado con exito. Ya podes iniciar sesion.');
        }
        catch (mysqli_sql_exception $e)
        {
            if (isset($con))
            {
                $con->close();
            }
            return array('ok' => false, 'mensaje' => 'No se pudo completar el registro. Verifica que el usuario no exista.');
        }
    }

    // Autentica tanto al administrador (A-12) como a jugadores registrados (RF-03)
    public static function autenticarBD($nombreUsuario, $passwordPlano)
    {
        $nombre = trim($nombreUsuario);
        if ($nombre === '' || $passwordPlano === '')
        {
            return array('ok' => false, 'mensaje' => 'Completa usuario y contraseña.');
        }

        // Caso 1: Usuario administrador definido en config/config.php
        if (mb_strtolower($nombre, 'UTF-8') === mb_strtolower(ADMIN_USUARIO, 'UTF-8'))
        {
            if (password_verify($passwordPlano, ADMIN_PASSWORD_HASH))
            {
                return array(
                    'ok' => true,
                    'rol' => 'admin',
                    'usuario' => null,
                    'mensaje' => 'Ingreso como administrador correcto.'
                );
            }
            return array('ok' => false, 'mensaje' => 'Usuario o contraseña incorrectos.');
        }

        // Caso 2: Jugador en tabla usuarios
        $con = Conexion::conectar();
        $query = 'SELECT id, nombre_usuario, correo, password_hash, puntaje_acumulado, fecha_registro
                  FROM usuarios
                  WHERE nombre_usuario = ?
                  LIMIT 1';
        $stmt = $con->prepare($query);
        $stmt->bind_param('s', $nombre);
        $stmt->execute();
        $resu = $stmt->get_result();

        $objUsuario = null;
        if ($regi = $resu->fetch_object())
        {
            if (password_verify($passwordPlano, $regi->password_hash))
            {
                $objUsuario = new Usuario();
                $objUsuario->setId($regi->id);
                $objUsuario->setNombreUsuario($regi->nombre_usuario);
                $objUsuario->setCorreo($regi->correo);
                $objUsuario->setPasswordHash($regi->password_hash);
                $objUsuario->setPuntajeAcumulado($regi->puntaje_acumulado);
                $objUsuario->setFechaRegistro($regi->fecha_registro);
            }
        }

        $resu->free();
        $stmt->close();
        $con->close();

        if ($objUsuario === null)
        {
            return array('ok' => false, 'mensaje' => 'Usuario o contraseña incorrectos.');
        }

        return array(
            'ok' => true,
            'rol' => 'jugador',
            'usuario' => $objUsuario,
            'mensaje' => 'Inicio de sesion exitoso.'
        );
    }

    // Calcula mensaje personalizado de bienvenida (RF-05, A-08, A-09)
    public static function obtenerEstadisticasBienvenidaBD($idUsuario)
    {
        $id = (int) $idUsuario;
        $con = Conexion::conectar();

        $query = "SELECT u.nombre_usuario,
                         u.puntaje_acumulado,
                         COUNT(p.id) AS total_partidas,
                         SUM(CASE WHEN p.resultado = 'ganada' THEN 1 ELSE 0 END) AS ganadas,
                         MAX(p.fecha_fin) AS ultima_fecha,
                         DATEDIFF(CURDATE(), DATE(MAX(p.fecha_fin))) AS dias_desde_ultima
                  FROM usuarios u
                  LEFT JOIN partidas p ON p.id_usuario = u.id
                  WHERE u.id = ?
                  GROUP BY u.id";
        $stmt = $con->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resu = $stmt->get_result();
        $regi = $resu->fetch_object();
        $resu->free();
        $stmt->close();
        $con->close();

        if (!$regi)
        {
            return null;
        }

        $totalPartidas = (int) $regi->total_partidas;
        $ganadas = (int) $regi->ganadas;
        $puntajeAcumulado = (int) $regi->puntaje_acumulado;
        $nombre = $regi->nombre_usuario;

        if ($totalPartidas === 0)
        {
            return array(
                'nombreUsuario' => $nombre,
                'puntajeAcumulado' => $puntajeAcumulado,
                'totalPartidas' => 0,
                'ganadas' => 0,
                'porcentajeGanadas' => 0,
                'tienePartidas' => false,
                'textoUltimoJuego' => '',
                'lineaSaludo' => 'Bienvenido @' . $nombre . '!!',
                'lineaUltimoJuego' => 'Todavia no jugaste ninguna partida.',
                'lineaAliento' => 'Animate a elegir una categoria y comenzar tu primer desafio!!'
            );
        }

        $porcentaje = (int) round(($ganadas / $totalPartidas) * 100);
        $dias = (int) $regi->dias_desde_ultima;

        // Formato relativo segun decision A-09 (N = 30 dias)
        if ($dias <= 0)
        {
            $textoUltimoJuego = 'hoy';
        }
        else if ($dias === 1)
        {
            $textoUltimoJuego = 'ayer';
        }
        else if ($dias <= DIAS_FECHA_RELATIVA_MAX)
        {
            $textoUltimoJuego = 'hace ' . $dias . ' dias';
        }
        else
        {
            $textoUltimoJuego = 'el ' . date('d/m/Y', strtotime($regi->ultima_fecha));
        }

        if ($porcentaje >= 70)
        {
            $lineaAliento = 'Recorda que sos un gran adivinador de palabras porque acertaste el ' . $porcentaje . '% de las veces!! A por ello nuevamente!!';
        }
        else if ($porcentaje >= 40)
        {
            $lineaAliento = 'Venis muy bien porque ganaste el ' . $porcentaje . '% de tus partidas!! Segui asi y supera tu marca!!';
        }
        else
        {
            $lineaAliento = 'Ganaste el ' . $porcentaje . '% de tus partidas hasta ahora. Cada partida suma experiencia, a por ello nuevamente!!';
        }

        return array(
            'nombreUsuario' => $nombre,
            'puntajeAcumulado' => $puntajeAcumulado,
            'totalPartidas' => $totalPartidas,
            'ganadas' => $ganadas,
            'porcentajeGanadas' => $porcentaje,
            'tienePartidas' => true,
            'textoUltimoJuego' => $textoUltimoJuego,
            'lineaSaludo' => 'Bienvenido @' . $nombre . '!!',
            'lineaUltimoJuego' => 'Tu ultimo juego fue ' . $textoUltimoJuego . '!!',
            'lineaAliento' => $lineaAliento
        );
    }

    // Obtiene el Top 10 ordenado por puntaje DESC y nombre ASC en empate (RF-17, A-13)
    public static function obtenerTop10BD()
    {
        $con = Conexion::conectar();
        $ranking = array();

        $query = 'SELECT id, nombre_usuario, puntaje_acumulado
                  FROM usuarios
                  ORDER BY puntaje_acumulado DESC, nombre_usuario ASC
                  LIMIT 10';
        $resu = $con->query($query);
        $puesto = 1;
        while ($regi = $resu->fetch_object())
        {
            $item = new stdClass();
            $item->puesto = $puesto;
            $item->id = (int) $regi->id;
            $item->nombreUsuario = $regi->nombre_usuario;
            $item->puntajeAcumulado = (int) $regi->puntaje_acumulado;
            $ranking[] = $item;
            $puesto++;
        }

        $resu->free();
        $con->close();

        return $ranking;
    }
}
?>
