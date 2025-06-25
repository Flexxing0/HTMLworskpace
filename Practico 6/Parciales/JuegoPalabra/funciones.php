<?php

function cargaJuego()
{
    include 'palabras.php';
    include 'pistas.php';
    include 'usuario.php';
    $usuario = new Usuario();
    $palabras = Palabras::getPalabrasBD();
    $palabraAleatoria = $palabras[array_rand($palabras)];
    $pistas = Pistas::getPistasPalabra($palabraAleatoria->getIdPalabra());
    echo "<div id='informacionPartida'>";
    echo "<input type='hidden' id='palabra' value='".$palabraAleatoria->getPalabra()."'>";
    echo "<input type='hidden' id='idpalabra' value='".$palabraAleatoria->getIdPalabra()."'>";
    echo "<p>Cantidad Letras: ".mb_strlen($palabraAleatoria->getPalabra())."</p>";
    echo "<p>Dificultad: ".$palabraAleatoria->getDificultadPalabra()."</p>";
    echo "<p>Veces acertada: ".$palabraAleatoria->getAcertada()."</p>";
    echo "<p>Puntaje actual: </p><div id='puntaje'>".$usuario->getPuntaje()."</div>";
    echo "</div>";
    echo "<div id='informacionPistas'>";
    echo "<p>Pistas Disponibles: </p><div id='cantidadPistas'>".$usuario->getPistasDisponibles()."</div>";
    echo "<input type='button' value='Nueva pista' onClick='nuevaPista();'>";
    echo "<div id='pistas'>";
    echo "<table><thead id='header'></thead><tbody id='cuerpo'></tbody></table>";
    echo "</div>";
    echo "</div>";
    echo "<div id='informacionControles'>";
    echo "<input type='text' id='inputPalabra'><input type='button' value='Arriesgar' onClick='arriesga();'>";
    echo "<input type='button' value='Rendirse' onClick='rendirse();'>";
    echo"</div>";
    echo "<div id='resultado'></div>";
}

?>