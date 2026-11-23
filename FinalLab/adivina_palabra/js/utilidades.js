/* Archivo: js/utilidades.js
 * Funciones auxiliares compartidas por todas las paginas del cliente.
 */

function ocultaElemento(idElemento)
{
    var e = document.getElementById(idElemento);
    if (!e)
    {
        return;
    }
    if (!e.classList.contains("oculto"))
    {
        e.classList.add("oculto");
    }
}

function muestraElemento(idElemento)
{
    var e = document.getElementById(idElemento);
    if (!e)
    {
        return;
    }
    if (e.classList.contains("oculto"))
    {
        e.classList.remove("oculto");
    }
}

// Escapa caracteres especiales HTML antes de insertarlos en el DOM (proteccion XSS)
function escaparHTML(texto)
{
    if (texto === null || texto === undefined)
    {
        return "";
    }
    var cadena = String(texto);
    return cadena
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#39;");
}

// Formatea segundos a MM:SS para la pantalla de juego
function formatearTiempo(segundos)
{
    var total = parseInt(segundos, 10);
    if (isNaN(total) || total < 0)
    {
        total = 0;
    }
    var minutos = Math.floor(total / 60);
    var segs = total % 60;
    var minTexto = (minutos < 10 ? "0" : "") + minutos;
    var segTexto = (segs < 10 ? "0" : "") + segs;
    return minTexto + ":" + segTexto;
}

// Validacion del formulario de inicio de sesion (index.php)
function validarLogin()
{
    const txtUsuario = document.getElementById("txtUsuario");
    const txtPassword = document.getElementById("txtPassword");
    const divError = document.getElementById("errorClienteLogin");

    var usuario = txtUsuario.value.trim();
    var password = txtPassword.value;

    if (usuario === "" || password === "")
    {
        divError.textContent = "Completa tu nombre de usuario y contraseña para ingresar.";
        muestraElemento("errorClienteLogin");
        if (usuario === "")
        {
            txtUsuario.focus();
        }
        else
        {
            txtPassword.focus();
        }
        return false;
    }

    ocultaElemento("errorClienteLogin");
    return true;
}
