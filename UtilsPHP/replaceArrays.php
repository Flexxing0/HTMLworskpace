<?php

// La función preg_replace() en PHP es una herramienta extremadamente potente y flexible
// para buscar y reemplazar texto en cadenas. A diferencia de str_replace() que busca
// y reemplaza cadenas literales, preg_replace() utiliza expresiones regulares (regex)
// para definir el patrón de búsqueda.

// ¿Qué es una Expresión Regular (Regex)?
// Una expresión regular es una secuencia de caracteres que forma un patrón de búsqueda.
// Es como un lenguaje mini-programación para describir conjuntos de cadenas de caracteres.
// Permiten buscar no solo texto exacto, sino también patrones de texto (por ejemplo,
// "todos los números", "cualquier letra mayúscula", "cadenas que empiecen con 'A' y terminen con 'Z'").

// ---

// Sintaxis Básica de preg_replace()
// mixed preg_replace ( mixed $pattern , mixed $replacement , mixed $subject [, int $limit = -1 [, int &$count ]] )

// $pattern: La expresión regular a buscar. Debe estar delimitada por caracteres como /, #, ~, !, etc. (ej. /patron/).
// $replacement: La cadena por la que se reemplazará la subcadena encontrada por el patrón. Puede ser una cadena simple o puede incluir referencias a los grupos capturados en el patrón.
// $subject: La cadena o array de cadenas en la que se realizará la búsqueda y el reemplazo.
// $limit (opcional): El número máximo de reemplazos a realizar. Por defecto es -1 (todos).
// $count (opcional): Una variable que se llenará con el número de reemplazos realizados.

// ---

// Ejemplo Simple: Eliminar Espacios (como con str_replace())
// Aunque str_replace() es más eficiente para espacios simples, preg_replace() también puede hacerlo:

$texto = "Hola  Mundo con  varios espacios";
$texto_sin_espacios = preg_replace('/\s+/', '', $texto);
// \s significa cualquier carácter de espacio en blanco (espacio, tab, salto de línea, etc.)
// + significa "uno o más" de la ocurrencia anterior.
// Entonces, '/\s+/' significa "uno o más caracteres de espacio en blanco".

echo "Ejemplo 1 (Eliminar Espacios):\n";
echo "Original: \"$texto\"\n";
echo "Resultado: \"$texto_sin_espacios\"\n\n"; // Salida: HolaMundoconvariosespacios

// ---

// Ejemplos Comunes y Útiles de preg_replace()

// 1. Eliminar Caracteres No Numéricos (para obtener solo dígitos)
// Este es un caso de uso muy común, especialmente cuando quieres asegurarte de que un input
// solo contenga números (teléfonos, IDs, etc.).

$input = "123-abc-45.678 (90)";
$solo_numeros = preg_replace('/[^0-9]/', '', $input);
// Explicación del patrón '/[^0-9]/'
// /.../ : Delimitadores de la expresión regular.
// [^...] : Corchetes para un conjunto de caracteres. El ^ al principio del conjunto significa "cualquier carácter QUE NO ESTÉ en este conjunto".
// 0-9 : Rango de caracteres, representa cualquier dígito del 0 al 9.
// En resumen: "cualquier carácter que no sea un dígito del 0 al 9".

echo "Ejemplo 2 (Eliminar Caracteres No Numéricos):\n";
echo "Original: \"$input\"\n";
echo "Resultado: \"$solo_numeros\"\n\n"; // Salida: 1234567890

// 2. Eliminar Caracteres No Alfabéticos (para obtener solo letras)

$frase = "¡Hola, mundo 123! ¿Cómo estás?";
$solo_letras = preg_replace('/[^a-zA-Z]/', '', $frase);
// [^a-zA-Z] : Cualquier carácter que no sea una letra minúscula (a-z) ni mayúscula (A-Z).

echo "Ejemplo 3 (Eliminar Caracteres No Alfabéticos):\n";
echo "Original: \"$frase\"\n";
echo "Resultado: \"$solo_letras\"\n\n"; // Salida: HolamundoCmoests

// 3. Reemplazar Múltiples Espacios por Uno Solo

$texto_con_muchos_espacios = "Este   texto    tiene    muchos   espacios.";
$texto_limpio = preg_replace('/\s+/', ' ', $texto_con_muchos_espacios);
// \s+ : Uno o más caracteres de espacio en blanco.
// ' ' : Reemplazar por un solo espacio.

echo "Ejemplo 4 (Reemplazar Múltiples Espacios):\n";
echo "Original: \"$texto_con_muchos_espacios\"\n";
echo "Resultado: \"$texto_limpio\"\n\n"; // Salida: Este texto tiene muchos espacios.

// 4. Reemplazar Etiquetas HTML Específicas

$html = "<p>Un párrafo.</p><span>Un span.</span><p>Otro párrafo.</p>";
$html_sin_parrafos = preg_replace('/<p>(.*?)<\/p>/', '<div>$1</div>', $html);
// <p>(.*?)<\/p> : Busca <p>...</p>
// (.*?) : Captura cualquier carácter (.*) de forma no codiciosa (?). Esto es un un "grupo de captura".
// \$1 : En el reemplazo, se refiere al contenido capturado por el primer grupo de captura.

echo "Ejemplo 5 (Reemplazar Etiquetas HTML):\n";
echo "Original: \"$html\"\n";
echo "Resultado: \"$html_sin_parrafos\"\n\n"; // Salida: <div>Un párrafo.</div><span>Un span.</span><div>Otro párrafo.</div>

// ---

// Modificadores de Expresiones Regulares
// Puedes añadir modificadores después del delimitador final del patrón para cambiar el comportamiento de la búsqueda:

// i (case-insensitive): Ignora mayúsculas y minúsculas.
echo "Ejemplo 6 (Modificador 'i' - Case-Insensitive):\n";
echo "Resultado: \"" . preg_replace('/hola/i', 'ADIOS', 'Hola mundo, hola otra vez') . "\"\n\n"; // Salida: ADIOS mundo, ADIOS otra vez

// g (global - implícito en preg_replace):
// En muchas otras lenguas el modificador 'g' es para todos los reemplazos,
// en PHP preg_replace lo hace por defecto. Si quieres solo el primer reemplazo,
// usarías el parámetro $limit.

// m (multiline): El ^ y $ coinciden con el inicio/fin de línea, no solo el inicio/fin del string.

// s (dotall): El . (punto) coincide con cualquier carácter, incluyendo saltos de línea.

// ---

// ¿Cuándo usar preg_replace() vs str_replace()?
// str_replace(): Ideal para reemplazos de cadenas literales simples, donde sabes exactamente
// lo que buscas. Es más rápido para estos casos.
// preg_replace(): Indispensable cuando necesitas buscar patrones complejos, no solo cadenas exactas.
// Útil para validación, limpieza de datos, extracción de información, etc. Es más lento que
// str_replace() debido a la complejidad del análisis de expresiones regulares.

// preg_replace() es una de las funciones más poderosas en PHP para la manipulación de cadenas,
// pero requiere aprender la sintaxis de las expresiones regulares, que puede ser un poco densa al principio.
// Sin embargo, dominarla te abre un mundo de posibilidades en el manejo de texto.

?>