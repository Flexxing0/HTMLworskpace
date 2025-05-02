function calculaFactorial(){
    var num, lbl, resu;
    lbl = document.getElementById("labelnumero");
    num = lbl.value;
    resu=1;
    for(i=1;i<=num;i++){
        resu = resu * i;
    }
    var contenedor, elemento;
    contenedor = document.getElementById("contenedor");
    elemento = document.getElementById("nuevoELemento");
    if(elemento){
        padre = elemento.parentNode;
        padre.removeChild(elemento);
    }
    var creaElemento = document.createElement("p");
    creaElemento.setAttribute("id","nuevoElemento");
    creaElemento.innerHTML = "El factorial de " + num + " es: " + resu;
    contenedor.appendChild(creaElemento);
}

