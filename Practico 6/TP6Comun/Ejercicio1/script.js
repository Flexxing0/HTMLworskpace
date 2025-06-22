function muestraCiudades()
{
    var idpais = document.getElementById("idpaises").selectedIndex;
    var parametros = "id_pais="+idpais;
    var peticion = new XMLHttpRequest();
    peticion.open("POST","indexa.php",true);
    peticion.onreadystatechange = cargaCiudades ;//cambiar
    peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    peticion.send(parametros);

    function cargaCiudades(){
        if  ((peticion.readyState == 4) && (peticion.status == 200))
        {
            console.log(peticion.responseText);
            var objeto = JSON.parse(peticion.responseText);
            var paisheader = document.getElementById('idpais');
            paisheader.innerHTML = objeto['nombre_pais'];
            var ciudadesbody = document.getElementById("idciudades");
            ciudadesbody.innerHTML = "";
            console.log(objeto['ciudades']);
            objeto['ciudades'].forEach(ciudad => {
                texto = "<tr><td>"+ciudad+"</td></tr>";
                ciudadesbody.innerHTML += texto;
            });
        }
    }
}

