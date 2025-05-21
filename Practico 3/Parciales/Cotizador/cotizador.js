var opcionesSeleccionadas = [];
var opAcuario = 0, opOtro = 0;


function chequeaOpciones() {
    const opciones = document.getElementsByName('opcion');
    opciones.forEach(element => {
        console.log(element.checked);
        if (element.checked) {
            opcionesSeleccionadas.push(element);
            console.log(element.checked);
        }
    })
}

function haceAlgo() {
    chequeaOpciones();
    for (i = 0; i < opcionesSeleccionadas.length; i++) {
        if (opcionesSeleccionadas[i].value === "Acuario") {
            document.getElementById("acuario").style.display = 'block';
        }else{
            document.getElementById("otros").style.display = 'block';
        }
    }
}