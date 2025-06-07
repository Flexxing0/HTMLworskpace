Elementos Principales de una Tabla:
<table>: El elemento contenedor principal para toda la tabla.

Atributos Comunes (Globales):
id: Identificador único para el elemento.
class: Una o más clases CSS para aplicar estilos.
style: Estilos CSS en línea (no recomendado para estilos complejos o reutilizables).
title: Información adicional (tooltip al pasar el ratón).
data-*: Atributos de datos personalizados para JavaScript.
Atributos Obsoletos (HTML5 desaconseja, usar CSS):
align: Alineación de la tabla.
bgcolor: Color de fondo.
border: Ancho del borde.
cellpadding: Espacio entre el contenido y el borde de la celda.
cellspacing: Espacio entre celdas.
width: Ancho de la tabla.
summary: Descripción breve del propósito de la tabla (usar <figcaption> o atributos ARIA en su lugar).
<caption>: Define un título o descripción para la tabla. Debe ser el primer hijo de <table>.

Atributos Comunes (Globales): id, class, style, etc.
<colgroup>: Especifica un grupo de una o más columnas para aplicar estilos o semántica común.

Atributos Comunes (Globales): id, class, style, etc.
Atributos Específicos:
span: Indica cuántas columnas consecutivas abarca el grupo.
Atributos Obsoletos (HTML5 desaconseja, usar CSS):
align: Alineación horizontal.
bgcolor: Color de fondo.
valign: Alineación vertical.
width: Ancho de las columnas en el grupo.
<col>: Define las propiedades de una columna dentro de un <colgroup>. Un <colgroup> puede contener múltiples <col>.

Atributos Comunes (Globales): id, class, style, etc.
Atributos Específicos:
span: Indica cuántas columnas consecutivas afecta este <col> (si no está dentro de un <colgroup> con span).
Atributos Obsoletos (HTML5 desaconseja, usar CSS):
align, bgcolor, valign, width.
<thead>: Agrupa el contenido de la cabecera de la tabla.

Debe contener una o más filas (<tr>).
Atributos Comunes (Globales): id, class, style, etc.
Atributos Obsoletos (HTML5 desaconseja, usar CSS): align, bgcolor, valign.
<tbody>: Agrupa el contenido principal (cuerpo) de la tabla.

Debe contener una o más filas (<tr>).
Atributos Comunes (Globales): id, class, style, etc.
Atributos Obsoletos (HTML5 desaconseja, usar CSS): align, bgcolor, valign.
<tfoot>: Agrupa el contenido del pie de la tabla.

Debe contener una o más filas (<tr>).
Atributos Comunes (Globales): id, class, style, etc.
Atributos Obsoletos (HTML5 desaconseja, usar CSS): align, bgcolor, valign.
<tr>: Define una fila en la tabla.

Atributos Comunes (Globales): id, class, style, etc.
Atributos Obsoletos (HTML5 desaconseja, usar CSS):
align: Alineación horizontal del contenido de las celdas en la fila.
bgcolor: Color de fondo de la fila.
valign: Alineación vertical del contenido de las celdas en la fila.
<th>: Define una celda de encabezado en la tabla. Por defecto, el contenido es en negrita y centrado.

Debe estar dentro de un <tr>.
Atributos Comunes (Globales): id, class, style, etc.
Atributos Específicos:
colspan: Indica cuántas columnas debe abarcar esta celda.
rowspan: Indica cuántas filas debe abarcar esta celda.
scope: Especifica si la celda es un encabezado para una col (columna), row (fila), colgroup, o rowgroup. Muy importante para accesibilidad.
abbr: Versión abreviada del contenido del encabezado (para lectores de pantalla).
headers: (Para <td> también) Una lista de IDs de celdas de encabezado a las que se relaciona esta celda de datos.
Atributos Obsoletos (HTML5 desaconseja, usar CSS): align, bgcolor, valign, width, height.
<td>: Define una celda de datos en la tabla.

Debe estar dentro de un <tr>.
Atributos Comunes (Globales): id, class, style, etc.
Atributos Específicos:
colspan: Indica cuántas columnas debe abarcar esta celda.
rowspan: Indica cuántas filas debe abarcar esta celda.
headers: (Ver <th>)
Atributos Obsoletos (HTML5 desaconseja, usar CSS): align, bgcolor, valign, width, height.