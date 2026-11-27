# Detalle de Páginas Principales y Estructura del Proyecto – Adivine la palabra (B)

**Universidad Nacional de la Patagonia San Juan Bosco – Facultad de Ingeniería**  
**Materia:** Laboratorio de Programación y Lenguajes  
**Alumno:** Félix Aldo TITOS  

---

## 1. Páginas web principales (Público y Jugador)

| Archivo | Acceso permitido | Función dentro de la aplicación |
| :--- | :--- | :--- |
| `index.php` | Público (si ya hay sesión válida, redirige según rol) | Página de entrada y formulario autoprocesado de **inicio de sesión**. Autentica tanto a jugadores registrados (tabla `usuarios`) como al administrador (`config/config.php`), regenera el ID de sesión y redirige a `menu.php` o `admin/index.php`. |
| `registro.php` | Público (si ya hay sesión válida, redirige según rol) | Formulario autoprocesado de **registro de nuevos jugadores**. Valida datos en cliente y servidor, consulta por AJAX la disponibilidad del nombre de usuario (`ajax/verificarUsuario.php`), hashea la contraseña con `password_hash()` y guarda el usuario en la BD. |
| `menu.php` | Solo **Jugador** (`Sesion::exigirJugador`) | Panel principal del jugador. Muestra el **mensaje personalizado de bienvenida** (último juego y porcentaje de partidas ganadas), gestiona partidas activas sin terminar (continuar o abandonar) y ofrece el formulario para configurar e iniciar una nueva partida (categoría, dificultad y tiempo), sorteando una palabra aleatoria en el servidor. |
| `partida.php` | Solo **Jugador** con partida activa en sesión | Pantalla interactiva del juego. Presenta los indicadores permanentes (categoría, dificultad, longitud, tiempos, barra `<progress>`, intentos, adivinadas, pistas), la palabra enmascarada con `*`, las letras arriesgadas, el ingreso de letra + teclado virtual y el botón de abandono. Al concluir la partida, oculta los controles, muestra el resultado con la palabra revelada y despliega automáticamente el **Top 10** cargado por AJAX. Nunca incluye la palabra oculta en el HTML mientras la partida está activa. |
| `ranking.php` | Solo **Jugador** (`Sesion::exigirJugador`) | Pantalla dedicada al **Top 10 de jugadores** con mayor puntaje acumulado (desempate alfabético por nombre de usuario). Destaca la fila del jugador autenticado, informa su puntaje y puesto actual y, si recién finalizó una partida en la sesión, exhibe un resumen compacto encima de la tabla. |
| `logout.php` | Jugador o Administrador | Cierra de forma segura la sesión activa. Si un jugador cierra sesión manteniendo una partida inconclusa, primero la registra como **abandonada (0 puntos)** en la BD; luego limpia `$_SESSION`, invalida la cookie de sesión, destruye la sesión y redirige a `index.php?salir=ok`. |

---

## 2. Páginas web del Panel de Administración

| Archivo | Acceso permitido | Función dentro de la aplicación |
| :--- | :--- | :--- |
| `admin/index.php` | Solo **Administrador** (`Sesion::exigirAdmin`) | Tablero principal de administración. Muestra estadísticas generales del sistema (categorías activas, palabras activas totales y por dificultad Baja/Media/Alta, jugadores y partidas) y los accesos directos a los ABM. |
| `admin/categorias.php` | Solo **Administrador** (`Sesion::exigirAdmin`) | Página autoprocesada para el **ABM de categorías**: alta (con reactivación automática si existía dada de baja), modificación, cancelación antes de guardar, listado de categorías activas con su cantidad de palabras y **baja lógica** habilitada únicamente cuando la categoría tiene 0 palabras activas asociadas. |
| `admin/palabras.php` | Solo **Administrador** (`Sesion::exigirAdmin`) | Página autoprocesada para el **ABM de palabras**: alta (exigiendo categoría activa existente y formato `A–Z`/`Ñ`, 3 a 50 letras), vista previa de dificultad, filtros por categoría y dificultad, modificación con recálculo de longitud, cancelación antes de guardar y **baja lógica** (con reactivación si se reingresa). |

---

## 3. Endpoints AJAX (comunicación asíncrona `XMLHttpRequest` + JSON)

| Archivo | Acceso permitido | Función dentro de la aplicación |
| :--- | :--- | :--- |
| `ajax/verificarUsuario.php` | Público (POST) | Valida el formato del nombre de usuario y consulta en MySQL si está disponible o reservado, devolviendo JSON al formulario de `registro.php`. |
| `ajax/estadoPartida.php` | Solo **Jugador** (POST) | Devuelve en JSON el estado público seguro de la partida almacenada en `$_SESSION` (incluyendo soporte tras recarga F5). Si el tiempo venció en el servidor, cierra y persiste la partida antes de responder. |
| `ajax/arriesgarLetra.php` | Solo **Jugador** (POST) | Recibe una letra, valida tiempo en el servidor, aplica la mecánica del juego (acierto, pista secuencial, repetida o ya descubierta) y, si la palabra se completa o vence el tiempo, guarda la partida y acumula el puntaje en una transacción InnoDB. |
| `ajax/abandonarPartida.php` | Solo **Jugador** (POST) | Procesa el abandono voluntario desde `partida.php`, marca el resultado como `abandonada` (`0` puntos acumulados), persiste en BD y limpia la partida activa de la sesión. |
| `ajax/finalizarPorTiempo.php` | Solo **Jugador** (POST) | Invocado por el temporizador visual de `partida.js` al llegar a `00:00`. El servidor verifica de manera independiente si el límite expiró realmente (si aún queda tiempo, resincroniza el reloj; si venció, aplica A-02 Opción A y persiste en BD). |
| `ajax/obtenerTop10.php` | Solo **Jugador** (POST) | Consulta el Top 10 actualizado y el puntaje acumulado del jugador en sesión, devolviendo JSON para renderizar la tabla inmediatamente debajo del resultado en `partida.php`. |

---

## 4. Capa de negocio (POO), configuración, estilos, scripts y base de datos

| Ruta / Archivo | Rol técnico |
| :--- | :--- |
| `config/config.php` | Constantes de conexión MySQL, credenciales hasheadas del administrador, timeouts de sesión (inactividad y absoluto), zona horaria y reglas parametrizables. |
| `clases/Conexion.class.php` | Apertura centralizada de conexión MySQLi OO en `utf8mb4` con zona horaria `-03:00` y manejo seguro de excepciones. |
| `clases/Sesion.class.php` | Inicio seguro de sesión (`HttpOnly`, `SameSite=Lax`), regeneración de ID, control de timeouts, cierre limpio y guardias por rol (`exigirJugador`, `exigirAdmin`). |
| `clases/Usuario.class.php` | Entidad `Usuario`, validaciones, registro, autenticación de jugador/admin, cálculo de estadísticas de bienvenida y consulta del Top 10. |
| `clases/Categoria.class.php` | Entidad `Categoria` y operaciones de listado, búsqueda, conteo de palabras activas, alta/reactivación, modificación y baja lógica restringida. |
| `clases/Palabra.class.php` | Entidad `Palabra`, normalización UTF-8, validación `A–Z`/`Ñ`, clasificación de dificultad, sorteo aleatorio y ABM con baja lógica. |
| `clases/Partida.class.php` | Motor del juego desacoplado de la interfaz (máscara, pistas secuenciales, control de tiempo en servidor, cálculo de resultado/multiplicador, serialización en `$_SESSION` y guardado atómico en transacción InnoDB). |
| `css/estilos.css` | Hoja de estilos externa única (CSS3) con diseño adaptable para todas las páginas del jugador y del administrador. |
| `js/utilidades.js` | Helpers compartidos (`ocultaElemento`, `muestraElemento`, `escaparHTML`, `formatearTiempo`, `validarLogin`). |
| `js/registro.js` | Validación en cliente de `registro.php` y verificación AJAX de disponibilidad de usuario. |
| `js/menu.js` | Validación de configuración de partida, confirmación de abandono y persistencia de preferencias con `localStorage`. |
| `js/partida.js` | Interacción completa de `partida.php`: teclado virtual DOM, llamadas AJAX `XMLHttpRequest`, actualización en vivo de métricas, temporizador visual y carga automática del Top 10. |
| `js/admin.js` | Validaciones en cliente de categorías/palabras, vista previa de longitud/dificultad y confirmaciones de baja lógica. |
| `sql/script_adivina_palabra_titos.sql` | Script SQL completo para crear la base de datos `adivina_palabra_titos`, sus 4 tablas InnoDB con restricciones e índices, y los datos iniciales de prueba. |
| `.htaccess`, `config/.htaccess`, `clases/.htaccess`, `sql/.htaccess` | Reglas Apache 2.4 que deshabilitan el listado de directorios (`Options -Indexes`) y bloquean el acceso HTTP directo a archivos internos sensibles (`Require all denied`). |

---

## 5. Matriz resumida de control de acceso (RF-21)

| Recurso | Sin sesión | Sesión Jugador | Sesión Administrador |
| :--- | :---: | :---: | :---: |
| `index.php`, `registro.php` | Acceso permitido | Redirige a `menu.php` | Redirige a `admin/index.php` |
| `menu.php`, `partida.php`, `ranking.php` | Redirige a `index.php` | Acceso permitido | Redirige a `admin/index.php` |
| `ajax/estadoPartida.php`, `arriesgarLetra.php`, `abandonarPartida.php`, `finalizarPorTiempo.php`, `obtenerTop10.php` | JSON `sesionExpirada: true` | Acceso permitido | JSON `sesionExpirada: true` |
| `admin/index.php`, `admin/categorias.php`, `admin/palabras.php` | Redirige a `../index.php` | Redirige a `../menu.php` | Acceso permitido |
| `config/*`, `clases/*`, `sql/*` (vía HTTP) | 403 Forbidden | 403 Forbidden | 403 Forbidden |
