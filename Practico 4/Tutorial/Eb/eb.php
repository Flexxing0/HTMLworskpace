<!DOCTYPE html>
<html lang="es">
<head>
    <title>Ejercicio B</title>
</head>
<body>
    <header><h1>Operacions con Strings en PHP</h1></header>
    <section>
        <article>
            <?php
            $var = "Palabra";
            echo "La longitud de \"".$var."\" es: Longitud ".Strlen($var);
            echo "<br>\"".$var. "\" en mayuscula : ". Strtoupper($var);
            echo "<br> La cantidad de veces que se repite 'a' en \"".$var."\" es : ". Substr_count($var,"a");
            echo "<br>\"".$var."\" invertida : ". Strrev($var);
            ?>
        </article>
    </section>
</body>
</html>