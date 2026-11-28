<?php

date_default_timezone_set('America/Argentina/Buenos_Aires');


define('BD_HOST', 'localhost');
define('BD_USUARIO', 'root');
define('BD_PASSWORD', '');
define('BD_NOMBRE', 'adivina_palabra_titos');
define('BD_PUERTO', 3306);

define('ADMIN_USUARIO', 'administrador');
define('ADMIN_PASSWORD_HASH', '$2y$10$27KVPUz4ujRYMuvAvvIlT.KOikxKs.fvpO/o12JCaqlAHF8oR7BeO');

define('SESION_TIMEOUT_INACTIVIDAD', 1800); 
define('SESION_TIMEOUT_ABSOLUTO', 14400);   


define('PASSWORD_MIN_LARGO', 6);            
define('DIAS_FECHA_RELATIVA_MAX', 30);      

?>