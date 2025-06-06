<?php 
require_once 'sessionManager.php';
require_once 'usuario.php';
require_once 'cookieManager.php';
$sesion = new SessionManager();
?>
<html>
<head>
<title>Sesiones - P&aacute;gina 1</title>
</head>

<body>
<?php

if (isset($_SESSION['usuario']))
{	//El usuario est� autenticado, por lo tanto se puede cerrar la sesion 
	$cookieManager = new CookieManager();
	$usuario = $sesion->get('usuario');
	$cookieManager->setJson($usuario->getNombre(),$usuario->toArray());
	$_SESSION[] = array();
	session_destroy();
	header("Location: index.php");
}
else
{	//No es un usuario v�lido. Muestro v�nculo para que inicie sesi�n
	
}

?>
</body>
</html>