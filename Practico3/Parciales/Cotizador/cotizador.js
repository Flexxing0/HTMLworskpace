var opcionesSeleccionadas = [];

function chequeaSelecciones(){
    var opciones = document.getElementsByName("opcion");
    opciones.forEach(element=>{
        if(element.check){
            opcionesSeleccionadas.push(element);
            console.log(element.value);
        }
    })
}


function haceAlgo(){
chequeaSelecciones();
for(i=0;i<opcionesSeleccionadas.length;i++){
    if(opcionesSeleccionadas[i] === "Acuario"){
        document.getElementById('acuario').style.display = 'block;'
    }
}
}