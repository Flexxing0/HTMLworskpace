var numJG, puntajeJG = 0 , digitoJG = 0 , numPJ, puntajePJ = 0 , digitoPJ = 0, ronda = 0 ;
var dia, mes, anio, hora;


document.addEventListener("DOMContentLoaded", function(){
const inicia = document.getElementById("inicia");
const seleccion = document.getElementById("seleccion");
})

function generaNumero(dificultad){ //genera numeros aleatorios dependiendo dificultad
        switch(dificultad){
        case 'Facil' : numJG = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                        numPJ = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
            break;
        case 'Normal' : numJG = Math.floor(Math.random() * 100000).toString().padStart(5, '0');
                        numPJ = Math.floor(Math.random() * 100000).toString().padStart(5, '0');
        break; 
        case 'Dificil' : numJG = Math.floor(Math.random() * 100000000).toString().padStart(8, '0');
                        numPJ = Math.floor(Math.random() * 100000000).toString().padStart(8, '0');
        break;
        default:
    }
}

function creaNumero(jugador, numero){ //crea el texto numero de jugador 
    var nuevo = document.createElement("p");
    nuevo.setAttribute("class","numero-generado");
    nuevo.innerHTML = "Numero " + jugador + ": " + numero;
    return nuevo;
}

function creaTextNumeroJugador(){  //crea el texto para cada jugador
    var numeroJugador = document.getElementById("partida-num-jugador");
    var numeroCompu = document.getElementById("partida-num-computadora");
    var numerosViejos = document.getElementsByClassName("numero-generado");
    for(i= numerosViejos.length -1; i>=0;i--){ //para eliminar nodos hijos de varios elementos, se hace en reversa para no saltar nodos
        if(numerosViejos[i]){
            padre = numerosViejos[i].parentNode;
            padre.removeChild(numerosViejos[i]);
        }
    }
    numeroJugador.appendChild(creaNumero("Jugador",numJG));
    numeroCompu.appendChild(creaNumero("Computadora",numPJ));
}

function textosNumeros(numeros){
    var numero=document.createElement("p");
    numero.setAttribute("class", "texto-numeros");
    numero.innerHTML = numeros;
    return numero;
}

function actualizaTxtNumero(){
    var numeroJugador = document.getElementById("partida-num-jugador");
    var numeroCompu = document.getElementById("partida-num-computadora");
    numeroJugador.appendChild(textosNumeros(numJG));
    numeroCompu.appendChild(textosNumeros(numPJ));
}

function sumaRonda(){//usado en actualizaRonda()
    var nueva = document.createElement("p");
    nueva.setAttribute("id","ronda-numero");
    nueva.innerHTML = "Ronda " + ronda++;
    return nueva;
}

function actualizaRonda(){//usado en generaRonda()
        var ronda = document.getElementById('partida-ronda');
    var rondaNueva = document.getElementById('ronda-numero');
    if(rondaNueva){
        padre = rondaNueva.parentNode;
        padre.removeChild(rondaNueva);
    }
    ronda.appendChild(sumaRonda());
}

function reducePrimerCifra(numero,puntaje, digito){ 
    var digitos = numero.toString().split(''); //convierte en cadena y luego en caracteres
    primerCifra = parseInt(digitos[digito]); //convierte en numero
    if(primerCifra===0){//si ya es 0, aumenta puntaje y pasa al siguiente digito
        puntaje++;
        digito++;
        return [numero, puntaje, digito];
    }else if(primerCifra>0){
        primerCifra--;
    }
    digitos[digito] = primerCifra.toString();
    numero = digitos.join(''); //join junta los caracteres en numero otra vez
    return [numero, puntaje, digito];
}

function textoPuntaje(jugador, numero){ //crea el texto con puntaje para cada jugador
    var nuevo = document.createElement("p");
    nuevo.setAttribute("class","puntaje-texto");
    nuevo.innerHTML = "Puntaje" + jugador + ": " + numero;
    return nuevo;
}

function textoPuntajes(){ //genera los textos con los puntajes
    var pJG = document.getElementById('puntaje-jugador');
    var pPJ = document.getElementById('puntaje-computadora');
    var puntajeViejo = document.getElementsByClassName("puntaje-texto");
    for(i= puntajeViejo.length -1; i>=0;i--){ //para eliminar nodos hijos de varios elementos, se hace en reversa para no saltar nodos
        if(puntajeViejo[i]){
            padre = puntajeViejo[i].parentNode;
            padre.removeChild(puntajeViejo[i]);
        }
    }
    pJG.appendChild(textoPuntaje("Jugador", puntajeJG));
    pPJ.appendChild(textoPuntaje("Computadora", puntajePJ));
}

function restaNumeros(){
    [numJG, puntajeJG, digitoJG] = reducePrimerCifra(numJG, puntajeJG, digitoJG);
    [numPJ, puntajePJ, digitoPJ] = reducePrimerCifra(numPJ, puntajePJ, digitoPJ);
}

function verificaGanador(){
    if(numJG.length === digitoJG){
        terminaPartida("Humano", puntajeJG, "Maquina" , puntajePJ);
    }else if(numPJ.length === digitoPJ){
        terminaPartida("Maquina", puntajePJ, "Humano" , puntajeJG);
    }
}

function terminaPartida(jugador1, puntaje1, jugador2, puntaje2){
    var pantalla = document.getElementById('pantalla-fin');
    var viejaPantalla = document.getElementsByClassName('pantalla-generada');
    for(i= viejaPantalla.length -1; i>=0 ; i--){
        if(viejaPantalla[i]){
        padre = viejaPantalla[i].parentNode;
        padre.removeChild(viejaPantalla[i]);
        }
    }
    var pantallanv = document.createElement("h2");
    pantallanv.setAttribute("class","pantalla-generada");
    pantallanv.innerHTML = "Felicidades, jugador " + jugador1 + " ganaste la partida con puntaje: " + puntaje1;
    pantalla.appendChild(pantallanv);
    pantallanv = document.createElement("h2");
    pantallanv.setAttribute("class","pantalla-generada");
    pantallanv.innerHTML = "Jugador " + jugador2 + " perdiste la partida con puntaje: " + puntaje2;
    pantalla.appendChild(pantallanv);
}

function creaOpciones(dificultad){
    var opcion = document.createElement("option");
    opcion.setAttribute("value", dificultad);
    opcion.innerHTML = dificultad;
    return opcion;
}

function creaBotonesDificultad(){
    var formu = document.getElementById("seleccion");
    var botones = document.createElement("select");
    botones.setAttribute("id", "iddificultad");
    botones.add(creaOpciones("Facil"));
    botones.add(creaOpciones("Normal"));
    botones.add(creaOpciones("Dificil"));
    formu.appendChild(botones);
}

function creaBotonIniciaPartida(){
    var cont = document.getElementById("seleccion");
    var boton = document.createElement("p");
    boton.setAttribute("id", "idp");
    boton.innerHTML = "<button type=\"button\" name=\"jugar\" value=\"Jugar\" onClick=\"comienzaJuego();\" >Iniciar Partida</button>";
    cont.appendChild(boton); 
}

function creaBotonJugar(){
    var cont = document.getElementById("seleccion");
    var boton = document.createElement("button");
    boton.setAttribute("id", "idpartida");
    boton.setAttribute("name", "inicia");
    boton.setAttribute("value", "Iniciar");
    boton.setAttribute("onClick", "generaRonda();");
    boton.innerHTML = "Jugar"
    cont.appendChild(boton);
}

function ingresaJuego(){
    creaBotonesDificultad();
    creaBotonIniciaPartida();
    creaBotonJugar();
}

function generaRonda(){
    actualizaRonda();
    restaNumeros();
    textoPuntajes();
    actualizaTxtNumero();
    verificaGanador();
}

function comienzaJuego(){
    difi = document.getElementById("iddificultad");
    dificultad = difi.value;
    generaNumero(dificultad);
    creaTextNumeroJugador();
    
    
}

function abandonaPartida(){
}
function guardaPartida(){}

