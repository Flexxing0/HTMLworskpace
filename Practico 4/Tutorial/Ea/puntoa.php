<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejercicio A</title>
</head>
<body>
    <header><h1>Operaciones numericas con php</h1></header>
    <section>
        <article>
            <?php
            $var = 10;
            $var2 = 20;
            echo "Primera variable: \$var = ".$var;
            echo "<br>Segunda varibale: \$var2 = ".$var2;
            echo "<br>Sumatoria de \$var + \$var2 = ".$var + $var2;
            echo "<br>Promedio de \$var y \$var2 = ".($var + $var2)/2;
            echo "<br>Producto de \$var * \$var2 = ".$var * $var2;

            ?>
        </article>
    </section>
    
</body>
</html>