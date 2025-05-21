const porcentajes = [0.30, 0.5, 0.10, 0.30, 0.25];


function comenzarTest(){
    document.getElementById("idbtn-test").style.display = 'none';
    document.getElementById("seccion-ingreso-datos").style.display = 'block';
}

function enviarSesion(){
    document.getElementById("seccion-ingreso-datos").style.display = 'none';
    document.getElementById("seccion-test").style.display = 'block';
}

function sumatoria(numid){
    var field = document.getElementById("idpregunta"+numid);
    var sumatoria = 0;
    Array.from(field.children).forEach(hijo => {
        if('value' in hijo){
            if(hijo.checked){
                console.log(hijo.value);
                sumatoria += hijo.value;
            }
        }
    })
    return sumatoria;
}

function procesaDatos(){
    var puntajeTotal = 0;
    for(i=0; i<porcentajes.length;i++){
        console.log(sumatoria(i+1),porcentajes[i]);
        puntajeTotal += (sumatoria(i+1) * porcentajes[i]);
    }
    return puntajeTotal;
}

function generaContenedor(texto){
    var contenedor = document.getElementById("contenedor");
    var parrafoviejo = document.getElementById("parrafo");
    if(parrafoviejo){
        padre = parrafoviejo.parentNode;
        padre.removeChild(parrafoviejo);
    }
    var parrafonuevo = document.createElement("p");
    parrafonuevo.setAttribute("id","parrafo");
    parrafonuevo.innerHTML = texto;
    contenedor.appendChild(parrafonuevo);
}

function muestraResultados(){
    let resultados = procesaDatos();
    if(resultados>=8){
        generaContenedor("Cocinero");
    }
    else if(resultados<6){
        generaContenedor("No calificas para el puesto");
    }
    else{
        generaContenedor("Ayudante de Cocina")
    }
}

