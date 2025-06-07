<?php

/**
 * Genera ejemplos de listas HTML (ordenadas y no ordenadas)
 * con sus elementos y atributos más comunes.
 */
function generarEjemplosDeListas() {

    // --- 1. Lista No Ordenada (<ul>) ---
    // Usada para agrupar ítems que no tienen un orden secuencial particular.
    echo '<h3>Lista No Ordenada (ul)</h3>';
    echo '<p>Elementos que no necesitan un orden específico (por ejemplo, ingredientes de una receta).</p>';
    
    // Atributos de <ul>:
    // 'id' y 'class' son atributos globales muy comunes para estilizar con CSS o manipular con JavaScript.
    echo '<ul id="ingredientes" class="lista-ingredientes">';
        // <li>: Elemento de lista. Contiene el contenido de cada ítem.
        // También puede tener atributos globales como 'id', 'class', 'style', etc.
        echo '<li class="item-ingrediente">Harina</li>';
        echo '<li class="item-ingrediente">Azúcar</li>';
        echo '<li class="item-ingrediente">Huevos</li>';
        echo '<li class="item-ingrediente">Leche</li>';
    echo '</ul>';

    // --- 2. Lista Ordenada (<ol>) ---
    // Usada para agrupar ítems que sí tienen un orden secuencial o jerárquico (por ejemplo, pasos de una receta).
    echo '<h3>Lista Ordenada (ol)</h3>';
    echo '<p>Pasos que deben seguirse en un orden específico (por ejemplo, instrucciones).</p>';

    // Atributos de <ol>:
    // - 'type': Define el tipo de marcador (1, a, A, i, I).
    // - 'start': Define el valor inicial de la lista.
    // - 'reversed': Invierte el orden de la numeración (descendente).
    echo '<ol type="1" start="1" id="pasosReceta" class="lista-pasos">';
        echo '<li>Mezclar los ingredientes secos.</li>';
        echo '<li>Añadir los líquidos gradualmente.</li>';
        echo '<li>Hornear a 180°C por 30 minutos.</li>';
    echo '</ol>';

    echo '<hr>'; // Separador

    // --- 3. Lista Ordenada con 'type' y 'start' diferentes ---
    echo '<h3>Lista Ordenada con tipo y inicio personalizados</h3>';
    echo '<p>Ejemplo de lista que inicia en un valor diferente y usa letras mayúsculas.</p>';
    echo '<ol type="A" start="5">'; // Inicia con 'E' (quinta letra del alfabeto)
        echo '<li>Primer punto (E)</li>';
        echo '<li>Segundo punto (F)</li>';
        echo '<li>Tercer punto (G)</li>';
    echo '</ol>';

    echo '<hr>';

    // --- 4. Lista Ordenada Invertida ---
    echo '<h3>Lista Ordenada Invertida</h3>';
    echo '<p>Ejemplo de lista numerada de forma descendente.</p>';
    echo '<ol type="i" start="3" reversed>'; // Inicia en 'iii' y cuenta hacia atrás
        echo '<li>Último en el orden original (iii)</li>';
        echo '<li>Penúltimo (ii)</li>';
        echo '<li>Antepenúltimo (i)</li>';
    echo '</ol>';
    
    // --- 5. Anidamiento de Listas ---
    // Las listas se pueden anidar unas dentro de otras para crear sub-listas.
    echo '<h3>Anidamiento de Listas</h3>';
    echo '<p>Puedes crear sub-listas anidando <code>&lt;ul&gt;</code> o <code>&lt;ol&gt;</code> dentro de un <code>&lt;li&gt;</code>.</p>';
    echo '<ul>';
        echo '<li>Frutas';
            echo '<ul>'; // Sub-lista no ordenada
                echo '<li>Manzana</li>';
                echo '<li>Banana</li>';
            echo '</ul>';
        echo '</li>';
        echo '<li>Verduras';
            echo '<ol>'; // Sub-lista ordenada
                echo '<li>Lechuga</li>';
                echo '<li>Tomate</li>';
            echo '</ol>';
        echo '</li>';
    echo '</ul>';
}

// Llama a la función para generar las listas
generarEjemplosDeListas();

// Opcional: CSS para estilizar las listas generadas
echo '
<style>
  body {
    font-family: Arial, sans-serif;
    margin: 20px;
  }
  h3 {
    color: #0056b3;
    margin-top: 30px;
  }
  ul, ol {
    margin-left: 20px;
    padding-left: 0;
  }
  li {
    margin-bottom: 5px;
    padding: 3px 0;
  }
  /* Estilos específicos para la lista de ingredientes */
  .lista-ingredientes {
    list-style-type: square; /* Cambia el marcador a cuadrado */
    color: #333;
  }
  .item-ingrediente {
    background-color: #f0f8ff;
    border-left: 3px solid #add8e6;
    padding-left: 10px;
  }
  /* Estilos para la lista de pasos */
  .lista-pasos {
    color: #555;
    font-weight: bold;
  }
  .lista-pasos li {
    background-color: #f9f9f9;
    padding: 5px 10px;
    border-bottom: 1px dashed #eee;
  }
</style>
';

?>