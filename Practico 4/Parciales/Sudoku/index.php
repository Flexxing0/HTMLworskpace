<?php
require_once 'sessionManager.php';
require_once 'tablero.php';
require_once 'funciones.php';
$sesion = new SessionManager();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="style.css">
    <title>Sudoku</title>
</head>
<body>
    <main>
        <?php
        if ($sesion->exists('tablero')) 
        {
            $tablero = $sesion->get('tablero');
            generaTablero($tablero->getCasillas());
        } else {
            $tablero = new Tablero();
            $tablero->eliminaCasillas();
            generaTablero($tablero->getCasillas());
            $sesion->set('tablero',$tablero);
        }
        ?>

    </main>
</body>
</html>