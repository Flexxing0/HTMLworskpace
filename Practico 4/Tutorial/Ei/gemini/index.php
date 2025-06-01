<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cálculo de Figuras Geométricas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Calculadora de Figuras Geométricas</h1>
        <form action="calcular.php" method="post">
            <div class="form-group">
                <label for="figura">Seleccione una figura:</label>
                <select id="figura" name="figura" onchange="mostrarCampos()">
                    <option value="">-- Seleccione --</option>
                    <option value="cuadrado">Cuadrado</option>
                    <option value="rectangulo">Rectángulo</option>
                    <option value="circulo">Círculo</option>
                    <option value="cubo">Cubo (para Volumen)</option>
                </select>
            </div>

            <div id="campos_cuadrado" class="form-group" style="display: none;">
                <label for="lado_cuadrado">Lado del Cuadrado:</label>
                <input type="number" step="0.01" id="lado_cuadrado" name="lado_cuadrado" placeholder="Ej: 5.00">
            </div>

            <div id="campos_rectangulo" class="form-group" style="display: none;">
                <label for="largo_rectangulo">Largo del Rectángulo:</label>
                <input type="number" step="0.01" id="largo_rectangulo" name="largo_rectangulo" placeholder="Ej: 8.00">
                <label for="ancho_rectangulo">Ancho del Rectángulo:</label>
                <input type="number" step="0.01" id="ancho_rectangulo" name="ancho_rectangulo" placeholder="Ej: 6.00">
            </div>

            <div id="campos_circulo" class="form-group" style="display: none;">
                <label for="radio_circulo">Radio del Círculo:</label>
                <input type="number" step="0.01" id="radio_circulo" name="radio_circulo" placeholder="Ej: 3.50">
            </div>

            <div id="campos_cubo" class="form-group" style="display: none;">
                <label for="lado_cubo">Lado del Cubo:</label>
                <input type="number" step="0.01" id="lado_cubo" name="lado_cubo" placeholder="Ej: 4.00">
            </div>

            <button type="submit">Calcular</button>
        </form>

        <script>
            // Función JavaScript para mostrar/ocultar campos según la figura seleccionada
            function mostrarCampos() {
                const figuraSeleccionada = document.getElementById('figura').value;
                const camposCuadrado = document.getElementById('campos_cuadrado');
                const camposRectangulo = document.getElementById('campos_rectangulo');
                const camposCirculo = document.getElementById('campos_circulo');
                const camposCubo = document.getElementById('campos_cubo');

                // Ocultar todos los campos primero
                camposCuadrado.style.display = 'none';
                camposRectangulo.style.display = 'none';
                camposCirculo.style.display = 'none';
                camposCubo.style.display = 'none';

                // Mostrar los campos relevantes
                if (figuraSeleccionada === 'cuadrado') {
                    camposCuadrado.style.display = 'block';
                } else if (figuraSeleccionada === 'rectangulo') {
                    camposRectangulo.style.display = 'block';
                } else if (figuraSeleccionada === 'circulo') {
                    camposCirculo.style.display = 'block';
                } else if (figuraSeleccionada === 'cubo') {
                    camposCubo.style.display = 'block';
                }
            }
        </script>
    </div>
</body>
</html>