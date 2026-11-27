# Manual del Usuario – Adivine la palabra (B)

**Universidad Nacional de la Patagonia San Juan Bosco – Facultad de Ingeniería**  
**Materia:** Laboratorio de Programación y Lenguajes  
**Instancia:** Ejercicio integrador para examen final regular  
**Alumno:** Félix Aldo TITOS  

---

## 1. Introducción

**Adivine la palabra (B)** es una aplicación web de entretenimiento en línea que permite a usuarios registrados jugar a descubrir palabras ocultas de distintas categorías y niveles de dificultad, acumulando puntos para competir en un ranking **Top 10**. Además, dispone de un **Panel de Administración** exclusivo para gestionar el catálogo de categorías y palabras del juego.

### Requisitos para el usuario
- Navegador web actualizado (Google Chrome, Mozilla Firefox o Microsoft Edge) con JavaScript y cookies habilitados.
- Acceso a la URL donde se encuentra publicada la aplicación en el servidor local WAMP (por ejemplo: `http://localhost/adivina_palabra/`).

---

## 2. Guía para el Jugador

### 2.1 Registro de una nueva cuenta (`registro.php`)

Para poder jugar es obligatorio crear una cuenta:

1. En la pantalla de inicio (`index.php`), hacer clic en el enlace **Registrate para jugar**.
2. Completar el formulario **Crear cuenta**:
   - **Nombre de usuario (único):** entre 3 y 30 caracteres (letras sin tilde, números o guion bajo `_`). No se puede registrar el nombre reservado `administrador`. Al salir del campo, el sistema consulta automáticamente por AJAX si el nombre está disponible o ya está ocupado.
   - **Correo electrónico:** dirección válida (se permite reutilizar un correo electrónico en distintas cuentas).
   - **Contraseña y Confirmar contraseña:** mínimo 6 caracteres y máximo 64; ambos campos deben coincidir.
3. Hacer clic en **Registrarme**. Si los datos son válidos, el sistema redirige al inicio de sesión con un mensaje de confirmación.

`[CAPTURA 1: Formulario de registro (registro.php) mostrando la verificación asíncrona AJAX de nombre de usuario disponible]`

---

### 2.2 Inicio de sesión (`index.php`)

1. Ingresar **Nombre de usuario** y **Contraseña**.
2. Presionar **Ingresar**.
3. Si las credenciales corresponden a un jugador registrado, se accede al **Menú principal** (`menu.php`). Si corresponden al administrador, el sistema redirige automáticamente al **Panel de Administración** (`admin/index.php`).

`[CAPTURA 2: Pantalla de inicio de sesión (index.php)]`

---

### 2.3 Menú principal y mensaje de bienvenida (`menu.php`)

Al iniciar sesión como jugador, la pantalla muestra:

1. **Barra de navegación superior:** enlaces a **Menu principal**, **Top 10**, **Partida en curso** (si existe una activa), nombre de usuario con puntaje acumulado actual y botón **Cerrar sesion**.
2. **Tarjeta de bienvenida personalizada:**
   - Saludo con `@usuario`.
   - Fecha relativa de la última partida jugada (*hoy*, *ayer*, *hace N días* hasta 30 días, o *el dd/mm/yyyy* si pasaron más de 30 días).
   - Texto de aliento con el porcentaje de partidas ganadas sobre el total de partidas jugadas (si el jugador todavía no disputó ninguna partida, se muestra una invitación especial sin porcentaje).
   - Resumen de puntaje acumulado, partidas jugadas y partidas ganadas.
3. **Aviso de partida en curso (si corresponde):** si el jugador salió sin finalizar una partida anterior, aparece una tarjeta para **Continuar partida en curso** o **Abandonar partida actual** antes de configurar una nueva.
4. **Configurar nueva partida:**
   - **Categoría de la palabra:** lista desplegable con las categorías activas y su cantidad de palabras disponibles.
   - **Nivel de dificultad:**
     - **Baja:** palabras de 3 a 5 letras (multiplicador `x1`).
     - **Media:** palabras de 6 a 8 letras (multiplicador `x2`).
     - **Alta:** palabras de 9 o más letras (multiplicador `x3`).
   - **Límite de tiempo:** *Sin tiempo*, *1 minuto*, *3 minutos* o *5 minutos*.
   - Al presionar **Comenzar partida**, el sistema selecciona al azar una palabra de la base de datos que cumpla la categoría y dificultad elegidas (si la combinación no tiene palabras disponibles, muestra un aviso claro para probar otra). Las preferencias de dificultad y tiempo quedan recordadas en el navegador.

`[CAPTURA 3: Menú principal del jugador (menu.php) mostrando la tarjeta de bienvenida personalizada y el formulario de configuración de partida]`

---

### 2.4 Desarrollo de la partida (`partida.php`)

Durante toda la partida el jugador visualiza:

- **Indicadores permanentes:** categoría, dificultad (y su multiplicador), cantidad de letras, tiempo transcurrido, tiempo restante (o *Sin limite*), barra de progreso temporal (cuando la partida tiene límite), intentos realizados, letras adivinadas y pistas mostradas.
- **Palabra enmascarada:** cada letra oculta aparece representada con `*`.
- **Letras arriesgadas:** historial de todas las letras ya intentadas.
- **Controles de juego:** campo para escribir una letra y botón **Arriesgar letra**, teclado virtual interactivo (`A`–`Z` y `Ñ`), y botón **Abandonar e iniciar una nueva**.

#### Reglas durante la partida
- Se arriesga una sola letra por vez (`A`–`Z` o `Ñ`, sin tildes).
- **Letra acertada:** se revela en todas las posiciones donde aparezca en la palabra, suma **1 punto** (aunque se repita varias veces) y el jugador conserva el turno.
- **Letra no acertada:** la aplicación revela automáticamente una **pista**, que corresponde a la **primera letra de la palabra que todavía esté oculta**, descubriéndola en todas sus apariciones de izquierda a derecha, y suma **1 pista**.
- **Letra repetida o ya descubierta por pista:** se muestra un aviso y no suma intento, punto ni pista.
- **Abandonar partida:** solicita confirmación; si se confirma, la partida termina inmediatamente, se guarda como **abandonada (perdida)** y acumula **0 puntos**.
- **Recarga de página (F5):** si se recarga el navegador durante una partida en curso, el estado y el tiempo transcurrido se recuperan intactos desde el servidor.

`[CAPTURA 4: Pantalla de partida en curso (partida.php) mostrando casillas descubiertas y ocultas, indicadores, teclado virtual y barra de tiempo]`

---

### 2.5 Resultado final y Top 10 (`partida.php` y `ranking.php`)

La partida finaliza cuando se descubren todas las letras de la palabra (por acierto o por pista) o cuando se agota el tiempo límite.

- **Evaluación del resultado:**
  - **Ganada:** `letras adivinadas > pistas mostradas`.
  - **Empate:** `letras adivinadas == pistas mostradas`.
  - **Perdida:** `letras adivinadas < pistas mostradas` (o abandono voluntario).
  - Si se agota el tiempo con la palabra incompleta, se informa el vencimiento del tiempo y se aplica la misma regla de comparación al momento del corte.
- **Puntaje acumulado:** si la partida resultó **ganada** o **empate**, se acumula `puntaje de la partida × multiplicador` (`x1` baja, `x2` media, `x3` alta). Si resultó perdida o abandonada, acumula `0`.
- **Cierre automático y Top 10:** al terminar, el sistema guarda todos los datos de la partida y actualiza el puntaje en la base de datos, revela la palabra completa, muestra el detalle del resultado y despliega automáticamente debajo el **Top 10 de jugadores con más puntaje acumulado**, resaltando en verde la fila del jugador actual (o informando su puntaje si se encuentra fuera del Top 10).
- Desde la barra superior o el botón **Ver pantalla completa del Top 10** también se puede consultar [ranking.php](../ranking.php) en cualquier momento.

`[CAPTURA 5: Resultado final de una partida en partida.php junto con la tabla Top 10 cargada automáticamente por AJAX]`

`[CAPTURA 6: Pantalla dedicada de Top 10 (ranking.php) mostrando el resumen de la última partida y el ranking con el jugador destacado]`

---

### 2.6 Cierre de sesión (`logout.php`)

Hacer clic en **Cerrar sesion** en la esquina superior derecha. Si el jugador tenía una partida activa sin terminar, el sistema la registra como abandonada (0 puntos) antes de cerrar la sesión y volver a `index.php`.

---

## 3. Guía para el Administrador

### 3.1 Acceso al Panel de Administración (`admin/index.php`)

1. En `index.php`, iniciar sesión con las credenciales fijas del administrador:
   - **Usuario:** `administrador`
   - **Contraseña:** `@adminJuegoPalabras#5555`
2. El sistema redirige al **Panel de Administracion** (`admin/index.php`), donde se visualiza un resumen de categorías activas, palabras activas por dificultad (Baja, Media, Alta), jugadores registrados y partidas jugadas, junto con los accesos a **ABM Categorias** y **ABM Palabras**.
3. Por separación de roles, el administrador no puede acceder a las páginas de juego ni un jugador puede acceder al panel de administración.

`[CAPTURA 7: Panel principal del administrador (admin/index.php)]`

---

### 3.2 ABM de Categorías (`admin/categorias.php`)

- **Alta:** escribir el nombre de la categoría (2 a 50 caracteres) y hacer clic en **Guardar categoria**. Antes de guardar se puede cancelar regresando con **Cancelar / Volver**. Si el nombre correspondía a una categoría dada de baja previamente, el sistema la reactiva automáticamente.
- **Modificación:** hacer clic en **Modificar** en la fila deseada, editar el nombre y presionar **Guardar cambios** (o **Cancelar edicion** para descartar).
- **Baja lógica:** hacer clic en **Dar de baja** y confirmar el diálogo. **Solo se permite dar de baja una categoría cuando tiene 0 palabras activas asociadas**; si tiene palabras asociadas, el botón aparece deshabilitado y el servidor también bloquea la operación. Las bajas son siempre lógicas (no se borran registros físicamente para preservar el historial de partidas).

`[CAPTURA 8: Pantalla ABM de Categorías (admin/categorias.php) mostrando el listado con conteo de palabras y el control de baja]`

---

### 3.3 ABM de Palabras (`admin/palabras.php`)

- **Requisito previo:** debe existir al menos una categoría activa; si no hay categorías activas, el formulario permanece deshabilitado indicando que primero debe crearse una categoría.
- **Alta:** seleccionar la categoría, ingresar la palabra (solo letras `A`–`Z` y `Ñ`, sin tildes ni espacios, entre 3 y 50 letras) y presionar **Guardar palabra**. Mientras se escribe, la interfaz muestra en tiempo real la cantidad de letras y la dificultad asignada automáticamente (`Baja`, `Media` o `Alta`). Antes de guardar se puede regresar con **Cancelar / Volver**. No se permiten palabras repetidas dentro de la misma categoría.
- **Filtrado:** se puede filtrar el listado de palabras activas por **Categoría** y/o **Dificultad** usando el bloque **Filtrar listado**.
- **Modificación:** presionar **Modificar** sobre una palabra, ajustar texto y/o categoría y presionar **Guardar cambios** (la longitud y dificultad se recalculan automáticamente).
- **Baja lógica:** presionar **Dar de baja** y confirmar. La palabra deja de sortearse en nuevas partidas pero se conserva en la base de datos para mantener intactas las partidas históricas. Si vuelve a cargarse la misma palabra en la misma categoría, se reactiva automáticamente.

`[CAPTURA 9: Pantalla ABM de Palabras (admin/palabras.php) con vista previa de dificultad, filtros y tabla de palabras activas]`

---

## 4. Mensajes frecuentes del sistema

| Mensaje | Motivo | Cómo proceder |
| :--- | :--- | :--- |
| *El nombre de usuario ya esta ocupado* | Otro jugador ya registró ese nombre (o es `administrador`). | Elegir otro nombre de usuario único. |
| *No hay palabras disponibles para esa categoria y dificultad* | La categoría elegida no posee palabras activas en el rango de letras del nivel seleccionado. | Elegir otra categoría o dificultad, o cargar palabras desde el panel administrador. |
| *Tenes una partida en curso* | El jugador volvió al menú sin finalizar una partida abierta. | Hacer clic en **Continuar partida en curso** o en **Abandonar partida actual**. |
| *Tu sesion expiro por seguridad* | Se superó el límite de inactividad (30 min) o el tiempo absoluto de sesión (4 h). | Volver a iniciar sesión en `index.php`. |
| *No se puede dar de baja la categoria porque tiene X palabra(s) activa(s)* | Se intentó dar de baja una categoría con palabras aún activas. | Dar de baja o reasignar primero las palabras activas de esa categoría. |
