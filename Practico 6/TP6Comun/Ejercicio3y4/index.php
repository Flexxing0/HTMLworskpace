<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <section>
        <article>
            <?php
            include 'funciones.php';
            selectProductos();
            echo "<div id='contenedor-paises' class='.oculto'>";
            selectPaises();
            echo"</div>";
            ?>
        </article>
    </section>
    <script src="script.js"></script>
</body>
</html>