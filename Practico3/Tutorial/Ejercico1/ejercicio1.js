function verCadena(){
    var arreglo1 = new String;
    var i;
    var mayusculas=0, minusculas=0;
    arreglo1=prompt("Ingresa una palabra o serie de palabras");
    for(i=0 ;i<arreglo1.length;i++){
        if(arreglo1.charAt(i)== arreglo1.toLowerCase().charAt(i)){
            minusculas++;
        }else if (arreglo1.charAt(i)== arreglo1.toUpperCase().charAt(i)){
            mayusculas++;
        }
    }
    if(mayusculas > 0 && minusculas >0){
        alert("La cadena esta conformada por mayusculas y minusculas, mayusculas: "+ mayusculas+" minusculas: " + minusculas);
    } else if(mayusculas>0){
        alert("La cadena esta conformada por mayusculas, en total: " + mayusculas);
    } else if(minusculas>0){
        alert("La cadena esta conformada por minusculas, minusculas: " + minusculas);
    }
}