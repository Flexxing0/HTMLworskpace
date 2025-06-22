    function ocultaElemento(elemento){
        var e = getId(elemento);
        if(e.classList.contains("oculto")){
            return;
        }else{
        e.classList.add("oculto");
        }
    }

    function muestraElemento(elemento){
        var e = getId(elemento);
        if(e.classList.contains("oculto")){
            e.classList.remove("oculto");
        }
    }