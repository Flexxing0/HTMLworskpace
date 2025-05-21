
var descripcionTarea = [];
var tarFinalizadas = [];

function setCookie(nombre, valor, dias) {
    const fecha = new Date();
    fecha.setTime(fecha.getTime() + (dias * 24 * 60 * 60 * 1000));
    const cookieStr = `${nombre}=${encodeURIComponent(valor)};expires=${fecha.toUTCString()};path=/`;
    console.log("Set cookie:", cookieStr);
    document.cookie = cookieStr;
}

function getCookie(nombre) {
    const cookies = document.cookie.split("; ");
    for (let cookie of cookies) {
        const [key, value] = cookie.split("=");
        if (key === nombre) {
            console.log(value)
            return decodeURIComponent(value);
        }
    }
    return null;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function enviarSesion() {
    var usuario = $id("idusuario").value.trim();
    var contrasena = $id("idcontrasena").value.trim();
    if (getCookie(usuario) != null) {
        mensajeSaludo(getCookie("usuario"), parseInt.getCookie("visitas"), getCookie("ultimoAcceso"));
        ocultaElemento("inicio-sesion");
    } else {
        const ahora = new Date().toLocaleString();
        setCookie("usuario", usuario, 30);
        setCookie("contrasena", contrasena, 30);
        setCookie("visitas", 1, 30);
        setCookie("ultimoAcceso", ahora, 30);
        mensajeSaludo(getCookie("usuario"), parseInt(getCookie("visitas")), getCookie("ultimoAcceso"));
        ocultaElemento("inicio-sesion");
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function $id(id) {//obtener el elemento por id
    let el = document.getElementById(id);
    return el;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function creaBotonContinuar(){
    var btnViejo = document.getElementById("btn-continuar");
    if(btnViejo){
        padre = btnViejo.parentNode;
        padre.removeChild(btnViejo);
    }
    var btnnuevo = document.createElement("button");
    btnnuevo.setAttribute("id","btn-continuar");
    btnnuevo.setAttribute("name","continuar");
    btnnuevo.setAttribute("value","continua");
    btnnuevo.setAttribute("onClick","botonContinua();");
    btnnuevo.innerHTML = "Continuar";
    return btnnuevo;
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function botonContinua() {
    ocultaElemento("mensaje-bienvenida");
    muestraElemento("area-ingreso-tareas");
    muestraElemento("tareas-pendiente");
    muestraElemento("tarea-finalizadas");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function mensajeSaludo(usuario, visitas, ultimoAcceso) {
    let ahora = new Date().toLocaleString();
    var contenedor = $id("mensaje-bienvenida");
    var parrafoViejo = document.getElementById("parrafo");
    if (parrafoViejo) {
        padre = parrafoViejo.parentNode;
        padre.removeChild(parrafoViejo);
    }
    var parrafoNuevo = document.createElement("h3");
    parrafoNuevo.setAttribute("id", "parrafo");
    if (visitas > 1) {
        parrafoNuevo.textContent = "Bienvenido " + usuario + " es su visita " + visitas + " ultimo acceso: " + ultimoAcceso;
        setCookie("usuario", usuario, 30);
        setCookie("visitas", visitas + 1, 30);
        setCookie("ultimoAcceso", ahora, 30);
    } else {
        parrafoNuevo.textContent = "Bienvenido " + usuario + " es su primera visita";
        setCookie("usuario", usuario, 30);
        setCookie("visitas", visitas + 1, 30);
        setCookie("ultimoAcceso", ahora, 30);
    }
    contenedor.appendChild(parrafoNuevo);
    contenedor.appendChild(creaBotonContinuar());
    muestraElemento("mensaje-bienvenida");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function ocultaElemento(elemento){
    var e = $id(elemento);
    e.classList.add("oculto");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function muestraElemento(elemento){
    var e = $id(elemento);
    e.classList.remove("oculto");
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function agregarTarea() {
    let tarea = $id("iddescripcion");
    if(tarea.value ==  ""){
        alert("No puso una descripcion");
        return;
    }
    if(existeTarea(tarea.value)){
        return;
    }else{
        descripcionTarea.push(tarea.value);
        
        
    }
    tareasPendientes();
}

function existeTarea(texto){
    for(i=0;i<descripcionTarea.length;i++){
        if(descripcionTarea[i] === texto){
            alert("Ya existe la tarea");
            return true;
        }else{return false;}
    }
}


function creaBotonPend(posicion){
        var botonConfirma = document.createElement("button");
    botonConfirma.setAttribute("id",posicion);
    botonConfirma.setAttribute("class","confirma");
    botonConfirma.addEventListener('click',()=>{
        botonConfirma.textContent="X";
    })
    return botonConfirma;
}

function buscaTarea(texto){
    for(i=0;i<descripcionTarea.length;i++){
        if(descripcionTarea[i]===texto){
            let pos = i;
            return pos;
        }
    }
}

function tareasPendientes(){
    var tareas = $id("pendientes");
    var tareasViejas = document.getElementsByClassName("tarea-pendiente");
    if(tareasViejas.length>0){
        for(i= tareasViejas.length-1;i>=0;i--){
            padre = tareasViejas[i].parentNode;
            padre.removeChild(tareasViejas[i]);
        }
    }
    var btnViejas = document.getElementsByClassName("confirma");
    if(btnViejas.length>0){
        for(i= btnViejas.length-1;i>=0;i--){
            padre = btnViejas[i].parentNode;
            padre.removeChild(btnViejas[i]);
        }
    }
    descripcionTarea.forEach(element =>{
    var tarea = document.createElement("p");
    tarea.setAttribute("class","tarea-pendiente");
    tarea.textContent = element;
    tareas.appendChild(creaBotonPend(buscaTarea(element)));
    tareas.appendChild(tarea); 
    });
}

function finalizarTarea(){
 var botones = document.getElementsByClassName("confirma");
 if(botones.length>0){
    console.log("entre");
    for(i=0;i<botones.length;i++){
        if(botones[i].textContent==="X"){
            tarFinalizadas.push(descripcionTarea.splice(i,i));
            sacaTarea(descripcionTarea[parseInt(botones[i].id)]);
        }
    }
 }
 tareasPendientes();
 tareasFinalizadas();
 muestraElemento("tareass-finalizadas");
}

function sacaTarea(texto){
    var textos = [];
    for(i=0;i<descripcionTarea.length;i++){
        if(descripcionTarea[i] === texto){
            textos.push(descripcionTarea[i]);
        }
    }
}

function tareasFinalizadas(){
    var tareas = $id("tarea-finalizadas");
    var tareasViejas = document.getElementsByClassName("tarea-fin");
    if(tareasViejas.length>0){
        for(i= tareasViejas.length-1;i>=0;i--){
            padre = tareasViejas[i].parentNode;
            padre.removeChild(tareasViejas[i]);
        }
    }
    tarFinalizadas.forEach(element =>{
    var tarea = document.createElement("p");
    tarea.setAttribute("class","tarea-fin");
    tarea.textContent = element;
    tareas.appendChild(tarea); 
    });
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function limpiarTareas() {
    remuevePendientes();
    remueveFinalizadas();
}

function remuevePendientes(){
      var tareasViejas = document.getElementsByClassName("tarea-pendiente");
    if(tareasViejas.length>0){
        for(i= tareasViejas.length-1;i>=0;i--){
            padre = tareasViejas[i].parentNode;
            padre.removeChild(tareasViejas[i]);
        }
    }
}
function remueveFinalizadas(){
   var tareasViejas = document.getElementsByClassName("tarea-fin");
    if(tareasViejas.length>0){
        for(i= tareasViejas.length-1;i>=0;i--){
            padre = tareasViejas[i].parentNode;
            padre.removeChild(tareasViejas[i]);
        }
    }
}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////