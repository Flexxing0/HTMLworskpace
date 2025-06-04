<?php

function formularioRegistro()
{?>
<form name="formRegistro" action="" method='POST'>
    <center>
        <label for="idnombre">Nombre: </label>
        <input type="text" id="idnombre" name="nombre">

        <label for="iddni">DNI: </label>
        <input type="text" id="iddniReg" name="dni">

        <label for="idclave">Clave: </label>
        <input type="text" id="idclaveReg" name="clave">
    </center>
</form>
<?php
}

function formularioInicioSesion()
{?>
<form name="formInicioSesion" action="" method='POST'>
    <center>
        <label>DNI: </label>
        <input type="text" id="iddniInicio" name="dni">
        <label>Clave: </label>
        <input type="text" id="idclaveInicio" name="clave">
    </center>
</form>
<?php
}

function 
?>