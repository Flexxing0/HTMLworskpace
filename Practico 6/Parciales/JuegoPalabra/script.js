function nuevaPista(){
    const puntaje = ((int).document.getElementById("puntaje").textContent) - 15;
    const pistas = ((int).document.getElementById("cantidadPistas").textContent) - 1;
    const idpalabra = document.getElementById("idpalabra").value;
    var parametros = "idpalabra="+idpalabra;
    parametros += "&pistas="+pistas;
    var peticion = new XMLHttpRequest();
    peticion.open("POST","procesamiento.php",true);
    peticion.onreadystatechange = mostrarPista;
    peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    peticion.send(parametros);

    function mostrarPista()
    {
        if  ((peticion.readyState == 4) && (peticion.status == 200))
        {
            console.log(peticion.responseText);
            var objeto = JSON.parse(peticion.responseText);

        }
    }
}

function arriesga(){

}

function rendirse(){

}

function nuevaPartida(){

}