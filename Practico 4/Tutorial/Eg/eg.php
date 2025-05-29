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
        srand((double) microtime() * 1000000);
        $num1 = rand(0,10);
        $num2 = rand(0,$num1);
        echo "num1: " .$num1 . "<br> num2: " . $num2 . "<br>";
        $arreglo = array();
        for($i=0;$i<=$num2;$i++){
            echo $num1**$i;
            $arreglo[] = $num1 ** $i;
        }
        echo "<pre>";
        print_r($arreglo);
        echo "</pre>";

        ?>
        
        </article>
    </section>
    
</body>
</html>