function suma(){

    var num1,num2, potencia, cociente=0; 
    num1 = parseInt(prompt("Ingrese un numero "));
    num2 = parseInt(prompt("Ingrese un valor: "));
    if(num1>num2){
        potencia=num1**num2;
        alert("La potencia de " + num1 +" y "+ num2 + "es: " + potencia);
    }
    else{
        while(num1<=num2){
            num2 -= num1;
            ++cociente;
        }
        alert("Se resto:"+cociente+"veces");
    }
    
}
