function ingresaNumero(){
    var formulario, cuadrotexto, inp, valor;
    inp = document.getElementById("labelnumero");
    valor = inp.value;
    formulario = document.getElementById("formulario1");
    cuadrotexto = document.getElementById("labelnumero");


    creaH3(valor);
    muestraInfo(valor);
}

function creaH3(numero){
    var contenedor, muestra; 
    contenedor = document.getElementById("contenedor");
    muestra = document.getElementById("nuevaMuestra");
    if(muestra){
        padre = muestra.parentNode;
        padre.removeChild(muestra);
    }
    var creaMuestra = document.createElement("h3");
    creaMuestra.setAttribute("id","nuevaMuestra");
    creaMuestra.innerHTML = "El numero ingresado es: " + numero;
    contenedor.appendChild(creaMuestra);
}

function muestraInfo(numero){
    var contenedor, muestra;
    contenedor = document.getElementById("contenedor");
    muestra = document.getElementById("nuevaMuestra2");
    if(muestra){
        padre = muestra.parentNode;
        padre.removeChild(muestra);
    }
    var creaMuestra = document.createElement("h3");
    creaMuestra.setAttribute("id","nuevaMuestra2");
    creaMuestra.innerHTML = creaInformacion(numero);
    contenedor.appendChild(creaMuestra);
    }

function creaInformacion(numero){
    var cadena;
    cadena = puntoa(numero) + ", " + puntob(numero) + " y " + puntoc(numero);
    return cadena; 

}

function puntoa(numero){
    var ptoa;
    if (espar(numero)){
        ptoa = "Es par";
    }else if(esimpar(numero)){
        ptoa = "Es Impar ";
    }
    return ptoa;
}

function puntob(numero){
    var ptob;
    if(espar(numero) && esimpar(numero) && div5(numero)){
        ptob = " es divisible por 2, 3 o 5 ";
        return ptob;
    }else if (espar(numero) && esimpar(numero)){
        ptob = " es divisible por 2 o 3 ";
        return ptob;
    }else if (espar(numero) && div5(numero)){
        ptob = " es divisible por 2 o 5";
        return ptob;
    }else if (esimpar(numero) && div5(numero)){
        ptob = " es divisible por 3 o 5";
        return ptob;
    }else if (espar(numero)){
        ptob = " es divisible por 2";
        return ptob;
    }else if (esimpar(numero)){
        ptob = " es divisible por 3";
        return ptob;
    }else if (div5(numero)){
        ptob = " es divisible por 5";
        return ptob;
    }
}

function puntoc(numero){
    var ptoc;
    if(esprimo(numero)){
        ptoc = " es primo ";
        return ptoc;
    }else{
        ptoc = " no es primo";
        return ptoc;
    }
}


function espar(numero){
    if((numero%2) != 0 ){
        return false;
    }else{
        return true;
    }
}

function esimpar(numero){
    if((numero%3) != 0){
        return false;
    }else{
        return true;
    }
}

function div5(numero){
    if((numero%5) != 0){
        return false;
    }else{
        return true;
    }
}

function esprimo(numero){
    if(numero>1){
        var bool = false;
        let raiz = Math.sqrt(numero); //hago la raiz de numero
        for(let i = 2; i<=raiz;i++){ // si la division de numero por cada numero mayor a 2 hasta la raiz de numero es igual a la raiz entonces devuelve true
            if((numero/i) == raiz){
                bool = true;
                return bool; 
            }
        }
        return bool;
    }else{
        return false;
    }
}