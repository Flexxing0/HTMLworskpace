function ocultaElemento(elemento){
    var e = document.getElementById(elemento);
    if(e.classList.contains("oculto")){
        return;
    }else{
    e.classList.add("oculto");
    }
}

function muestraElemento(elemento){
    var e = document.getElementById(elemento);
     if(e.classList.contains("oculto")){
        e.classList.remove("oculto");
    }
}

function resetForm(elemento){
    var e = document.getElementById(elemento);
    e.reset();
}
const contenedorPaises = document.getElementById("contenedor-paises");

function muestraProducto()
{
    const producto = document.getElementById("idselect-productos").selectedIndex;
    ocultaElemento("contenedor-paises");

    var parametros = "idproducto="+producto+"&action=producto";
    var peticion = new XMLHttpRequest();
    peticion.open("POST","procesamiento.php",true);
    peticion.onreadystatechange = mostrarProducto;
    peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    peticion.send(parametros);

    function mostrarProducto()
    {
        if  ((peticion.readyState == 4) && (peticion.status == 200))
        {
        console.log(peticion.responseText);
        var objeto = JSON.parse(peticion.responseText);
        var nombre = objeto.nombre_producto;
        var precio = objeto['precio'];
        var stock = objeto['stock'];
        var contenedor = document.getElementById("contenedor-producto");
        var texto = "<table class='tabla-moderna'>";
        texto += "<tr><th>Nombre</th><th>Precio</th><th>Stock</th><th>Cantidad a comprar</th></tr>";
        texto += "<tr><td>"+nombre+"</td><td>"+precio+"</td><td>"+stock+"</td><td><input type='number' min='0' max='"+stock+"' value='0' id='idcantidad'><input type='button' value='Aceptar' onClick='muestraTotal();'></td></tr>";
        texto += "</table><div id='idtotal'></div>";
        contenedor.innerHTML = texto;
        }
    }
}

function muestraTotal()
{
    const producto = document.getElementById("idselect-productos").selectedIndex;
    const cantidad = document.getElementById("idcantidad").value;
    var parametros = "idproducto="+producto+"&action=importe"+"&idcantidad="+cantidad;
    var peticion = new XMLHttpRequest();
    peticion.open("POST","procesamiento.php",true);
    peticion.onreadystatechange = mostrarTotal;
    peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    peticion.send(parametros);

    function mostrarTotal()
    {
        if  ((peticion.readyState == 4) && (peticion.status == 200))
        {
            console.log(peticion.responseText);
            var objeto = JSON.parse(peticion.responseText);
            const contenedor = document.getElementById("idtotal");
            contenedor.innerHTML = "<p>Total: "+objeto.total+"</p>";
        }
    }
    muestraPaises();
}

function muestraPaises()
{
    muestraElemento("contenedor-paises");
    resetForm("contenedor-paises");
}

function muestraCiudades()
{
    const pais = document.getElementById("idselect-paises").selectedIndex;
    
    var parametros = "idpais="+pais+"&action=ciudad";
    var peticion = new XMLHttpRequest();
    peticion.open("POST","procesamiento.php",true);
    peticion.onreadystatechange = mostrarCiudades;
    peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    peticion.send(parametros);

    function mostrarCiudades()
    {
        if  ((peticion.readyState == 4) && (peticion.status == 200))
        {
            console.log(peticion.responseText);
            var objeto = JSON.parse(peticion.responseText);
            const contenedor = document.getElementById("idselect-ciudades");
            var texto = "<option value='' selected>--Selecciona una ciudad--</option>";
            var ciudades = objeto['ciudades'];
            for(i=0;i<ciudades.length;i++){
                console.log(ciudades[i].id_ciudad);
                texto += "<option value='"+ciudades[i].id_ciudad+"'>"+ciudades[i].nombre_ciudad+"</option>";
            }
           
            contenedor.innerHTML = texto;
        }
    }
}

function muestraImporte()
{
    const pais = document.getElementById("idselect-paises").selectedIndex;
    const ciudad = document.getElementById("idselect-ciudades").value;
    console.log(ciudad);
    var parametros = "idpais="+pais+"&action=tarifa"+"&idciudad="+ciudad;
    var peticion = new XMLHttpRequest();
    peticion.open("POST","procesamiento.php",true);
    peticion.onreadystatechange = mostrarTarifa;
    peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
    peticion.send(parametros);

    function mostrarTarifa()
    {
        if  ((peticion.readyState == 4) && (peticion.status == 200))
        {
            console.log(peticion.responseText);
            var objeto = JSON.parse(peticion.responseText);
            const contenedor = document.getElementById("idtarifa");
            var texto = "<p>Tarifa envio: "+objeto.importe+"</p>";
            contenedor.innerHTML = texto;
        }  
    }
}