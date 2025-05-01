function ingresaNumero(){
    var formulario, cuadrotexto;
    formulario = document.getElementById("formulario1");
    cuadrotexto = document.getElementById("labelnumero");
    if(cuadrotexto.value == ""){
        alert("Faltan cargar datos");
        cuadrotexto.focus();
    }else{
        alert("Se envio el form");
        formulario.submit();
    }
}