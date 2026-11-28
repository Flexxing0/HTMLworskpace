/* Archivo: js/registro.js
 * Validacion en cliente del formulario de registro y verificacion asincronica
 * de disponibilidad del nombre de usuario mediante XMLHttpRequest (Unidad 6).
 */

var usuarioDisponibleAjax = null;
var ultimoUsuarioVerificado = "";

function verificarUsuarioDisponible()
{
    const txtUsuario = document.getElementById("txtUsuario");
    const spanEstado = document.getElementById("estadoUsuarioAjax");
    var nombre = txtUsuario.value.trim();

    if (nombre === "")
    {
        spanEstado.textContent = "";
        spanEstado.className = "estado-ajax";
        usuarioDisponibleAjax = null;
        ultimoUsuarioVerificado = "";
        return;
    }

    if (nombre.length < 3 || nombre.length > 30)
    {
        spanEstado.textContent = "Debe tener entre 3 y 30 caracteres.";
        spanEstado.className = "estado-ajax estado-ocupado";
        usuarioDisponibleAjax = false;
        ultimoUsuarioVerificado = nombre;
        return;
    }

    if (!/^[a-zA-Z0-9_]+$/.test(nombre))
    {
        spanEstado.textContent = "Solo letras sin tilde, numeros y guion bajo (_).";
        spanEstado.className = "estado-ajax estado-ocupado";
        usuarioDisponibleAjax = false;
        ultimoUsuarioVerificado = nombre;
        return;
    }

    if (nombre.toLowerCase() === "administrador")
    {
        spanEstado.textContent = "El nombre 'administrador' esta reservado.";
        spanEstado.className = "estado-ajax estado-ocupado";
        usuarioDisponibleAjax = false;
        ultimoUsuarioVerificado = nombre;
        return;
    }

    spanEstado.textContent = "Verificando disponibilidad...";
    spanEstado.className = "estado-ajax estado-verificando";
    usuarioDisponibleAjax = null;
    ultimoUsuarioVerificado = nombre;

    var parametros = "nombreUsuario=" + encodeURIComponent(nombre);
    var peticion = new XMLHttpRequest();
    peticion.open("POST", "../controladores/verificarUsuario.php", true);
    peticion.onreadystatechange = procesarVerificacion;
    peticion.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    peticion.send(parametros);

    function procesarVerificacion()
    {
        if ((peticion.readyState == 4) && (peticion.status == 200))
        {
            var objeto = JSON.parse(peticion.responseText);

            // Ignora respuestas viejas si el usuario siguio editando el campo
            if (txtUsuario.value.trim() !== nombre)
            {
                return;
            }

            if (objeto.ok && objeto.disponible)
            {
                spanEstado.textContent = objeto.mensaje;
                spanEstado.className = "estado-ajax estado-disponible";
                usuarioDisponibleAjax = true;
                ultimoUsuarioVerificado = nombre;
            }
            else
            {
                spanEstado.textContent = objeto.mensaje;
                spanEstado.className = "estado-ajax estado-ocupado";
                usuarioDisponibleAjax = false;
                ultimoUsuarioVerificado = nombre;
            }
        }
        else if (peticion.readyState == 4)
        {
            spanEstado.textContent = "No se pudo verificar disponibilidad (se validara al enviar).";
            spanEstado.className = "estado-ajax estado-verificando";
            usuarioDisponibleAjax = null;
        }
    }
}

function validarRegistro()
{
    const txtUsuario = document.getElementById("txtUsuario");
    const txtCorreo = document.getElementById("txtCorreo");
    const txtPassword = document.getElementById("txtPassword");
    const txtPassword2 = document.getElementById("txtPassword2");
    const divError = document.getElementById("errorClienteRegistro");

    var usuario = txtUsuario.value.trim();
    var correo = txtCorreo.value.trim();
    var password = txtPassword.value;
    var password2 = txtPassword2.value;
    var mensajeError = "";

    if (usuario.length < 3 || usuario.length > 30)
    {
        mensajeError = "El nombre de usuario debe tener entre 3 y 30 caracteres.";
        txtUsuario.focus();
    }
    else if (!/^[a-zA-Z0-9_]+$/.test(usuario))
    {
        mensajeError = "El nombre de usuario solo permite letras sin tilde, numeros y guion bajo (_).";
        txtUsuario.focus();
    }
    else if (usuario.toLowerCase() === "administrador")
    {
        mensajeError = "El nombre de usuario 'administrador' esta reservado.";
        txtUsuario.focus();
    }
    else if (usuarioDisponibleAjax === false && ultimoUsuarioVerificado === usuario)
    {
        mensajeError = "Ese nombre de usuario no esta disponible. Elegi otro.";
        txtUsuario.focus();
    }
    else if (correo === "" || correo.indexOf("@") === -1 || correo.indexOf(".") === -1)
    {
        mensajeError = "Ingresa una direccion de correo electronico valida.";
        txtCorreo.focus();
    }
    else if (password.length < 6 || password.length > 64)
    {
        mensajeError = "La contraseña debe tener entre 6 y 64 caracteres.";
        txtPassword.focus();
    }
    else if (password !== password2)
    {
        mensajeError = "Las contraseñas ingresadas no coinciden.";
        txtPassword2.focus();
    }

    if (mensajeError !== "")
    {
        divError.textContent = mensajeError;
        muestraElemento("errorClienteRegistro");
        return false;
    }

    ocultaElemento("errorClienteRegistro");
    return true;
}
