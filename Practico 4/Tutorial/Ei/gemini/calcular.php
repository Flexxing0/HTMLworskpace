<?php

// Función para validar un número positivo (puede ser flotante)
function validarNumeroPositivo($valor, $nombreCampo) {
    if (!isset($valor) || $valor === '') {
        return "El campo '{$nombreCampo}' es obligatorio.";
    }
    if (!is_numeric($valor)) {
        return "El campo '{$nombreCampo}' debe ser un valor numérico.";
    }
    if ((float)$valor <= 0) {
        return "El campo '{$nombreCampo}' debe ser un número positivo.";
    }
    return true; // La validación pasó
}

$figura = $_POST['figura'] ?? '';
$errores = [];
$resultados = [];

// Validación de la figura seleccionada
if (empty($figura)) {
    $errores[] = "Debe seleccionar una figura geométrica.";
}

// Procesar según la figura seleccionada y validar los valores introducidos [cite: 4]
switch ($figura) {
    case 'cuadrado':
        $lado = $_POST['lado_cuadrado'] ?? '';
        $validacion_lado = validarNumeroPositivo($lado, "Lado del Cuadrado");
        if ($validacion_lado !== true) {
            $errores[] = $validacion_lado;
        } else {
            $lado = (float)$lado;
            // Perímetro: suma de las medidas de los lados [cite: 5]
            $resultados['perimetro'] = "Perímetro: " . (4 * $lado) . " unidades.";
            // Área: multiplicar la base por la altura [cite: 8] (lado * lado para un cuadrado)
            $resultados['area'] = "Área: " . ($lado * $lado) . " unidades cuadradas.";
            // Volumen: no aplica directamente para un cuadrado 2D. Podríamos considerar un "cubo" para volumen.
            $resultados['volumen'] = "Volumen: N/A para un cuadrado (es una figura 2D).";
        }
        break;

    case 'rectangulo':
        $largo = $_POST['largo_rectangulo'] ?? '';
        $ancho = $_POST['ancho_rectangulo'] ?? '';

        $validacion_largo = validarNumeroPositivo($largo, "Largo del Rectángulo");
        if ($validacion_largo !== true) {
            $errores[] = $validacion_largo;
        } else {
            $largo = (float)$largo;
        }

        $validacion_ancho = validarNumeroPositivo($ancho, "Ancho del Rectángulo");
        if ($validacion_ancho !== true) {
            $errores[] = $validacion_ancho;
        } else {
            $ancho = (float)$ancho;
        }

        if (empty($errores)) {
            // Perímetro: suma de las medidas de los lados [cite: 5] (2 * (largo + ancho))
            $resultados['perimetro'] = "Perímetro: " . (2 * ($largo + $ancho)) . " unidades.";
            // Área: multiplicar la base por la altura [cite: 8] (largo * ancho)
            $resultados['area'] = "Área: " . ($largo * $ancho) . " unidades cuadradas.";
            // Volumen: no aplica directamente para un rectángulo 2D.
            $resultados['volumen'] = "Volumen: N/A para un rectángulo (es una figura 2D).";
        }
        break;

    case 'circulo':
        $radio = $_POST['radio_circulo'] ?? '';
        $validacion_radio = validarNumeroPositivo($radio, "Radio del Círculo");
        if ($validacion_radio !== true) {
            $errores[] = $validacion_radio;
        } else {
            $radio = (float)$radio;
            // Perímetro (Circunferencia): 2 * PI * Radio
            $resultados['perimetro'] = "Circunferencia: " . (2 * M_PI * $radio) . " unidades.";
            // Área: PI * Radio^2
            $resultados['area'] = "Área: " . (M_PI * pow($radio, 2)) . " unidades cuadradas.";
            // Volumen: no aplica directamente para un círculo 2D.
            $resultados['volumen'] = "Volumen: N/A para un círculo (es una figura 2D).";
        }
        break;

    case 'cubo':
        $lado = $_POST['lado_cubo'] ?? '';
        $validacion_lado = validarNumeroPositivo($lado, "Lado del Cubo");
        if ($validacion_lado !== true) {
            $errores[] = $validacion_lado;
        } else {
            $lado = (float)$lado;
            // Perímetro: Un cubo no tiene un "perímetro" en el sentido 2D. Podríamos referirnos a la suma de aristas.
            $resultados['perimetro'] = "Perímetro (suma de aristas): " . (12 * $lado) . " unidades.";
            // Área (Área de la superficie total): 6 * lado^2
            $resultados['area'] = "Área de superficie: " . (6 * pow($lado, 2)) . " unidades cuadradas.";
            // Volumen: multiplicación de la altura por el ancho y por el largo [cite: 10] (lado * lado * lado)
            $resultados['volumen'] = "Volumen: " . pow($lado, 3) . " unidades cúbicas.";
        }
        break;

    default:
        // Este caso ya debería estar cubierto por la validación inicial de $figura vacía,
        // pero se mantiene por si se manipula el HTML.
        $errores[] = "Figura geométrica no válida.";
        break;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Cálculo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Resultados del Cálculo</h1>

        <?php if (!empty($errores)): ?>
            <div class="error-messages">
                <p>Se encontraron los siguientes errores:</p>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="window.history.back()">Volver al formulario</button>
            </div>
        <?php else: ?>
            <div class="results">
                <h2>Figura: <?php echo htmlspecialchars(ucfirst($figura)); ?></h2>
                <ul>
                    <?php foreach ($resultados as $tipo => $valor): ?>
                        <li><?php echo htmlspecialchars($valor); ?></li>
                    <?php endforeach; ?>
                </ul>
                <button onclick="window.history.back()">Realizar otro cálculo</button>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>