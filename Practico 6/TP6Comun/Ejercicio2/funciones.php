<?php

function ingresaDocumento()
{
   ?>
    <form id="formulario">
        <label for="iddocumento">Ingrese un documento</label>
        <input type="text" id="iddocumento" name="documentos" required maxlength="8"><br>
        <button type="submit">Enviar</button>
    </form>
    <div id="datosCliente"></div>
    <div id="datosCompras">
        <table>
            <thead id="encabezado">

            </thead>
            <tbody id="cuerpo">

            </tbody>
        </table>
    </div>
   <?php
}

?>