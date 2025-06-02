<!DOCTYPE html>
<html lang="es">
<head>
    <title></title>
    <link rel="stylesheet" href="style.css">
    <script type="text/javascript" src="script.js"></script>
</head>
<body>
    <header>Simon dice</header>
    <main>
        <form action="manejoJuego.php" method="POST">
            <label for="usuario">Usuario </label>
            <input type="text" id="usuario" name="usuario" placeholder="Ingresa nombre de usuario" required autocomplete="on">
            <label for="cantidad">Ingrese una cantidad de secuencias </label>
            <input type="number" id="cantidad" name="cantidad" min="1" max="10" placeholder="Ejemplo: 2">
            <input type="submit" value="Comenzar Juego">
        </form>
    </main>
    <footer></footer>
</body>
</html>