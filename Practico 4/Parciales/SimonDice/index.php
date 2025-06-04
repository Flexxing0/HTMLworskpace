<?php
// Carga de clases
// La función `cargaClase` debe ser revisada. Debería ser `include($clase . '.php');`
// o `include($clase . '.class.php');` dependiendo de cómo nombres tus archivos.
// Si tus clases están en archivos con el mismo nombre que la clase, sin prefijo `index.`,
// entonces `spl_autoload_register(function($clase) { require_once $clase . '.php'; });` es lo común.
//spl_autoload_register(function ($clase) {
//    if (file_exists($clase . '.php')) {
//        require_once $clase . '.php';
//    }
//});

session_start(); // Siempre inicia la sesión
require_once 'cookieManager.php';
$cookieManager = new CookieManager();

// Si las cookies existen, significa que el usuario ya inició sesión y el juego puede comenzar
if ($cookieManager->exists('usuario') && $cookieManager->exists('cantidad')) {
    $usuario = htmlspecialchars($cookieManager->get('usuario'));
    $cantidad = htmlspecialchars((string) $cookieManager->getInt('cantidad')); // 
    require_once 'manejoJuego.php';
    // Asegura que cantidad sea int

    $manejo = new manejoJuego((int)$cantidad); // Instancia la clase de manejo del juego

    // --- Lógica de procesamiento para el formulario de secuencia ---
    // Este bloque solo se ejecuta cuando el formulario de secuencia es enviado (POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['texto'])) {
        if (!$manejo->haPerdido()) { // Solo procesa si el jugador no ha perdido aún
            $manejo->analizaInputUsuario($_POST['texto']);
        }
        // Después de procesar, la página se recargará y mostrará el nuevo estado
    }
    // --- Fin de la lógica de procesamiento ---

    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <title>Simon dice</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>Simon dice</header>
        <main>
            <?php
            // Comprobar si el jugador ha perdido después de cualquier procesamiento POST
            if ($manejo->haPerdido()) {
                ?>
                <h3>¡Perdiste!</h3>
                <p>La secuencia correcta era: <?php echo implode(' - ', $manejo->getSecuenciaMaestra()); ?></p>
                <p><a href="borrar_cookie.php">Comenzar de nuevo</a></p>
                <?php
            } else {
                // Si no ha perdido, mostrar el estado actual del juego y el formulario para el siguiente intento
                $manejo->mostrarEstadoJuego();
                ?>
                <form action="" method="POST"> <label for="texto">Ingresa la secuencia que memorizaste (ej. R-Y-V): </label>
                    <input type="text" id="texto" name="texto" placeholder="Por ejemplo: R-Y-V" required>
                    <input type="submit" name="Enviar" value="Verificar Secuencia">
                </form>
                <?php
            }
            ?>
        </main>
        <footer></footer>
    </body>
    </html>
    <?php
} else {
    // Si no hay cookies de usuario o cantidad, mostrar el formulario de inicio de sesión
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <title>Inicio de Sesión</title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <header>Simon dice</header>
        <main>
            <form action="inicioSesion.php" method="POST">
                <label for="usuario">Usuario </label>
                <input type="text" id="usuario" name="usuario" placeholder="Ingresa nombre de usuario" required
                    autocomplete="on">
                <label for="cantidad">Ingrese una cantidad de secuencias </label>
                <input type="number" id="cantidad" name="cantidad" min="1" max="10" placeholder="Ejemplo: 2">
                <input type="submit" value="Comenzar Juego">
            </form>
        </main>
        <footer></footer>
    </body>
    </html>
    <?php
}
?>