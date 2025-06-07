<?php

function formularioInicioSesion()
{ ?>
    <form name="formInicioSesion" action="login.php" method='POST'>
        <center>
            <label>Usuario </label>
            <input type="text" id="iddniInicio" name="nombre" required>
            <br>
            <label>Contrasena </label>
            <input type="password" id="idclaveInicio" name="contrasena" required>
            <br>
            <input type="checkbox" id="idrecordarme" name="recordarme">
            <label for="idrecordarme">Recordarme?</label>
            <br>

            <input type="submit" value="Iniciar Sesion">
            <br>
        </center>
    </form>
    <?php
}


?>