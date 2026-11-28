<?php
/* Archivo: modelo/Usuario.php
 * Entidad Usuario y validaciones de formato del modelo (A-11).
 * Las operaciones de acceso a la base de datos se ubican en dao/UsuarioDAO.php.
 */

require_once __DIR__ . '/../db/config.php';

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
}
?>
