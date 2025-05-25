//document.addEventListener('DOMContentLoaded', () => {
const usuarioLogeado = 'usuarioLogeado' ; 
let usuarioActual = null ; 

    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Lax";
    }

    function getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    function eraseCookie(name) {
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Lax;';
    }

    function getUserData(username) {
        const cookieValue = getCookie(username);
        return cookieValue ? JSON.parse(cookieValue) : null;
    }

    function saveUserData(username, data) {
        setCookie(username, JSON.stringify(data), 30); // Guardar por 30 días
    }

    function getId(id){
        return document.getElementById(id);
    }

    function ocultaElemento(elemento){
        var e = getId(elemento);
        if(e.classList.contains("oculto")){
            return;
        }else{
        e.classList.add("oculto");
        }
    }

    function muestraElemento(elemento){
        var e = getId(elemento);
        if(e.classList.contains("oculto")){
            e.classList.remove("oculto");
        }
    }

    function enviarSesion(){
        const datos = getId('idusuario');
        const usuario = datos.value;
        datosUsuario=getUserData(usuario);
        if(datosUsuario){
            //se usa para mantener la sesion iniciada al refrescar la pagina, se hace la consistencia despues
            usuarioActual={...datosUsuario};
            usuarioActual.visitas++;
            usuarioActual.ultimoAcceso=new Date().toLocaleString('es-ES');
            saveUserData(usuarioActual.usuario,usuarioActual);
            
            console.log("primer if");
        }else{
            usuarioActual = {
                usuario:usuario,
                visitas: 1,
                ultimoAcceso: new Date().toLocaleString('es-ES'),
                tareasPendientes: [],
                tareasFinalizadas: []
            };
            saveUserData(usuario,usuarioActual);
            //se usa para mantener la sesion iniciada al refrescar la pagina, se hace la consistencia despues
            console.log("ps if");
        }
        setCookie(usuarioLogeado,usuario,1);
        mensajeBienvenida();
        muestraMensajeBienvenida();
        inicializaWeb();
    }

    function mensajeBienvenida(){
        const contenedor = getId("mensaje-bienvenida");
        var cuerpo ='';
        if(usuarioActual.visitas===1){
            cuerpo+= "Es su primera visita " + usuarioActual.usuario;
        }else{
            cuerpo+= "Bienvenido devuelta " + usuarioActual.usuario + "<br>";
            cuerpo+= "Es su visita numero " + usuarioActual.visitas + "<br>";
            cuerpo+= "Fecha de ultimo acceso: " + usuarioActual.ultimoAcceso;
        }
        var btnContinua = '<button type="button" id="btnContinua" onClick="botonContinuar();">Continuar</button>';
        contenedor.innerHTML= cuerpo + btnContinua;
        
    }

    function botonContinuar(){
        muestraTareas();
    }

    function actualizaPendientes(){
        var texto='';
        for(i=0;i<usuarioActual.tareasPendientes.length;i++){
            texto+= '<tr><td><input type="checkbox" id="btnCheck'+i+'" class="btn-checks" data-indice="'+i+'"></td><td> <label for="btnCheck'+i+'">';
            texto += usuarioActual.tareasPendientes[i] + '</label></td></tr>';
        }
        document.querySelector("#tareas-p").innerHTML = texto;
    }

    function actualizaFinalizadas(){
        var texto='';
        for(i=0;i<usuarioActual.tareasFinalizadas.length;i++){
            texto+= '<tr><td>'+ usuarioActual.tareasFinalizadas[i]+'</td></tr>';
        }
        document.querySelector("#tareas-f").innerHTML = texto;
    }

    function agregarTarea(){
        const inp = document.getElementById("iddescripcion");
        usuarioActual.tareasPendientes.push(inp.value);
        saveUserData(usuarioActual.usuario,usuarioActual);
        console.log("se guardo exitosamente");
        actualizaPendientes();
    }

    function finalizarTareas(){
        var indicesEliminar = [];
        var checks = document.getElementsByClassName('btn-checks');//hacer lo de abajo si son htmlcollection
        Array.from(checks).forEach(element => {//Recolectar los índices de las tareas marcadas (si son checkboxes)
            if(element.checked){
                var indice = element.getAttribute("data-indice");
                indicesEliminar.push(parseInt(indice,10));
            }
        });//Ordenar los índices en orden descendente para evitar problemas con splice
        indicesEliminar.sort((a,b)=>b-a);
        indicesEliminar.forEach(indice=>{
            if(indice<usuarioActual.tareasPendientes.length){
                let elementosEliminados = usuarioActual.tareasPendientes.splice(indice,1);
                if(elementosEliminados.length>0){
                    usuarioActual.tareasFinalizadas.push(elementosEliminados[0]);
                } else {
            console.warn("Índice inválido o tarea no encontrada al mover: " + indice);
        }
            }
        });
        saveUserData(usuarioActual.usuario,usuarioActual);
        console.log("se guardo exitosamente");
        actualizaPendientes();
        actualizaFinalizadas();
    }

    function muestraMensajeBienvenida(){
        ocultaElemento("inicio-sesion");
        ocultaElemento("tareas-pendiente");
        ocultaElemento("tareas-finalizadas");
        ocultaElemento("area-ingreso-tareas");
        muestraElemento("mensaje-bienvenida");
    }

    function muestraTareas(){
        ocultaElemento("mensaje-bienvenida");
        ocultaElemento("inicio-sesion");
        muestraElemento("area-ingreso-tareas");
        muestraElemento("tareas-finalizadas");
        muestraElemento("tareas-pendiente");
    }

    function muestraInicioSesion(){
        ocultaElemento("mensaje-bienvenida");
        ocultaElemento("tareas-pendiente");
        ocultaElemento("tareas-finalizadas");
        muestraElemento("inicio-sesion");
    }

    function inicializaWeb(){
        //const 
        const usuarioLog = getCookie(usuarioLogeado);
        if(usuarioLog){
            const datosUsuario = getUserData(usuarioLog);
            if (datosUsuario){
                usuarioActual = {...datosUsuario};
               actualizaFinalizadas();
                actualizaPendientes(); 
            }else{

            }
            
        }else{
            muestraInicioSesion();
        }
    }

    

//});