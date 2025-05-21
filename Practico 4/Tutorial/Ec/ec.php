<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejercicio C</title>
</head>
<body>
    <header><h1>Operaciones numericas con php parte 2</h1></header>
    <section>
        <article>
            <?php
            $var = 10;
            $var2 = 3;
            echo "Primera variable: \$var = ".$var;
            echo "<br>Segunda varibale: \$var2 = ".$var2;
            echo "<br>Sumatoria de \$var + \$var2 = ".$var + $var2;
            echo "<br>Promedio de \$var y \$var2 = ".($var + $var2)/2;
            echo "<br>Producto de \$var * \$var2 = ".$var * $var2;

            if($var > $var2){
                $potencia = $var;
                for($i=0;$i<$var2-1;$i++){
                    $potencia *=$potencia;
                }
                echo "<br> La potencia de ".$var." a la ".$var2." es = ".$potencia;
            }else if($var<=$var2){
                $dividendo = $var2;
                $divisor = $var;
                $cociente = 0;
                while($dividendo>=$divisor){
                    $dividendo -= $divisor;
                    $cociente += 1;
                }
                echo "<br> Division de ".$var2."/".$var;
                echo "<br> Cociente: ".$cociente;
                echo "<br> Resto: ".$dividendo;
            }

            ?>
        </article>
    </section>
    
</body>
</html>