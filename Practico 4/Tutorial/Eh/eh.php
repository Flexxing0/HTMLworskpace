<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejercicio H</title>
</head>
<body>
    <header><h1>Con arrays</h1></header>
    <section>
        <article>
        <?php
        srand((double) microtime() * 1000000);
        $num = rand(0,10);
        $arreglo = array();
        $suma = 0;
        for($i=0;$i<$num;$i++){
            $arreglo[$i] = rand(0,10);
            
        }
        $longitud = count($arreglo);
        echo "Arreglo: <pre>".print_r($arreglo) ."</pre><br>";
        echo "Cantidad: ".count($arreglo)." sumatoria: ".array_sum($arreglo);
        echo "<br> Primer elemento: ". $arreglo[0]. " Ultimo elemento: " . $arreglo[$longitud-1];
        echo "<br> Menor elemento: ". min($arreglo)." Mayor elemento: ". max($arreglo);
        $esta = (In_array(5, $arreglo)) ? "Esta" : "No esta";
        echo "<br>El numero 5 ". $esta;
        echo "<br>Promedio:  ". array_sum($arreglo)/$longitud."<br>";
        Sort($arreglo);
        echo "<br>Ordenado: " . print_r($arreglo);
        ?>
        
        </article>
    </section>
    
</body>
</html>