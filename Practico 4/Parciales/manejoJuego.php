<?php
require_once 'jugador.php';
require_once 'arrayUtils.php';
require_once 'sessionManager.php';
require_once 'cookieManager.php';
class manejoJuego
{
    private const COLORES = ['R', 'Y', 'V', 'A'];//R: rojo, Y: amarillo, V: verde, A: azul
    private array $secuenciaMaestra; //este se usa para generar la secuencia completa, ira llenado a $secuencia a medida que avance el jugador
    private array $secuenciaActualRonda;//tendra lo que ingrese el usuario
    private SessionManager $sesion;
    private Jugador $jugador;
    private CookieManager $CookieManager;
    private int $cantidadColoresTotal;
    private int $rondaActualndice;

    function __construct(int $cantidadColoresTotal)
    {
        $this->sesion = new SessionManager();
        $this->cantidadColoresTotal = $cantidadColoresTotal;
        
        // Recuperar vidas o inicializar si es nueva partida
        $vidas_guardadas = $this->sesion->get("vidas",3);
        $this->jugador = new Jugador($vidas_guardadas);

        if(!$this->sesion->exists("secuenciaMaestra")){
            // Es nueva partida o la sesion se ha perdido
            $this->secuenciaActualRonda = [];
            $this->secuenciaMaestra = [];
            $this->rondaActualndice = 0;//Se enpieza mostrando el primer color
            $this->generaSecuenciaAleatoria();//Genera la secuencia maestra

            //Guarda el estado inicial del juego en sesion
            $this->sesion->set("secuenciaMaestra", $this->secuenciaMaestra);
            $this->sesion->set("secuenciaActualRonda", $this->secuenciaActualRonda); // Inicialmente vacío
            $this->sesion->set("rondaActualndice", $this->rondaActualndice);
            $this->sesion->set("vidas", $this->jugador->getVidas());

            // En la primera carga solo se muestra el primer color para la ronda 0
            $this->avanzarRonda();

        }else{
            // Cargar estado existente de la sesion
            $this->secuenciaMaestra = $this->sesion->get("secuenciaMaestra");
            $this->secuenciaActualRonda = $this->sesion->get("secuenciaActualRonda");
            $this->rondaActualndice = $this->sesion->get("rondaActualndice");
            // Las vidas ya se cargaron en el constructor de Jugador
        }
    }

    // Genera la secuencia maestra completa al inicio del juego
    private function generaSecuenciaAleatoria()
    {
        for($i=0;$i<$this->cantidadColoresTotal;$i++){
            $valor = ArrayUtils::randomValue(self::COLORES);
            array_push($this->secuenciaMaestra,$valor);
        }
        $this->sesion->set("secuenciaMaestra",$this->secuenciaMaestra);
    }

    public function analizaInputUsuario(string $input_usuario)
    {
        $texto_limpio = str_replace([' ','-'],'',$input_usuario);
        $array_texto_limpio = str_split($texto_limpio);

        $esCorrectoRonda = true;
        // 1. Verificar si coinciden longitudes
        if (count($this->secuenciaActualRonda) !== count($array_texto_limpio)){
            $esCorrectoRonda = false;
            echo "<p style='color: red;'>ERROR: La longitud de tu secuencia no coincide con la esperada (ingresaste " . count($array_texto_limpio) . " colores, se esperaban " . count($this->secuenciaActualRonda) . ").</p>";
        } else {

            // 2. Comparar cada elemento de la secuencia ingresada con la esperada
            for($i=0; $i< count($this->secuenciaActualRonda); $i++){
                if ($this->secuenciaActualRonda[$i] !== $array_texto_limpio[$i]) {
                   $esCorrectoRonda = false;
                   break; // Fallo, se rompe el loop para no seguir esperando
                }
            }
        }   

        if ($esCorrectoRonda){
            echo "<p style='color: green;'>¡Correcto! Preparando la siguiente secuencia...</p>";
            // Si la secuencia fue correcta el juego avanza
            $this->avanzarRonda();
        } else {
            $this->jugador->restaVidas();
            $this->sesion->set("vidas",$this->jugador->getVidas());
            // Guarda las vidas actualizadas

            if($this->jugador->vidas0()) {
                echo "<p style='color: red;'>¡GAME OVER! Has perdido todas tus vidas.</p>";
                $this->sesion->clearAll(); // Limpia la sesión para reiniciar el juego
                header("Location: index.php?status=lose");
                exit();
            } else {
                //$this->mostrarEstadoJuego();
            }
        }

    }

    private function avanzarRonda()
    {

        if($this->rondaActualndice < $this->cantidadColoresTotal) {
            // Anade el proximo color de la secuencia maestra a la secuencia de la ronda actual
            array_push($this->secuenciaActualRonda, $this->secuenciaMaestra[$this->rondaActualndice]);
            $this->sesion->set("secuenciaActualRonda", $this->secuenciaActualRonda);

            $this->rondaActualndice++;
            $this->sesion->set("rondaActualndice", $this->rondaActualndice);
        } else {
            echo "<p style='color: green;'>¡Has ganado la partida!</p>";
            $this->sesion->clearAll();
            
            header("Location: borrar_cookie.php");
            exit;
        }
    }

    public function mostrarEstadoJuego()
    {
        echo "<p> Color Actual: ".print_r($this->secuenciaActualRonda)." </p>";
        echo "<p> Vidas: ".$this->jugador->getVidas()." </p>";
        echo "<p> Colores restantes: ".$this->cantidadColoresTotal - $this->rondaActualndice." </p>";
        echo "<p>" .implode(', ',$this->secuenciaMaestra). "</p>";
    }

    public function haPerdido()
    {
        return $this->jugador->vidas0();
    }

    public function getSecuenciaMaestra()
    {
        return $this->secuenciaMaestra;
    }


}
?>
