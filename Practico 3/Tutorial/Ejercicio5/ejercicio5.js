function agrandar(imagen){
    newWindow = window.open("","","width=400, heigth=350, status=yes, resizable=yes, menubar=yes");
    newWindow.document.write("<img src=");
    newWindow.document.write('"');
    newWindow.document.write(imagen.src);
    newWindow.document.write('"');
    newWindow.document.write("width=100% heigth=100% >");
    newWindow.document.write("<input id=\"botoncerrar\" type=\"button\" onClick=\"window.close();\" value=\"Cerrar ventana\"/>");
    newWindow.document.bgColor = "white";
    newWindow.focus();
}