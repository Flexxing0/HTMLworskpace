document.addEventListener("DOMContentLoaded", () => {

    document.getElementById("formulario").addEventListener("submit",(e)=>{
        e.preventDefault();

        const documento = document.getElementById("iddocumento").value;
        var parametros = "documento="+documento;
        var peticion = new XMLHttpRequest();
        peticion.open("POST","indexa.php",true);
        peticion.onreadystatechange = muestraInformacion;
        peticion.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
        peticion.send(parametros);

        function muestraInformacion()
        {
            if  ((peticion.readyState == 4) && (peticion.status == 200))
            {
                console.log(peticion.responseText);
                var objeto = JSON.parse(peticion.responseText);
                
                var cuerpoCl = document.getElementById("datosCliente");
                var texto="";
                const cliente = objeto.cliente;
                texto += "<p>Nombre: "+cliente.nombre+"</p><br>";
                texto += "<p>Apellido: "+cliente.apellido+"</p><br>";
                texto += "<p>Fecha nacimiento: "+cliente.fecha_nacimiento+"</p><br>";
                texto += "<p>Domicilio: "+cliente.domicilio+"</p><br>";
                cuerpoCl.innerHTML = texto;

                var encabezado = document.getElementById("encabezado");
                encabezado.innerHTML = "<tr><th>COMPRA</th><th>Producto</th><th>Cantidad</th><th>Total</th><th>Fecha</th></tr>";
                var tabla = document.getElementById("cuerpo");
                var texto1 = "";
                const compras = objeto.compras;
                for(i=0; i<compras.length;i++)
                {
                    var compra = compras[i];
                    texto1 += "<tr>";
                    texto1 += "<td>"+compra.id_compra+"</td>";
                    texto1 += "<td>"+compra.producto+"</td>";
                    texto1 += "<td>"+compra.cantidad+"</td>";
                    texto1 += "<td>"+compra.total+"</td>";
                    texto1 += "<td>"+compra.fecha+"</td>";
                    texto1 += "</tr>";
                }
                tabla.innerHTML = texto1;
            }
        }

    });


})