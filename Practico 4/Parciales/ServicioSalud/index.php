<?php
require_once 'sessionManager.php';
require_once 'usuario.php';
require_once 'funciones.php';
$sesion = new SessionManager();
function miAutocargador($nombreClase) {
    // Convierte el nombre de la clase a un nombre de archivo (por ejemplo, "Coche" -> "Coche.php")
    $archivoClase = $nombreClase . '.php';

    // Comprueba si el archivo existe antes de intentar incluirlo
    if (file_exists($archivoClase)) {
        require_once $archivoClase;
    } else {
        // Opcional: Manejo de errores si la clase no se encuentra
        echo "Error: La clase '{$nombreClase}' no se pudo cargar. Archivo '{$archivoClase}' no encontrado.<br>";
    }
}
spl_autoload_register('miAutocargador');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Servicio Clinica</title>
</head>
<body>
    <main>
        <?php
        $cookieManager = new CookieManager();
        if ($sesion->exists('usuario')){
            
            $dato = $sesion->get('usuario');
            if($cookieManager->exists($dato->getDni())){
                $cookieManager->setJson(trim($dato->getDni()),$dato);
            }
            ?>
            <a href="reporte.php">Ver reportaje</a><br>
            <a href="pagCalculadora.php">Ir a calculadora</a>
            <?php
        } else{
            formularioInicioSesion();
        }
        ?>
    </main>
    
</body>
</html>