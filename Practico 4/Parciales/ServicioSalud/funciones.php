<?php

function formularioRegistro()
{ ?>
    <form name="formRegistro" action="registro.php" method='POST'>
        <center>
            <label for="idnombre">Nombre: </label>
            <input type="text" id="idnombre" name="nombre">

            <label for="iddni">DNI: </label>
            <input type="text" id="iddniReg" name="dni">

            <label for="idclave">Clave: </label>
            <input type="text" id="idclaveReg" name="clave">

            <input type="submit" value="Registrarse">
        </center>
    </form>
    <?php
}

function formularioInicioSesion()
{ ?>
    <form name="formInicioSesion" action="inicioSesion.php" method='POST'>
        <center>
            <label>DNI: </label>
            <input type="text" id="iddniInicio" name="dni">
            <label>Clave: </label>
            <input type="text" id="idclaveInicio" name="clave">

            <input type="submit" value="Iniciar Sesion">
            <br>

            <a href="registro.php">Primera vez? Click Aqui</a>
        </center>
    </form>
    <?php
}

function calculaIMC(Paciente $paciente)
{
    $peso = $paciente->getPeso();
    $altura = $paciente->getEstatura();
    $paciente->setIMC($peso/$altura);
    $imc = $paciente->getIMC();
    switch ($imc){
        case ($imc < 18.5):
            $paciente->setResultado("Bajo peso");
            break;
        case ($imc >= 18.5 && $imc <= 24.89):
            $paciente->setResultado("Peso normal");
            break;
        case ($imc >= 24.9 && $imc <= 29.89):
            $paciente->setResultado("Sobrepeso");
            break;
        case ($imc >= 29.9):
            $paciente->setResultado("Obesidad");
            break;
    }

    return $paciente;
}

function formulaioIMC()
{
?>
<form name="formIMC" action="pagCalculadora.php" method="POST">
    <label for="idnombreimc">Ingrese nombre completo: </label>
    <input id="idnombreimc" name="nombreimc" type="text" required>
    <br>

    <label for="idpesoimc">Ingrese su peso: </label>
    <input id="idpesoimc" name="pesoimc" type="number" step="0.01" placeholder="Ejemplo: 50.55" required>
    <label for="idpesoimc">kg</label><br>

    <label for="idalturaimc">Ingrese la altura: </label>
    <input id="idalturaimc" name="alturaimc" type="number" step="0.01" placeholder="Ejemplo: 1.55" required>
    <label for="idalturaimc">m/2</label><br>

    <input type="submit" value="Agregar">
</form>
<?php
}

function reporte(Usuario $usuario)
{
    $main = $usuario->getUsuarioMain($usuario->getNombre());
    if($main !== null){
    echo "<p>Paciente: " . $main->getNombre() . "</p><br>";
    echo "<p>Peso: " . $main->getPeso() . "</p><br>";
    echo "<p>IMC: " . $main->getIMC() . "</p><br>";
    echo "<p>Resultado: " . $main->getResultado() . "</p><br>";
    echo "<p>Calculos realizados hasta el momento: </p><br>";
    echo "<table><tr><th>Paciente</th><th>IMC</th><th>Resultado</th></tr>";
    }
    $integrantes = $usuario->getIntegrantes();
    for($i=0;$i<count($integrantes);$i++){
        echo "<tr><td>". $integrantes[$i]->getNombre()."</td>";
        echo "<td>". $integrantes[$i]->getIMC(). "</td>";
        echo "<td>". $integrantes[$i]->getResultado(). "</td></tr>";
    }
}

?>