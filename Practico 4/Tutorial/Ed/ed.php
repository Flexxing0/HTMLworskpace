<!DOCTYPE html>
<html lang="en">

<head>
    <title>Tablas de multiplicar</title>
</head>

<body>
    <header>
        <h1>Tablas de multiplicar</h1>
    </header>
    <section>
        <article>
            <?php
            $contador = 0;
            do {
                if($contador!==0){
                $ths = "<br><table><tr>";
                for ($i = 0; $i < 11; $i++) {
                    if ($i === 0) {
                        $ths .= "<th>*</th>";
                    } else {
                        $ths .= "<th>" . $i . "</th>";
                    }
                }
                $ths .= "</tr>";
                $ths .= "<tr>";
                for ($i = 0; $i < 11; $i++) {
                    if ($i === 0) {
                        $ths .= "<td>" . $contador . "</td>";
                    } else {
                        $ths .= "<td>" . $i * $contador . "</td>";
                    }
                }
                $contador+=1;
                echo "Tabla de multiplicar del: " . $contador;
                echo $ths;
            }
            } while ($contador < 10);
            ?>
        </article>
    </section>

</body>

</html>