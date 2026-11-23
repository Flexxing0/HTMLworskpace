/* Archivo: js/admin.js
 * Validaciones del lado del cliente y confirmaciones de baja logica para el
 * panel de administracion (admin/categorias.php y admin/palabras.php).
 */

// Valida alta y modificacion de categorias (admin/categorias.php)
function validarCategoria()
{
    const txtNombre = document.getElementById("txtNombreCategoria");
    const divError = document.getElementById("errorClienteCategoria");

    var nombre = txtNombre.value.trim();

    if (nombre.length < 2 || nombre.length > 50)
    {
        divError.textContent = "El nombre de la categoria debe tener entre 2 y 50 caracteres.";
        muestraElemento("errorClienteCategoria");
        txtNombre.focus();
        return false;
    }

    ocultaElemento("errorClienteCategoria");
    return true;
}

// Confirmacion antes de ejecutar baja logica de una categoria (A-15)
function confirmarBajaCategoria(formulario)
{
    var nombre = formulario.getAttribute("data-nombre") || "";
    return window.confirm(
        "¿Seguro que queres dar de baja logica la categoria \"" + nombre + "\"? " +
        "No se eliminara fisicamente de la base de datos y podra reactivarse si volves a crearla con el mismo nombre."
    );
}

// Valida alta y modificacion de palabras (admin/palabras.php, A-06 y A-07)
function validarPalabra()
{
    const cmbCategoria = document.getElementById("cmbCategoriaPalabra");
    const txtPalabra = document.getElementById("txtPalabra");
    const divError = document.getElementById("errorClientePalabra");

    var idCategoria = parseInt(cmbCategoria.value, 10);
    var palabra = txtPalabra.value.trim().toLowerCase();

    if (isNaN(idCategoria) || idCategoria <= 0)
    {
        divError.textContent = "Selecciona una categoria activa antes de guardar la palabra.";
        muestraElemento("errorClientePalabra");
        cmbCategoria.focus();
        return false;
    }

    if (palabra === "")
    {
        divError.textContent = "Ingresa la palabra que queres guardar.";
        muestraElemento("errorClientePalabra");
        txtPalabra.focus();
        return false;
    }

    if (!/^[a-zñ]+$/.test(palabra))
    {
        divError.textContent = "La palabra solo puede contener letras de la A a la Z y Ñ, sin espacios ni tildes.";
        muestraElemento("errorClientePalabra");
        txtPalabra.focus();
        return false;
    }

    if (palabra.length < 3)
    {
        divError.textContent = "La palabra debe tener al menos 3 letras.";
        muestraElemento("errorClientePalabra");
        txtPalabra.focus();
        return false;
    }

    if (palabra.length > 50)
    {
        divError.textContent = "La palabra no puede superar las 50 letras.";
        muestraElemento("errorClientePalabra");
        txtPalabra.focus();
        return false;
    }

    ocultaElemento("errorClientePalabra");
    return true;
}

// Muestra en tiempo real la cantidad de letras y dificultad resultante (A-06)
function actualizarVistaDificultadPalabra()
{
    const txtPalabra = document.getElementById("txtPalabra");
    const spanVista = document.getElementById("vistaDificultadPalabra");
    if (!txtPalabra || !spanVista)
    {
        return;
    }

    var palabra = txtPalabra.value.trim().toLowerCase();
    if (palabra === "")
    {
        spanVista.textContent = "Solo letras A-Z y Ñ (sin espacios ni tildes), entre 3 y 50 letras. Se guarda en minuscula.";
        return;
    }

    if (!/^[a-zñ]+$/.test(palabra))
    {
        spanVista.textContent = "Caracter no permitido: usa unicamente letras A-Z y Ñ (sin tildes ni espacios).";
        return;
    }

    var largo = palabra.length;
    if (largo < 3)
    {
        spanVista.textContent = largo + " letra(s): se requieren al menos 3 letras.";
        return;
    }

    var nivel = "Alta (x3)";
    if (largo <= 5)
    {
        nivel = "Baja (x1)";
    }
    else if (largo <= 8)
    {
        nivel = "Media (x2)";
    }

    spanVista.textContent = largo + " letras - Dificultad asignada automaticamente: " + nivel + ".";
}

// Confirmacion antes de ejecutar baja logica de una palabra (A-15)
function confirmarBajaPalabra(formulario)
{
    var palabra = formulario.getAttribute("data-palabra") || "";
    return window.confirm(
        "¿Seguro que queres dar de baja logica la palabra \"" + palabra + "\"? " +
        "Dejara de sortearse en nuevas partidas pero se conservara en el historial de la base de datos."
    );
}
