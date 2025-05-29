<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejercicio E</title>
</head>
<body>
    <header><h1>Rand y Srand</h1></header>
    <section>
        <article>
        <?php
        srand((double)microtime() * 1000000);
        $n = rand(2,15);
        $arreglo = array();
        for($i=1;$i<=$n;$i++){
            $arreglo[$i] = array_fill(0,$i,$i);
        }
        for($i=1;$i<=count($arreglo);$i++){
            for($e=0;$e<count($arreglo[$i]);$e++){
                echo $arreglo[$i][$e];
            }
            echo "<br>";
        }
        
        ?>
        
        </article>
    </section>
    
</body>
</html>