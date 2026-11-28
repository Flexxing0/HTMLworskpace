<?php

require_once __DIR__ . '/Palabra.php';

class Partida
{
    private $idUsuario;
    private $idPalabra;
    private $palabraOculta;
    private $letrasPalabra;
    private $categoriaNombre;
    private $nivel;
    private $longitud;
    private $tiempoLimiteSeg;
    private $fechaInicioTs;
    private $fechaInicioStr;
    private $fechaFinStr;
    private $mascara;
    private $intentos;
    private $letrasAcertadas;
    private $pistas;
    private $letrasArriesgadas;
    private $finalizada;
    private $guardadaEnBD;
    private $motivoFin;
    private $resultado;
    private $puntajePartida;
    private $puntajeSumado;

    public function __construct()
    {
        $this->idUsuario = 0;
        $this->idPalabra = 0;
        $this->palabraOculta = '';
        $this->letrasPalabra = array();
        $this->categoriaNombre = '';
        $this->nivel = 'baja';
        $this->longitud = 0;
        $this->tiempoLimiteSeg = null;
        $this->fechaInicioTs = 0;
        $this->fechaInicioStr = '';
        $this->fechaFinStr = '';
        $this->mascara = array();
        $this->intentos = 0;
        $this->letrasAcertadas = 0;
        $this->pistas = 0;
        $this->letrasArriesgadas = array();
        $this->finalizada = false;
        $this->guardadaEnBD = false;
        $this->motivoFin = '';
        $this->resultado = '';
        $this->puntajePartida = 0;
        $this->puntajeSumado = 0;
    }

    public function getIdUsuario()
    {
        return $this->idUsuario;
    }

    public function getIdPalabra()
    {
        return $this->idPalabra;
    }

    public function getNivel()
    {
        return $this->nivel;
    }

    public function getLongitud()
    {
        return $this->longitud;
    }

    public function getTiempoLimiteSeg()
    {
        return $this->tiempoLimiteSeg;
    }

    public function getFechaInicioStr()
    {
        return $this->fechaInicioStr;
    }

    public function getFechaFinStr()
    {
        return $this->fechaFinStr;
    }

    public function getIntentos()
    {
        return $this->intentos;
    }

    public function getLetrasAcertadas()
    {
        return $this->letrasAcertadas;
    }

    public function getPistas()
    {
        return $this->pistas;
    }

    public function getLetrasArriesgadas()
    {
        return $this->letrasArriesgadas;
    }

    public function estaFinalizada()
    {
        return $this->finalizada;
    }

    public function estaGuardadaEnBD()
    {
        return $this->guardadaEnBD;
    }

    public function marcarGuardadaEnBD()
    {
        $this->guardadaEnBD = true;
    }

    public function getResultado()
    {
        return $this->resultado;
    }

    public function getMotivoFin()
    {
        return $this->motivoFin;
    }

    public function getPuntajePartida()
    {
        return $this->puntajePartida;
    }

    public function getPuntajeSumado()
    {
        return $this->puntajeSumado;
    }

    public function getMascaraTexto()
    {
        return implode(' ', $this->mascara);
    }

    public function iniciarNueva($idUsuario, $objPalabra, $nivel, $tiempoLimiteSeg)
    {
        $this->idUsuario = (int) $idUsuario;
        $this->idPalabra = $objPalabra->getId();
        $this->palabraOculta = Palabra::normalizar($objPalabra->getPalabra());
        $this->letrasPalabra = preg_split('//u', $this->palabraOculta, -1, PREG_SPLIT_NO_EMPTY);
        $this->categoriaNombre = $objPalabra->getNombreCategoria();
        $this->nivel = $nivel;
        $this->longitud = count($this->letrasPalabra);
        $this->tiempoLimiteSeg = ($tiempoLimiteSeg === null || (int) $tiempoLimiteSeg <= 0)
            ? null
            : (int) $tiempoLimiteSeg;
        $this->fechaInicioTs = time();
        $this->fechaInicioStr = date('Y-m-d H:i:s', $this->fechaInicioTs);
        $this->fechaFinStr = '';
        $this->mascara = array_fill(0, $this->longitud, '*');
        $this->intentos = 0;
        $this->letrasAcertadas = 0;
        $this->pistas = 0;
        $this->letrasArriesgadas = array();
        $this->finalizada = false;
        $this->guardadaEnBD = false;
        $this->motivoFin = '';
        $this->resultado = '';
        $this->puntajePartida = 0;
        $this->puntajeSumado = 0;
    }

    public function getTiempoTranscurridoSeg()
    {
        if ($this->fechaInicioTs <= 0)
        {
            return 0;
        }

        if ($this->finalizada && $this->fechaFinStr !== '')
        {
            $finTs = strtotime($this->fechaFinStr);
            $transcurrido = max(0, $finTs - $this->fechaInicioTs);
        }
        else
        {
            $transcurrido = max(0, time() - $this->fechaInicioTs);
        }

        if ($this->tiempoLimiteSeg !== null && $transcurrido > $this->tiempoLimiteSeg)
        {
            return $this->tiempoLimiteSeg;
        }

        return $transcurrido;
    }

    public function estaTiempoVencido()
    {
        if ($this->tiempoLimiteSeg === null || $this->finalizada)
        {
            return false;
        }
        return (time() - $this->fechaInicioTs) >= $this->tiempoLimiteSeg;
    }

    public function arriesgarLetra($letraRaw)
    {
        if ($this->finalizada)
        {
            return array(
                'ok' => false,
                'accion' => 'finalizada',
                'letraPista' => null,
                'mensaje' => 'La partida ya finalizo.'
            );
        }

        if ($this->estaTiempoVencido())
        {
            $this->finalizarPorTiempo();
            return array(
                'ok' => true,
                'accion' => 'tiempo_agotado',
                'letraPista' => null,
                'mensaje' => 'Se agoto el tiempo de la partida.'
            );
        }

        $letra = mb_strtolower(trim($letraRaw), 'UTF-8');
        if (!preg_match('/^[a-zñ]$/u', $letra))
        {
            return array(
                'ok' => false,
                'accion' => 'invalida',
                'letraPista' => null,
                'mensaje' => 'Ingresa una unica letra valida (A-Z o Ñ, sin tilde).'
            );
        }

        if (in_array($letra, $this->letrasArriesgadas, true))
        {
            return array(
                'ok' => true,
                'accion' => 'repetida',
                'letraPista' => null,
                'mensaje' => 'Ya arriesgaste la letra "' . mb_strtoupper($letra, 'UTF-8') . '". Proba con otra.'
            );
        }

        if (in_array($letra, $this->mascara, true))
        {
            return array(
                'ok' => true,
                'accion' => 'ya_descubierta',
                'letraPista' => null,
                'mensaje' => 'La letra "' . mb_strtoupper($letra, 'UTF-8') . '" ya esta descubierta por una pista. Proba con otra.'
            );
        }

        $this->intentos++;
        $this->letrasArriesgadas[] = $letra;

        $encontrada = false;
        for ($i = 0; $i < $this->longitud; $i++)
        {
            if ($this->letrasPalabra[$i] === $letra)
            {
                $this->mascara[$i] = $letra;
                $encontrada = true;
            }
        }

        if ($encontrada)
        {

            $this->letrasAcertadas++;

            if ($this->estaPalabraCompleta())
            {
                $this->evaluarFinalizacion('completa');
            }

            return array(
                'ok' => true,
                'accion' => 'acierto',
                'letraPista' => null,
                'mensaje' => 'Acierto! La letra "' . mb_strtoupper($letra, 'UTF-8') . '" esta en la palabra.'
            );
        }

        $letraPista = $this->aplicarPista();

        if ($this->estaPalabraCompleta())
        {
            $this->evaluarFinalizacion('completa');
        }

        return array(
            'ok' => true,
            'accion' => 'pista',
            'letraPista' => $letraPista,
            'mensaje' => 'La letra "' . mb_strtoupper($letra, 'UTF-8') . '" no esta en la palabra. Pista: "' . mb_strtoupper((string) $letraPista, 'UTF-8') . '".'
        );
    }

    private function aplicarPista()
    {
        $letraPista = null;
        for ($i = 0; $i < $this->longitud; $i++)
        {
            if ($this->mascara[$i] === '*')
            {
                $letraPista = $this->letrasPalabra[$i];
                break;
            }
        }

        if ($letraPista !== null)
        {
            for ($i = 0; $i < $this->longitud; $i++)
            {
                if ($this->letrasPalabra[$i] === $letraPista)
                {
                    $this->mascara[$i] = $letraPista;
                }
            }
            $this->pistas++;
        }

        return $letraPista;
    }

    private function estaPalabraCompleta()
    {
        return !in_array('*', $this->mascara, true);
    }

    public function abandonar()
    {
        if (!$this->finalizada)
        {
            $this->evaluarFinalizacion('abandono');
        }
    }

    public function finalizarPorTiempo()
    {
        if (!$this->finalizada)
        {
            $this->evaluarFinalizacion('tiempo');
        }
    }

    private function evaluarFinalizacion($motivo)
    {
        $this->finalizada = true;
        $this->motivoFin = $motivo;
        $this->fechaFinStr = date('Y-m-d H:i:s');
        $this->puntajePartida = $this->letrasAcertadas;

        if ($motivo === 'abandono')
        {
            $this->resultado = 'abandonada';
            $this->puntajeSumado = 0;
            return;
        }

        if ($this->letrasAcertadas > $this->pistas)
        {
            $this->resultado = 'ganada';
        }
        else if ($this->letrasAcertadas === $this->pistas)
        {
            $this->resultado = 'empate';
        }
        else
        {
            $this->resultado = 'perdida';
        }

        if ($this->resultado === 'ganada' || $this->resultado === 'empate')
        {
            $multiplicador = self::getMultiplicadorPorNivel($this->nivel);
            $this->puntajeSumado = $this->puntajePartida * $multiplicador;
        }
        else
        {
            $this->puntajeSumado = 0;
        }
    }

    public static function getMultiplicadorPorNivel($nivel)
    {
        switch ($nivel)
        {
            case 'media':
                return 2;
            case 'alta':
                return 3;
            default:
                return 1;
        }
    }

    public function getEstadoPublico()
    {
        $transcurrido = $this->getTiempoTranscurridoSeg();
        $restante = ($this->tiempoLimiteSeg !== null)
            ? max(0, $this->tiempoLimiteSeg - $transcurrido)
            : null;

        $estado = array(
            'categoria' => $this->categoriaNombre,
            'nivel' => $this->nivel,
            'multiplicador' => self::getMultiplicadorPorNivel($this->nivel),
            'longitud' => $this->longitud,
            'mascara' => $this->mascara,
            'mascaraTexto' => $this->getMascaraTexto(),
            'intentos' => $this->intentos,
            'letrasAcertadas' => $this->letrasAcertadas,
            'pistas' => $this->pistas,
            'letrasArriesgadas' => $this->letrasArriesgadas,
            'tiempoLimiteSeg' => $this->tiempoLimiteSeg,
            'tiempoTranscurridoSeg' => $transcurrido,
            'tiempoRestanteSeg' => $restante,
            'finalizada' => $this->finalizada
        );

        if ($this->finalizada)
        {
            $estado['resultado'] = $this->resultado;
            $estado['motivoFin'] = $this->motivoFin;
            $estado['puntajePartida'] = $this->puntajePartida;
            $estado['puntajeSumado'] = $this->puntajeSumado;
            $estado['palabraRevelada'] = $this->palabraOculta;
        }

        return $estado;
    }

    public function guardarEnSession()
    {
        $_SESSION['partida_activa'] = array(
            'idUsuario' => $this->idUsuario,
            'idPalabra' => $this->idPalabra,
            'palabraOculta' => $this->palabraOculta,
            'categoriaNombre' => $this->categoriaNombre,
            'nivel' => $this->nivel,
            'longitud' => $this->longitud,
            'tiempoLimiteSeg' => $this->tiempoLimiteSeg,
            'fechaInicioTs' => $this->fechaInicioTs,
            'fechaInicioStr' => $this->fechaInicioStr,
            'fechaFinStr' => $this->fechaFinStr,
            'mascara' => $this->mascara,
            'intentos' => $this->intentos,
            'letrasAcertadas' => $this->letrasAcertadas,
            'pistas' => $this->pistas,
            'letrasArriesgadas' => $this->letrasArriesgadas,
            'finalizada' => $this->finalizada,
            'guardadaEnBD' => $this->guardadaEnBD,
            'motivoFin' => $this->motivoFin,
            'resultado' => $this->resultado,
            'puntajePartida' => $this->puntajePartida,
            'puntajeSumado' => $this->puntajeSumado
        );
    }

    public static function cargarDesdeSession()
    {
        if (!isset($_SESSION['partida_activa']) || !is_array($_SESSION['partida_activa']))
        {
            return null;
        }

        $datos = $_SESSION['partida_activa'];
        $partida = new Partida();
        $partida->idUsuario = (int) $datos['idUsuario'];
        $partida->idPalabra = (int) $datos['idPalabra'];
        $partida->palabraOculta = $datos['palabraOculta'];
        $partida->letrasPalabra = preg_split('//u', $partida->palabraOculta, -1, PREG_SPLIT_NO_EMPTY);
        $partida->categoriaNombre = $datos['categoriaNombre'];
        $partida->nivel = $datos['nivel'];
        $partida->longitud = (int) $datos['longitud'];
        $partida->tiempoLimiteSeg = $datos['tiempoLimiteSeg'];
        $partida->fechaInicioTs = (int) $datos['fechaInicioTs'];
        $partida->fechaInicioStr = $datos['fechaInicioStr'];
        $partida->fechaFinStr = $datos['fechaFinStr'];
        $partida->mascara = $datos['mascara'];
        $partida->intentos = (int) $datos['intentos'];
        $partida->letrasAcertadas = (int) $datos['letrasAcertadas'];
        $partida->pistas = (int) $datos['pistas'];
        $partida->letrasArriesgadas = $datos['letrasArriesgadas'];
        $partida->finalizada = (bool) $datos['finalizada'];
        $partida->guardadaEnBD = (bool) $datos['guardadaEnBD'];
        $partida->motivoFin = $datos['motivoFin'];
        $partida->resultado = $datos['resultado'];
        $partida->puntajePartida = (int) $datos['puntajePartida'];
        $partida->puntajeSumado = (int) $datos['puntajeSumado'];

        return $partida;
    }

    public static function limpiarSession()
    {
        unset($_SESSION['partida_activa']);
    }
}
?>
