<?php
/* Archivo: db/config.php
 * Configuracion central de la aplicacion: conexion a BD, credenciales del
 * administrador (hash) y parametros de seguridad y reglas del juego.
 */

date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuracion de la base de datos MySQL (WAMP local)
define('BD_HOST', 'localhost');
define('BD_USUARIO', 'root');
define('BD_PASSWORD', '');
define('BD_NOMBRE', 'adivina_palabra_titos');
define('BD_PUERTO', 3306);

// Credenciales fijas del unico usuario administrador (decision A-12)
// Contrasena en texto plano solo conocida por el administrador; aqui se almacena su hash bcrypt.
define('ADMIN_USUARIO', 'administrador');
define('ADMIN_PASSWORD_HASH', '$2y$10$27KVPUz4ujRYMuvAvvIlT.KOikxKs.fvpO/o12JCaqlAHF8oR7BeO');

// Tiempos de expiracion de sesion contra Session Hijacking (Unidad 7)
define('SESION_TIMEOUT_INACTIVIDAD', 1800); // 30 minutos sin actividad
define('SESION_TIMEOUT_ABSOLUTO', 14400);   // 4 horas desde el inicio de sesion

// Parametros de validacion y reglas del juego
define('PASSWORD_MIN_LARGO', 6);            // Decision A-11
define('DIAS_FECHA_RELATIVA_MAX', 30);      // Decision A-09
?>
