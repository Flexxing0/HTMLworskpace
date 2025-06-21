<!DOCTYPE html>
<html lang="es">
<head>
    <title>Document</title>
</head>
<body>
    <section>
        <article>
            <?php
            require_once 'funciones.php';
            $con = new mysqli("localhost","root","","inmobiliaria");
            $query = "SELECT * FROM inmueble LIMIT 0, 30 ";
            $resultado = $con->query($query);
            if ($resultado->num_rows>0){
                creaTabla($resultado);
                $resultado->free();
            } else {
                echo "<p>NO SE ENCONTRARON DATOS</p>";
            }
            $con->close();
            ?>
        </article>
    </section>
    
</body>
</html>