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
        /*
        srand((double) microtime() * 1000000);
        $a = rand(0, 3);
        $b = rand(0, 3);
        ($a == $b) ? ($mensaje = "$a = $b") : ($mensaje = "$a != $b");
        echo $mensaje;
        */
        srand((double) microtime() * 1000000);
        $d = $c = $b = $a = rand(0, 10);
        echo "\$a = " . $a;
        echo "<br>\$b = " . $b;
        echo "<br>\$c = " . $c;
        echo "<br>\$d = " . $d;
        ?>
        

        </article>
    </section>
    
</body>
</html>