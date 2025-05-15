const longitud7 = ["gaviota", "informe", "revista", "esquema"];
const longitud8 = ["ambiente", "historia", "personas"];
const longitud10 = ["calendario", "transporte", "desarrollo"];
var letrasadivinadas = 0; //contador letras adivinadas
var letraspistas = 0; //contador pistas obtenidas
var pistasmostradas = 0;
var palabraGenerada , longitudgenerada; //longitud y palabras generadas
var palabraVacia = []; //se usa para ir llenando de a poco
var palabraPista = []; //se usa para ir eliminando de a poco e ir colocando en palabra vacia
var jugador = "jugador1";
var continua = true;


//////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////genera palabra/////////////////////////////////////////////////////////////////////////////
function obtieneLong() {//obtiene long del combobox
    var longi = document.getElementById("idlongitud");
    longitudgenerada = longi.value;
}

function generaPalabraVacia(longitud) {//genera la palabra vacia dinamicamente
    for (let i = 0; i < longitud; i++) {
        palabraVacia.push('_');
    }
    console.log(palabraVacia);
}

function generaPalabra() { //genera la palabra segun la longitud seleccionada, USAR ESTE
    obtieneLong();
    longitudgenerada = Number(longitudgenerada);
    switch (longitudgenerada) {
        case 7: palabraGenerada = longitud7[Math.floor(Math.random() * longitud7.length)].split('');
                generaPalabraVacia(7);
            break;
        case 8: palabraGenerada = longitud8[Math.floor(Math.random() * longitud8.length)].split('');
            generaPalabraVacia(8);
            break;
        case 10: palabraGenerada = longitud10[Math.floor(Math.random() * longitud10.length)].split('');
            generaPalabraVacia(10);
            break;
        default:
            break;
    }
}

function llenaPalabraPista(){
    console.log(longitud7[0].split('').length);
    console.log(palabraGenerada);
    var letra;
    for(i=0;i<palabraGenerada.length;i++){
        letra = palabraGenerada[i];
        palabraPista.push(letra);
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
function botonComenzar(){
    generaPalabra();
    llenaPalabraPista();
    creaContenedorPalabra();
    creaCajaTextoFinal();
    actualizaPistasYLetras();
    if(document.getElementById("contenedor-palabra-vacia").classList.contains("oculto")){
        muestraElemento("contenedor-palabra-vacia");
        muestraElemento("contenedor-letras-ingresadas");
        muestraElemento("mensaje-decision");

    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////creacion casilla adivina palabra///////////////////////////////////////////////////////////////////////////////
function creaCasillaPalabra(palabra){// crea la casilla donde se iran colocando las letras
    var palabraAntigua = document.getElementById("palabra-colocada");
    if (palabraAntigua){
        padre = palabraAntigua.parentNode;
        padre.removeChild(palabraAntigua);
    }
    var palabraActualizada = document.createElement("h3");
    palabraActualizada.setAttribute("id", "palabra-colocada");
    palabraActualizada.innerHTML = palabra;
    return palabraActualizada;
}

function creaContenedorPalabra(){// crea el contenedor donde se colocara la casilla de la palabra, USAR ESTE
    var contenedor = document.getElementById("contenedor-palabra-vacia");
    contenedor.appendChild(creaCasillaPalabra(palabraVacia.join('')));
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////Seccion creacion div////////////////////////////////////////////////////////////////////////
function creaBotonGeneraPista(){
    var botonViejo = document.getElementById("generapista");
    if(botonViejo){
        padre = botonViejo.parentNode;
        padre.removeChild(botonViejo);
    }
    var nuevoBoton = document.createElement("button");
    nuevoBoton.setAttribute("id","generapista");
    nuevoBoton.setAttribute("value","Genera");
    nuevoBoton.setAttribute("name","boton");
    nuevoBoton.setAttribute("onclick","generaPista();");
    nuevoBoton.innerHTML = "Pista";
    return nuevoBoton;
}

function creaPistaGanadasTexto(){
    var pista = document.getElementById("contador-pista");
    if (pista){
        padre = pista.parentNode;
        padre.removeChild(pista);
    }
    var pistanueva = document.createElement("p");
    pistanueva.setAttribute("id","contador-pista");
    pistanueva.setAttribute("name","pista_text");
    pistanueva.setAttribute("value","pist");
    pistanueva.innerHTML = "Pistas Disponibles: " + letraspistas;
    return pistanueva;
}

function creaLetrasAdivinadasTexto(){
    var letraVieja = document.getElementById("contador-letras");
    if (letraVieja){
        padre = letraVieja.parentNode;
        padre.removeChild(letraVieja);
    }
    var letranueva = document.createElement("p");
    letranueva.setAttribute("id","contador-letras");
    letranueva.setAttribute("name","letras_text");
    letranueva.setAttribute("value","letr");
    letranueva.innerHTML = "Letras adivinadas: " + letrasadivinadas;
    return letranueva;
}

function creaCajaTexto(){
    var caja = document.getElementById("caja");
    ocultaElemento("parte-principal");
    if (caja){
        padre = caja.parentNode;
        padre.removeChild(caja);
    }
    var cajanueva = document.createElement("input");
    cajanueva.setAttribute("type","text");
    cajanueva.setAttribute("id","caja");
    cajanueva.setAttribute("name","caja_text");
    cajanueva.setAttribute("Maxlength","1");
    cajanueva.setAttribute("size","30");
    return cajanueva;
}

function creaBoton(){
    var botonviejo = document.getElementById("boton-enviar");
    if(botonviejo){
        padre = botonviejo.parentNode;
        padre.removeChild(botonviejo);
    }
    var botonnuevo = document.createElement("button");
    botonnuevo.setAttribute("type","button");
    botonnuevo.setAttribute("id","boton-enviar");
    botonnuevo.setAttribute("value","Enviar");
    botonnuevo.setAttribute("name","enviado");
    botonnuevo.setAttribute("onClick","escuchaIngreso();");
    botonnuevo.innerHTML = "Enviar";
    return botonnuevo;
}

function creaCajaTextoFinal(){
    var contenedor = document.getElementById("contenedor-letras-ingresadas");
    contenedor.appendChild(creaCajaTexto());
    contenedor.appendChild(creaBoton());
}

function actualizaPistasYLetras(){//contenedor donde se ingresan las letras y cuentan letras y pistas, USAR ESTE
    var contenedor = document.getElementById("contenedor-letras-ingresadas");
    contenedor.appendChild(creaPistaGanadasTexto());
    contenedor.appendChild(creaLetrasAdivinadasTexto());
    contenedor.appendChild(creaBotonGeneraPista());
    contenedor.appendChild(creaBotonABNDoVolver("abandonar","botonAbandonar","Abandonar"));
    contenedor.appendChild(creaBotonABNDoVolver("volver","botonVolverJugar","Volver a jugar"));
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
function escuchaIngreso(){
    var textoIngresado = document.getElementById("caja");
    letra = textoIngresado.value.toLowerCase();
    if(letra===''){
        return;
    }
    llenaPalabraVacia(letra, analizaLetra(letra));
    actualizaPistasYLetras();
    creaContenedorPalabra();
    analizaLetrasAdivinadas();
} 

function analizaLetra(letra){//busca la posicion de la letra,
    var posiciones = [];
    var contador = 0;
    for(i=0;i<palabraGenerada.length;i++){
        if(palabraGenerada[i]===letra){
            console.log(palabraGenerada[i]===letra, letra, palabraGenerada[i]);
            posiciones[contador] = i;
            contador++;
        }
    }
    return posiciones;
}

function llenaPalabraVacia(letra, posiciones){
    console.log(analizaLetra(letra).length);
    console.log(posiciones);
    if(analizaLetra(letra).length>0){
    for(i=0;i<posiciones.length;i++){
        palabraVacia[posiciones[i]] = letra;
        console.log(palabraVacia);
        incrementaLetrasAdivinadas();
        console.log("Llegue");
    }} else{
        incrementaPistasGanadas();
        console.log("Llegue");
    }
}

function incrementaPistasGanadas(){
    letraspistas++;
}

function incrementaPistasMostradas(){
    pistasmostradas;
}

function incrementaLetrasAdivinadas(){
    letrasadivinadas++;
}

function generaPista() {
    var letra;
    if(palabraPista.length>0 && letraspistas>0){
        letra = palabraPista.shift();
        suprimeLetra(letra);//saco las letras coincidente de palabraPista
        incrementaPistasMostradas();
        llenaPalabraVacia(letra,analizaLetra(letra));
        actualizaPistasYLetras();
        creaContenedorPalabra();
        analizaLetrasAdivinadas();
        
    }else{
        alert("No hay pistas suficientes");
    }
}

function suprimeLetra(letra){
    var posiciones = analizaLetra(letra);
    var letrasEliminadas = [];
    for(i=0;i<posiciones.length;i++){
        letrasEliminadas.push(palabraPista.splice(posiciones[i],posiciones[i]));
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////
function analizaLetrasAdivinadas(){
    if(palabraVacia.length === letrasadivinadas){
        if(letrasadivinadas>letraspistas){
            juegoTerminado("Gana");
            continua = false;
        }else if(letrasadivinadas===letraspistas){
            juegoTerminado("Empate");
            continua = false;
        }else{
            juegoTerminado("Pierde");
            continua = false;
        }
    }
}

function mensajeDecision(decision){
    var palabra;
    switch(decision){
        case "Pierde" : palabra= "Jugador " + jugador + " perdiste, intenta nuevamente";
        break;
        case "Gana" : console.log("gana");
        palabra = "Jugador " + jugador + " ganaste la partida con el puntaje: " + letrasadivinadas;
        break;
        case "Empate" : palabra = "Jugador " + jugador + " empataste la partida con puntaje " + letrasadivinadas;
        default :
        break;
    }
    console.log(palabra);
    return palabra;
}

function creaParrafoMensaje(decision){
    var mensajeAntiguo = document.getElementById("mensaje");
    if(mensajeAntiguo){
        padre = mensajeAntiguo.parentNode;
        padre.removeChild(mensajeAntiguo);
    }
    var nuevoMensaje = document.createElement("p");
    nuevoMensaje.setAttribute("id","mensaje");
    nuevoMensaje.innerHTML = mensajeDecision(decision);
    return nuevoMensaje;
}

function juegoTerminado(decision){
    var mensaje = document.getElementById("mensaje-decision");
    mensaje.appendChild(creaParrafoMensaje(decision));
}

function ocultaElemento(elemento){
    var e = document.getElementById(elemento);
    console.log(e.id);
    
    e.classList.add("oculto");
    console.log(e.classList);
}

function muestraElemento(elemento){
    var e = document.getElementById(elemento);
    e.classList.toggle("oculto");
}

function enviarSesion(){
    var padre = document.getElementById("seccion-ingreso-datos");
    Array.from(padre.children).forEach(hijo => {
        if(hijo.tagName === 'INPUT'){
            localStorage.setItem(hijo.id,hijo.value);
        }
    })
    ocultaElemento("seccion-ingreso-datos");
    muestraElemento("parte-principal");
}

function creaBotonABNDoVolver(opcion,funcion,texto){
    var btnviejo = document.getElementById("btn-"+opcion);
    if (btnviejo){
        padre = btnviejo.parentNode;
        padre.removeChild(btnviejo);
    }
    var btnnuevo = document.createElement("button");
    btnnuevo.setAttribute("id","btn-"+opcion);
    btnnuevo.setAttribute("name",opcion);
    btnnuevo.setAttribute("value","op"+opcion);
    btnnuevo.setAttribute("onclick",funcion + "();");
    btnnuevo.innerHTML = texto;
    return btnnuevo;
}

function botonAbandonar(){
ocultaElemento("contenedor-palabra-vacia");
ocultaElemento("contenedor-letras-ingresadas");
ocultaElemento("mensaje-decision");
muestraElemento("seccion-ingreso-datos");
reiniciaValores();
}

function reiniciaValores(){
    palabraVacia = [];
    palabraPista = [];
    letrasadivinadas = 0; 
    letraspistas = 0; 
    pistasmostradas = 0;
}
function botonVolverJugar(){
    ocultaElemento("contenedor-palabra-vacia");
    ocultaElemento("contenedor-letras-ingresadas");
    ocultaElemento("mensaje-decision");
    reiniciaValores();
    muestraElemento("parte-principal");
}