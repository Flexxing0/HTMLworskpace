<?php
require_once 'jugador.php';
require_once 'arrayUtils.php';
require_once 'sessionManager.php';
require_once 'cookieManager.php';

class manejoJuego
{
    private const COLORES = ['R', 'Y', 'V', 'A']; // Colores posibles
    private array $secuenciaMaestra; // La secuencia completa y generada una vez al inicio del juego
    private array $secuenciaActualRonda; // La parte de la secuencia maestra que se muestra en la ronda actual (lo que el usuario debe adivinar)
    private SessionManager $sesion;
    private Jugador $jugador; // Debe ser una instancia de Jugador
    private int $cantidadColoresTotal; // Longitud total de la secuencia maestra
    private int $rondaActualIndice; // Índice del color que se debe añadir en la siguiente ronda (o el número de colores ya mostrados)

    function __construct(int $cantidad)
    {
        $this->sesion = new SessionManager();
        $this->cantidadColoresTotal = $cantidad;

        // Recuperar vidas de la sesión o inicializar jugador si es una nueva partida
        $vidas_guardadas = $this->sesion->get("vidas", 3); // Valor por defecto 3
        $this->jugador = new Jugador($vidas_guardadas); // Pasa las vidas al constructor de Jugador

        if (!$this->sesion->exists("secuenciaMaestra")) {
            // Es una nueva partida o la sesión se ha perdido
            $this->secuenciaMaestra = [];
            $this->secuenciaActualRonda = [];
            $this->rondaActualIndice = 0; // Se empieza mostrando el primer color
            $this->generaSecuenciaAleatoria(); // Genera la secuencia maestra

            // Guarda el estado inicial del juego en sesión
            $this->sesion->set("secuenciaMaestra", $this->secuenciaMaestra);
            $this->sesion->set("secuenciaActualRonda", $this->secuenciaActualRonda); // Inicialmente vacío
            $this->sesion->set("rondaActualIndice", $this->rondaActualIndice);
            $this->sesion->set("vidas", $this->jugador->getVidas());

            // En la primera carga, solo se muestra el primer color para la ronda 0
            $this->avanzarRonda(); // Revela el primer color
        } else {
            // Cargar estado existente de la sesión
            $this->secuenciaMaestra = $this->sesion->get("secuenciaMaestra");
            $this->secuenciaActualRonda = $this->sesion->get("secuenciaActualRonda");
            $this->rondaActualIndice = $this->sesion->get("rondaActualIndice");
            // Las vidas ya se cargaron en el constructor de Jugador
        }
    }

    // Genera la secuencia maestra completa al inicio del juego
    private function generaSecuenciaAleatoria()
    {
        for ($i = 0; $i < $this->cantidadColoresTotal; $i++) {
            $valor = ArrayUtils::randomValue(self::COLORES);
            array_push($this->secuenciaMaestra, $valor);
        }
        $this->sesion->set("secuenciaMaestra", $this->secuenciaMaestra);
    }

    // Este método analiza la entrada del usuario para la ronda actual
    // y decide si el juego avanza o si el jugador pierde una vida.
    public function procesarInputUsuario(string $input_texto_usuario)
    {
        // Limpiar el texto ingresado por el usuario (eliminar espacios y guiones)
        $input_texto_limpio = str_replace([' ', '-'], '', $input_texto_usuario);
        $arrayTextoUsuario = str_split($input_texto_limpio);

        $esCorrectoEstaRonda = true;

        // 1. Verificar si la longitud de la entrada del usuario coincide con la secuencia mostrada
        if (count($this->secuenciaActualRonda) !== count($arrayTextoUsuario)) {
            $esCorrectoEstaRonda = false;
            echo "<p style='color: red;'>ERROR: La longitud de tu secuencia no coincide con la esperada (ingresaste " . count($arrayTextoUsuario) . " colores, se esperaban " . count($this->secuenciaActualRonda) . ").</p>";
        } else {
            // 2. Comparar cada elemento de la secuencia ingresada con la esperada
            for ($i = 0; $i < count($this->secuenciaActualRonda); $i++) {
                if ($this->secuenciaActualRonda[$i] !== $arrayTextoUsuario[$i]) {
                    $esCorrectoEstaRonda = false;
                    echo "<p style='color: red;'>ERROR: Secuencia incorrecta en el color " . ($i + 1) . ". Esperado: " . $this->secuenciaActualRonda[$i] . ", Recibido: " . $arrayTextoUsuario[$i] . "</p>";
                    break; // Fallo, no es necesario seguir comparando
                }
            }
        }

        // 3. Reaccionar al resultado de la comparación
        if ($esCorrectoEstaRonda) {
            echo "<p style='color: green;'>¡Correcto! Preparando la siguiente secuencia...</p>";
            // Si la secuencia fue correcta, el juego avanza.
            $this->avanzarRonda();
        } else {
            echo "<p style='color: red;'>Incorrecto. ¡Pierdes una vida!</p>";
            $this->jugador->restaVidas();
            $this->sesion->set("vidas", $this->jugador->getVidas()); // Guarda las vidas actualizadas
            // Si el jugador ha perdido todas las vidas
            if ($this->jugador->vidas0()) {
                echo "<p style='color: red;'>¡GAME OVER! Has perdido todas tus vidas.</p>";
                $this->sesion->clearAll(); // Limpia la sesión para reiniciar el juego
                // Puedes añadir una redirección aquí: header("Location: index.php?status=lose"); exit();
            } else {
                // Si aún tiene vidas, se le muestra la misma secuencia de nuevo para que la adivine correctamente
                // (no se avanza de ronda, sigue en el mismo nivel)
            }
        }
    }

    // Este método revela un nuevo color para la ronda y actualiza la secuencia a adivinar
    private function avanzarRonda()
    {
        // Asegurarse de no exceder la longitud de la secuencia maestra
        if ($this->rondaActualIndice < $this->cantidadColoresTotal) {
            // Añade el próximo color de la secuencia maestra a la secuencia de la ronda actual
            array_push($this->secuenciaActualRonda, $this->secuenciaMaestra[$this->rondaActualIndice]);
            $this->sesion->set("secuenciaActualRonda", $this->secuenciaActualRonda);

            $this->rondaActualIndice++; // Incrementa el índice para la próxima vez
            $this->sesion->set("rondaActualIndice", $this->rondaActualIndice);
        } else {
            // Todas las rondas han sido completadas con éxito
            echo "<p style='color: blue;'>¡FELICIDADES! Has completado el juego.</p>";
            $this->sesion->clearAll(); // Borrar sesión al ganar
            // Puedes añadir una redirección aquí: header("Location: index.php?status=win"); exit();
        }
    }

    // Método para mostrar el estado actual del juego al usuario
    public function mostrarEstadoJuego()
    {
        // Muestra la secuencia que el usuario debe recordar y adivinar
        echo "<h2>Memoriza la secuencia:</h2>";
        echo "<div style='font-size: 2em; margin: 20px 0; padding: 10px; border: 2px solid #007bff; display: inline-block;'>";
        foreach ($this->secuenciaActualRonda as $color) {
            echo "<span style='padding: 5px; margin: 2px; border: 1px solid black; background-color: lightgray;'>$color</span> ";
        }
        echo "</div>";
        echo "<p>Vidas restantes: " . $this->jugador->getVidas() . "</p>";
        echo "<p>Ronda: " . count($this->secuenciaActualRonda) . " / " . $this->cantidadColoresTotal . "</p>";

        // DEBUG: Muestra la secuencia maestra completa (QUITAR EN PRODUCCIÓN)
        // echo "<p style='color: gray; font-size: 0.8em;'>DEBUG: Secuencia Maestra: " . implode(', ', $this->secuenciaMaestra) . "</p>";
    }

    // Verifica si el jugador ha perdido
    public function haPerdido(): bool
    {
        return $this->jugador->vidas0();
    }

    // Método para obtener la secuencia maestra (útil para mostrar al perder)
    public function getSecuenciaMaestra(): array
    {
        return $this->secuenciaMaestra;
    }
}

<?php
// Carga de clases
// La función `cargaClase` debe ser revisada. Debería ser `include($clase . '.php');`
// o `include($clase . '.class.php');` dependiendo de cómo nombres tus archivos.
// Si tus clases están en archivos con el mismo nombre que la clase, sin prefijo `index.`,
// entonces `spl_autoload_register(function($clase) { require_once $clase . '.php'; });` es lo común.
spl_autoload_register(function ($clase) {
    if (file_exists($clase . '.php')) {
        require_once $clase . '.php';
    }
});

session_start(); // Siempre inicia la sesión

$cookieManager = new CookieManager();

// Si las cookies existen, significa que el usuario ya inició sesión y el juego puede comenzar
if ($cookieManager->exists('usuario') && $cookieManager->exists('cantidad')) {
    $usuario = htmlspecialchars($cookieManager->get('usuario'));
    $cantidad = htmlspecialchars((string) $cookieManager->getInt('cantidad')); // Asegura que cantidad sea int

    $manejo = new manejoJuego((int)$cantidad); // Instancia la clase de manejo del juego

    // --- Lógica de procesamiento para el formulario de secuencia ---
    // Este bloque solo se ejecuta cuando el formulario de secuencia es enviado (POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['texto'])) {
        if (!$manejo->haPerdido()) { // Solo procesa si el jugador no ha perdido aún
            $manejo->procesarInputUsuario($_POST['texto']);
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