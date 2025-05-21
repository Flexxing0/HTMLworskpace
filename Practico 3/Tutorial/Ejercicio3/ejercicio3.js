function ingresaDNI(){
    var dni, letra, vdni, vletra;
    dni = document.getElementById("labeldni");
    vdni = dni.value;
    letra = document.getElementById("labelletra");
    vletra = letra.value;
    if(vdni<0 || vdni>99999999){
        alert("Datos errones");
        dni.focus();
    }
    if(!isNaN(vletra)){
        alert("No es una letra");
        letra.focus();
    }
    if(calculo(vdni,vletra)){
        newWindow = window.open("","","width=400, heigth=350, status=yes, resizable=yes, menubar=no");
        newWindow.document.write("<h2>Bienvenido usuario</h2>");
        newWindow.document.write("<input id=\"botoncerrar\" type=\"button\" onClick=\"window.close();\" value=\"Cerrar ventana\"/>");
        newWindow.document.bgColor = "white";
        newWindow.focus();
        window.document.write("Ventana creada");
    }
    else{
        alert("Letra incorrecta");
        letra.focus;
    }
}

function calculo(dni, letra){
    var resto, letras;
    letras = new Array;
    letras=('T','R','W','A','G','M','Y','F','P','D','X','B','N','J','Z','S','Q','V','H','L','C','K','E','T');
    resto = dni % 23;
    if(letras[resto]==letra.toUpperCase()){
        return true;
    }else{
        return false;
    }
}
