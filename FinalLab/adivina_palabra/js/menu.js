
function cargarPreferenciasMenu() {
    if (typeof localStorage === "undefined") {
        return;
    }

    var ultimoNivel = localStorage.getItem("adivina_ultimo_nivel");
    if (ultimoNivel) {
        var radiosNivel = document.getElementsByName("rdnDificultad");
        for (var i = 0; i < radiosNivel.length; i++) {
            if (radiosNivel[i].value === ultimoNivel) {
                radiosNivel[i].checked = true;
            }
        }
    }

    var ultimoTiempo = localStorage.getItem("adivina_ultimo_tiempo");
    if (ultimoTiempo !== null) {
        var radiosTiempo = document.getElementsByName("rdnTiempo");
        for (var j = 0; j < radiosTiempo.length; j++) {
            if (radiosTiempo[j].value === ultimoTiempo) {
                radiosTiempo[j].checked = true;
            }
        }
    }
}

function guardarPreferenciasMenu() {
    if (typeof localStorage === "undefined") {
        return;
    }

    var radiosNivel = document.getElementsByName("rdnDificultad");
    for (var i = 0; i < radiosNivel.length; i++) {
        if (radiosNivel[i].checked) {
            localStorage.setItem("adivina_ultimo_nivel", radiosNivel[i].value);
        }
    }

    var radiosTiempo = document.getElementsByName("rdnTiempo");
    for (var j = 0; j < radiosTiempo.length; j++) {
        if (radiosTiempo[j].checked) {
            localStorage.setItem("adivina_ultimo_tiempo", radiosTiempo[j].value);
        }
    }
}

function validarNuevaPartida() {
    const cmbCategoria = document.getElementById("cmbCategoria");
    const divError = document.getElementById("errorClienteMenu");

    if (!cmbCategoria || cmbCategoria.value === "" || cmbCategoria.value === "0") {
        divError.textContent = "Debes seleccionar una categoria para iniciar la partida.";
        muestraElemento("errorClienteMenu");
        if (cmbCategoria) {
            cmbCategoria.focus();
        }
        return false;
    }

    var radiosNivel = document.getElementsByName("rdnDificultad");
    var nivelElegido = false;
    for (var i = 0; i < radiosNivel.length; i++) {
        if (radiosNivel[i].checked) {
            nivelElegido = true;
            break;
        }
    }

    if (!nivelElegido) {
        divError.textContent = "Debes seleccionar un nivel de dificultad.";
        muestraElemento("errorClienteMenu");
        return false;
    }

    ocultaElemento("errorClienteMenu");
    guardarPreferenciasMenu();
    return true;
}

function confirmarAbandonoDesdeMenu() {
    return window.confirm("Si abandonas la partida en curso, se registrara como perdida y no sumara puntos. ¿Deseas continuar?");
}
