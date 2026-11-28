var temporizadorId = null;
var tiempoLimiteSeg = null;
var tiempoTranscurridoSeg = 0;
var partidaFinalizada = false;
var peticionEnCurso = false;

var ALFABETO = [
    "a", "b", "c", "d", "e", "f", "g", "h", "i",
    "j", "k", "l", "m", "n", "ñ", "o", "p", "q",
    "r", "s", "t", "u", "v", "w", "x", "y", "z"
];

function inicializarPartida() {
    construirTecladoVirtual();
    cargarEstadoPartida();
}

function construirTecladoVirtual() {
    const contenedor = document.getElementById("tecladoVirtual");
    if (!contenedor) {
        return;
    }
    contenedor.innerHTML = "";

    for (var i = 0; i < ALFABETO.length; i++) {
        var letra = ALFABETO[i];
        var boton = document.createElement("button");
        boton.type = "button";
        boton.className = "tecla-letra";
        boton.id = "tecla_" + letra;
        boton.textContent = letra.toUpperCase();
        boton.setAttribute("data-letra", letra);
        boton.setAttribute("onClick", "arriesgarDesdeTecla('" + letra + "');");
        contenedor.appendChild(boton);
    }
}

function cargarEstadoPartida() {
    var peticion = new XMLHttpRequest();
    peticion.open("POST", "../controladores/estadoPartida.php", true);
    peticion.onreadystatechange = procesarEstadoInicial;
    peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    peticion.send(null);

    function procesarEstadoInicial() {
        if ((peticion.readyState == 4) && (peticion.status == 200)) {
            var objeto = JSON.parse(peticion.responseText);

            if (objeto.sesionExpirada) {
                window.location.href = "../index.php";
                return;
            }

            if (objeto.sinPartida) {
                window.location.href = "menuView.php";
                return;
            }

            if (objeto.ok && objeto.partida) {
                actualizarInterfazPartida(objeto.partida);
                if (objeto.partida.finalizada) {
                    mostrarResultadoFinal(objeto.partida, objeto.mensaje, objeto.guardadaBD);
                }
                else {
                    iniciarTemporizadorVisual(objeto.partida);
                    enfocarIngresoLetra();
                }
            }
            else {
                mostrarMensajeMovimiento(objeto.mensaje || "No se pudo cargar la partida.", "alerta-error");
            }
        }
    }
}

function arriesgarDesdeFormulario() {
    const txtLetra = document.getElementById("txtLetra");
    if (!txtLetra) {
        return false;
    }

    var letra = txtLetra.value.trim().toLowerCase();
    txtLetra.value = "";

    if (!/^[a-zñ]$/.test(letra)) {
        mostrarMensajeMovimiento("Ingresa una unica letra valida (A-Z o Ñ, sin tilde).", "alerta-error");
        enfocarIngresoLetra();
        return false;
    }

    enviarLetraArriesgada(letra);
    return false;
}

function arriesgarDesdeTecla(letra) {
    if (partidaFinalizada || peticionEnCurso) {
        return;
    }
    enviarLetraArriesgada(letra);
}

function enviarLetraArriesgada(letra) {
    if (partidaFinalizada || peticionEnCurso) {
        return;
    }

    peticionEnCurso = true;
    cambiarEstadoControles(true);

    var parametros = "letra=" + encodeURIComponent(letra);
    var peticion = new XMLHttpRequest();
    peticion.open("POST", "../controladores/arriesgarLetra.php", true);
    peticion.onreadystatechange = procesarRespuestaLetra;
    peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    peticion.send(parametros);

    function procesarRespuestaLetra() {
        if ((peticion.readyState == 4) && (peticion.status == 200)) {
            peticionEnCurso = false;
            var objeto = JSON.parse(peticion.responseText);

            if (objeto.sesionExpirada) {
                detenerTemporizador();
                window.location.href = "../index.php";
                return;
            }

            if (objeto.sinPartida) {
                detenerTemporizador();
                window.location.href = "menuView.php";
                return;
            }

            if (objeto.partida) {
                actualizarInterfazPartida(objeto.partida);
            }

            var claseAlerta = "alerta-info";
            if (!objeto.ok) {
                claseAlerta = "alerta-error";
            }
            else if (objeto.accion === "acierto") {
                claseAlerta = "alerta-exito";
            }
            else if (objeto.accion === "pista" || objeto.accion === "repetida" || objeto.accion === "ya_descubierta") {
                claseAlerta = "alerta-aviso";
            }
            else if (objeto.accion === "tiempo_agotado") {
                claseAlerta = "alerta-error";
            }

            mostrarMensajeMovimiento(objeto.mensaje, claseAlerta);

            if (objeto.partida && objeto.partida.finalizada) {
                mostrarResultadoFinal(objeto.partida, objeto.mensaje, objeto.guardadaBD);
            }
            else {
                cambiarEstadoControles(false);
                enfocarIngresoLetra();
            }
        }
        else if (peticion.readyState == 4) {
            peticionEnCurso = false;
            cambiarEstadoControles(false);
            mostrarMensajeMovimiento("Error de comunicacion con el servidor (HTTP " + peticion.status + ").", "alerta-error");
        }
    }
}

function abandonarPartida() {
    if (partidaFinalizada || peticionEnCurso) {
        return;
    }

    var confirma = window.confirm("¿Seguro que queres abandonar la partida? Se considerara perdida y no acumulara puntos.");
    if (!confirma) {
        return;
    }

    peticionEnCurso = true;
    cambiarEstadoControles(true);
    detenerTemporizador();

    var peticion = new XMLHttpRequest();
    peticion.open("POST", "../controladores/abandonarPartida.php", true);
    peticion.onreadystatechange = procesarAbandono;
    peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    peticion.send("confirmar=1");

    function procesarAbandono() {
        if ((peticion.readyState == 4) && (peticion.status == 200)) {
            peticionEnCurso = false;
            var objeto = JSON.parse(peticion.responseText);

            if (objeto.sesionExpirada) {
                window.location.href = "../index.php";
                return;
            }

            if (objeto.ok && objeto.partida) {
                actualizarInterfazPartida(objeto.partida);
                mostrarMensajeMovimiento(objeto.mensaje, "alerta-error");
                mostrarResultadoFinal(objeto.partida, objeto.mensaje, objeto.guardadaBD);
            }
            else {
                mostrarMensajeMovimiento(objeto.mensaje || "No se pudo abandonar la partida.", "alerta-error");
                cambiarEstadoControles(false);
            }
        }
        else if (peticion.readyState == 4) {
            peticionEnCurso = false;
            cambiarEstadoControles(false);
            mostrarMensajeMovimiento("Error de comunicacion con el servidor (HTTP " + peticion.status + ").", "alerta-error");
        }
    }
}

function iniciarTemporizadorVisual(partida) {
    detenerTemporizador();

    tiempoLimiteSeg = partida.tiempoLimiteSeg;
    tiempoTranscurridoSeg = partida.tiempoTranscurridoSeg;

    actualizarVisualizacionTiempo();

    if (tiempoLimiteSeg === null) {
        ocultaElemento("contenedorBarraTiempo");
        return;
    }

    muestraElemento("contenedorBarraTiempo");

    temporizadorId = window.setInterval(function () {
        if (partidaFinalizada) {
            detenerTemporizador();
            return;
        }

        tiempoTranscurridoSeg++;
        if (tiempoTranscurridoSeg > tiempoLimiteSeg) {
            tiempoTranscurridoSeg = tiempoLimiteSeg;
        }

        actualizarVisualizacionTiempo();

        if (tiempoTranscurridoSeg >= tiempoLimiteSeg && !peticionEnCurso) {
            notificarTiempoAgotadoServidor();
        }
    }, 1000);
}

function detenerTemporizador() {
    if (temporizadorId !== null) {
        window.clearInterval(temporizadorId);
        temporizadorId = null;
    }
}

function actualizarVisualizacionTiempo() {
    const spanTranscurrido = document.getElementById("infoTiempoTranscurrido");
    const spanRestante = document.getElementById("infoTiempoRestante");
    const barra = document.getElementById("barraTiempo");

    if (tiempoLimiteSeg === null) {
        if (spanTranscurrido) {
            spanTranscurrido.textContent = "Sin limite";
        }
        if (spanRestante) {
            spanRestante.textContent = "Sin limite";
        }
        return;
    }

    var restante = tiempoLimiteSeg - tiempoTranscurridoSeg;
    if (restante < 0) {
        restante = 0;
    }

    if (spanTranscurrido) {
        spanTranscurrido.textContent = formatearTiempo(tiempoTranscurridoSeg) + " / " + formatearTiempo(tiempoLimiteSeg);
    }
    if (spanRestante) {
        spanRestante.textContent = formatearTiempo(restante);
    }
    if (barra) {
        barra.max = tiempoLimiteSeg;
        barra.value = tiempoTranscurridoSeg;
    }
}

function notificarTiempoAgotadoServidor() {
    if (partidaFinalizada || peticionEnCurso) {
        return;
    }

    peticionEnCurso = true;
    cambiarEstadoControles(true);

    var peticion = new XMLHttpRequest();
    peticion.open("POST", "../controladores/finalizarPorTiempo.php", true);
    peticion.onreadystatechange = procesarFinPorTiempo;
    peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    peticion.send("finTiempo=1");

    function procesarFinPorTiempo() {
        if ((peticion.readyState == 4) && (peticion.status == 200)) {
            peticionEnCurso = false;
            var objeto = JSON.parse(peticion.responseText);

            if (objeto.sesionExpirada) {
                detenerTemporizador();
                window.location.href = "../index.php";
                return;
            }

            if (objeto.ok && objeto.partida) {
                actualizarInterfazPartida(objeto.partida);

                if (objeto.partida.finalizada) {
                    detenerTemporizador();
                    mostrarMensajeMovimiento(objeto.mensaje, "alerta-error");
                    mostrarResultadoFinal(objeto.partida, objeto.mensaje, objeto.guardadaBD);
                }
                else {
                    tiempoTranscurridoSeg = objeto.partida.tiempoTranscurridoSeg;
                    actualizarVisualizacionTiempo();
                    cambiarEstadoControles(false);
                }
            }
            else {
                cambiarEstadoControles(false);
            }
        }
        else if (peticion.readyState == 4) {
            peticionEnCurso = false;
            cambiarEstadoControles(false);
        }
    }
}

function actualizarInterfazPartida(partida) {
    document.getElementById("infoCategoria").textContent = partida.categoria;
    document.getElementById("infoDificultad").textContent =
        capitalizarTexto(partida.nivel) + " (x" + partida.multiplicador + ")";
    document.getElementById("infoLongitud").textContent = partida.longitud + " letras";
    document.getElementById("infoIntentos").textContent = partida.intentos;
    document.getElementById("infoAdivinadas").textContent = partida.letrasAcertadas;
    document.getElementById("infoPistas").textContent = partida.pistas;

    tiempoLimiteSeg = partida.tiempoLimiteSeg;
    tiempoTranscurridoSeg = partida.tiempoTranscurridoSeg;
    actualizarVisualizacionTiempo();

    const contenedorMascara = document.getElementById("contenedorMascara");
    var htmlMascara = "";
    for (var i = 0; i < partida.mascara.length; i++) {
        var caracter = partida.mascara[i];
        var claseCasilla = (caracter === "*") ? "casilla-letra oculta" : "casilla-letra descubierta";
        htmlMascara += "<span class=\"" + claseCasilla + "\">" + escaparHTML(caracter) + "</span>";
    }
    contenedorMascara.innerHTML = htmlMascara;

    const contenedorArriesgadas = document.getElementById("listaLetrasArriesgadas");
    if (partida.letrasArriesgadas.length === 0) {
        contenedorArriesgadas.innerHTML = "<span class=\"texto-ayuda\">Ninguna todavia.</span>";
    }
    else {
        var htmlLetras = "";
        for (var j = 0; j < partida.letrasArriesgadas.length; j++) {
            htmlLetras += "<span class=\"etiqueta-letra\">" + escaparHTML(partida.letrasArriesgadas[j]) + "</span>";
        }
        contenedorArriesgadas.innerHTML = htmlLetras;
    }

    for (var k = 0; k < ALFABETO.length; k++) {
        var letra = ALFABETO[k];
        var tecla = document.getElementById("tecla_" + letra);
        if (!tecla) {
            continue;
        }
        var yaUsada = (partida.letrasArriesgadas.indexOf(letra) !== -1) ||
            (partida.mascara.indexOf(letra) !== -1);
        if (yaUsada || partida.finalizada) {
            tecla.disabled = true;
            if (!tecla.classList.contains("usada")) {
                tecla.classList.add("usada");
            }
        }
        else {
            tecla.disabled = false;
            tecla.classList.remove("usada");
        }
    }
}

function mostrarResultadoFinal(partida, mensajeAdicional, guardadaBD) {
    partidaFinalizada = true;
    detenerTemporizador();
    cambiarEstadoControles(true);
    ocultaElemento("panelControlesJuego");

    const titulo = document.getElementById("tituloResultadoFinal");
    const detalle = document.getElementById("detalleResultadoFinal");
    const palabraEl = document.getElementById("palabraReveladaFinal");
    const puntajeEl = document.getElementById("puntajeResultadoFinal");
    const avisoPersistencia = document.getElementById("avisoPersistenciaFinal");

    var textoResultado = "";
    if (partida.resultado === "ganada") {
        textoResultado = "¡Ganaste la partida!";
    }
    else if (partida.resultado === "empate") {
        textoResultado = "La partida termino en empate.";
    }
    else if (partida.resultado === "abandonada") {
        textoResultado = "Partida abandonada (se considera perdida).";
    }
    else {
        textoResultado = "Perdiste la partida.";
    }

    var motivoTexto = "";
    if (partida.motivoFin === "tiempo") {
        motivoTexto = "Se agoto el tiempo limite. ";
    }
    else if (partida.motivoFin === "completa") {
        motivoTexto = "Se completaron todas las letras de la palabra. ";
    }
    else if (partida.motivoFin === "abandono") {
        motivoTexto = "Decidiste abandonar la partida en curso. ";
    }

    titulo.textContent = textoResultado;
    detalle.textContent = motivoTexto +
        "Letras adivinadas: " + partida.letrasAcertadas +
        " | Pistas mostradas: " + partida.pistas +
        " | Intentos: " + partida.intentos + ".";

    palabraEl.textContent = partida.palabraRevelada ? partida.palabraRevelada.toUpperCase() : "";

    if (partida.resultado === "ganada" || partida.resultado === "empate") {
        puntajeEl.textContent = "Obtuviste " + partida.puntajePartida +
            " punto(s) en la partida y acumulaste " + partida.puntajeSumado +
            " punto(s) en la base de datos (dificultad " + partida.nivel + " x" + partida.multiplicador + ").";
    }
    else {
        puntajeEl.textContent = "Obtuviste " + partida.puntajePartida +
            " acierto(s), pero al no ganar ni empatar acumulas 0 puntos en la base de datos.";
    }

    if (avisoPersistencia) {
        if (guardadaBD === false) {
            avisoPersistencia.className = "alerta alerta-error";
            avisoPersistencia.textContent = "Atencion: no se pudo confirmar el guardado de la partida en la base de datos.";
        }
        else {
            avisoPersistencia.className = "alerta alerta-exito";
            avisoPersistencia.textContent = "Los datos de la partida y tu puntaje acumulado fueron guardados en la base de datos.";
        }
        muestraElemento("avisoPersistenciaFinal");
    }

    muestraElemento("panelResultadoFinal");

    cargarTop10Ajax();
}

function cargarTop10Ajax() {
    const estadoCarga = document.getElementById("estadoCargaTop10");
    const cuerpo = document.getElementById("cuerpoTop10Partida");
    const resumen = document.getElementById("resumenPosicionTop10");

    muestraElemento("panelTop10Partida");
    if (estadoCarga) {
        estadoCarga.className = "alerta alerta-info";
        estadoCarga.textContent = "Cargando tabla de posiciones...";
        muestraElemento("estadoCargaTop10");
    }
    if (cuerpo) {
        cuerpo.innerHTML = "";
    }

    var peticion = new XMLHttpRequest();
    peticion.open("POST", "../controladores/obtenerTop10.php", true);
    peticion.onreadystatechange = procesarTop10;
    peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    peticion.send(null);

    function procesarTop10() {
        if ((peticion.readyState == 4) && (peticion.status == 200)) {
            var objeto = JSON.parse(peticion.responseText);

            if (objeto.sesionExpirada) {
                window.location.href = "../index.php";
                return;
            }

            if (objeto.ok && objeto.top10) {
                ocultaElemento("estadoCargaTop10");

                const puntajeNav = document.getElementById("puntajeNav");
                if (puntajeNav && typeof objeto.puntajeAcumuladoActual === "number") {
                    puntajeNav.textContent = objeto.puntajeAcumuladoActual;
                }

                if (resumen) {
                    if (objeto.estaEnTop10 && objeto.puestoUsuarioActual !== null) {
                        resumen.textContent = "Tu puntaje acumulado actual es " +
                            objeto.puntajeAcumuladoActual + " punto(s) (puesto #" +
                            objeto.puestoUsuarioActual + " en el Top 10).";
                    }
                    else {
                        resumen.textContent = "Tu puntaje acumulado actual es " +
                            objeto.puntajeAcumuladoActual + " punto(s) (actualmente fuera del Top 10).";
                    }
                }

                if (cuerpo) {
                    if (objeto.top10.length === 0) {
                        cuerpo.innerHTML = "<tr><td colspan=\"3\">Todavia no hay jugadores registrados.</td></tr>";
                    }
                    else {
                        var htmlFilas = "";
                        for (var i = 0; i < objeto.top10.length; i++) {
                            var fila = objeto.top10[i];
                            var claseFila = fila.esUsuarioActual ? " class=\"fila-destacada\"" : "";
                            var sufijo = fila.esUsuarioActual ? " (Vos)" : "";

                            htmlFilas += "<tr" + claseFila + ">";
                            htmlFilas += "<td>#" + fila.puesto + "</td>";
                            htmlFilas += "<td>@" + escaparHTML(fila.nombreUsuario) + sufijo + "</td>";
                            htmlFilas += "<td>" + fila.puntajeAcumulado + " pts</td>";
                            htmlFilas += "</tr>";
                        }
                        cuerpo.innerHTML = htmlFilas;
                    }
                }
            }
            else if (estadoCarga) {
                estadoCarga.className = "alerta alerta-error";
                estadoCarga.textContent = objeto.mensaje || "No se pudo cargar el Top 10.";
                muestraElemento("estadoCargaTop10");
            }
        }
        else if (peticion.readyState == 4) {
            if (estadoCarga) {
                estadoCarga.className = "alerta alerta-error";
                estadoCarga.textContent = "Error al obtener el Top 10 (HTTP " + peticion.status + ").";
                muestraElemento("estadoCargaTop10");
            }
        }
    }
}

function mostrarMensajeMovimiento(texto, claseAlerta) {
    const divMsg = document.getElementById("mensajeMovimiento");
    if (!divMsg) {
        return;
    }
    divMsg.className = "alerta " + claseAlerta;
    divMsg.textContent = texto;
    muestraElemento("mensajeMovimiento");
}

function cambiarEstadoControles(deshabilitar) {
    const txtLetra = document.getElementById("txtLetra");
    const btnArriesgar = document.getElementById("btnArriesgar");
    const btnAbandonar = document.getElementById("btnAbandonar");

    if (txtLetra) {
        txtLetra.disabled = deshabilitar;
    }
    if (btnArriesgar) {
        btnArriesgar.disabled = deshabilitar;
    }
    if (btnAbandonar) {
        btnAbandonar.disabled = deshabilitar;
    }
}

function enfocarIngresoLetra() {
    const txtLetra = document.getElementById("txtLetra");
    if (txtLetra && !txtLetra.disabled) {
        txtLetra.focus();
    }
}

function capitalizarTexto(texto) {
    if (!texto || texto.length === 0) {
        return "";
    }
    return texto.charAt(0).toUpperCase() + texto.slice(1);
}
