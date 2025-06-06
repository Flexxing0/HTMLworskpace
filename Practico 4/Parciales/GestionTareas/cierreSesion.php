<?php session_start();	?>
<html>
<head>
<title>Sesiones - P&aacute;gina 1</title>
</head>

<body>
<?php

if (isset($_SESSION['usuario']))
{	//El usuario est� autenticado, por lo tanto se puede cerrar la sesion 
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