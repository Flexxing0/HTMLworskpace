<?php

include_once 'paises.php';
if(isset($_POST['id_pais'])){
$pais = Paises::getPaisBD($_POST['id_pais']);
if (is_null($pais))
{
    $objTemp = new stdClass();
    $objTemp->id_pais = "Pais no encontrado";
    $objTemp->nombre_pais = "---";
    $objTemp->ciudades = "No hay ciudades";
    $json = json_encode($objTemp);
} else {
    
    $objTemp = new stdClass(); 
    $objTemp->id_pais = $pais->getIdpais();        
    $objTemp->nombre_pais = $pais->getNombrepais();
    $objTemp->ciudades = Paises::getCiudadesBD($_POST['id_pais']);    
    $json = json_encode($objTemp);
}
echo $json;
}
?>