# Plan y Resultados de Pruebas – Adivine la palabra (B)

**Universidad Nacional de la Patagonia San Juan Bosco – Facultad de Ingeniería**  
**Materia:** Laboratorio de Programación y Lenguajes  
**Alumno:** Félix Aldo TITOS  

Este plan sigue las categorías y técnicas del proceso de prueba de aplicaciones web vistas en la **Unidad 7** (*Contenido, Interfaz, Navegación, Componentes, Configuración y Seguridad*). Todas las pruebas fueron ejecutadas sobre el entorno local **WAMP Server** (Windows, Apache 2.4, PHP 8.3.14, MySQL 9.1.0).

---

## 1. Pruebas de contenido y base de datos

| ID | Qué se prueba | Procedimiento / Verificación | Resultado esperado | Estado |
| :---: | :--- | :--- | :--- | :---: |
| **PC-01** | Creación limpia del esquema `adivina_palabra_titos` | Importar `sql/script_adivina_palabra_titos.sql` en UTF-8 (`SOURCE` / phpMyAdmin). | 4 tablas InnoDB `utf8mb4_unicode_ci` creadas sin errores; 5 categorías, 37 palabras, 12 usuarios, 28 partidas. | OK |
| **PC-02** | Codificación UTF-8 y longitud real de palabras con `ñ` | Consultar `WHERE CHAR_LENGTH(palabra) <> longitud` y verificar `HEX(nombre)` en `Tecnología` y `Música`. | `0` desajustes de longitud (`ñandu` = 5, `cabaña` = 6, `montaña` = 7); tildes y `ñ` correctas. | OK |
| **PC-03** | Consistencia entre `usuarios` y `partidas` | Comparar `usuarios.puntaje_acumulado` contra `SUM(partidas.puntaje_sumado)` agrupado por usuario. | `0` diferencias en los 12 jugadores iniciales y tras cerrar nuevas partidas. | OK |
| **PC-04** | Integridad de sintaxis PHP | Ejecutar `php -l` sobre los 22 archivos `.php` del proyecto. | `No syntax errors detected` en todos los archivos. | OK |

---

## 2. Pruebas de interfaz y usabilidad

| ID | Mecanismo | Verificación realizada | Resultado esperado | Estado |
| :---: | :--- | :--- | :--- | :---: |
| **PI-01** | Formularios y etiquetas | Revisar `<label for>`, `required`, `maxlength`, `placeholder`, `autofocus` y orden de foco (`Tab`) en login, registro, menú, partida y ABM. | Todos los campos identificados claramente, longitud acotada y retroalimentación visible. | OK |
| **PI-02** | Teclado virtual y casillas DOM | Jugar una partida alternando teclado físico (`txtLetra`) y botones virtuales (`A`–`Z`, `Ñ`). | Letras usadas o reveladas por pista se deshabilitan visualmente (`.usada`) y las casillas actualizan sin recargar. | OK |
| **PI-03** | Preferencias en cliente | Configurar `Media` + `3 minutos` en `menu.php`, iniciar partida y volver luego al menú. | `localStorage` recuerda la última dificultad y tiempo seleccionados. | OK |
| **PI-04** | Adaptabilidad visual | Redimensionar navegador a ancho móvil (`< 640px`) en menú, partida y tablas. | Diseño legible sin solapamientos; tablas con desplazamiento horizontal `.tabla-scroll-contenedor`. | OK |

---

## 3. Pruebas de navegación y control de acceso

| ID | Caso | Acción realizada | Resultado esperado | Estado |
| :---: | :--- | :--- | :--- | :---: |
| **PN-01** | Enlaces internos | Recorrer todos los enlaces de `<nav>` y botones de acción en vistas de jugador y administrador. | Sin enlaces rotos; elemento actual resaltado con clase `.activo`. | OK |
| **PN-02** | Separación de roles (RF-21) | Visitar `menu.php`/`ranking.php` sin sesión o como `admin`; visitar `admin/*` sin sesión o como `jugador`. | Redirecciones estrictas a `index.php`, `menu.php` o `admin/index.php` sin renderizar contenido no autorizado. | OK |
| **PN-03** | Continuidad ante F5 y salida al menú (A-19) | Recargar `partida.php` (F5) a mitad de partida; luego ir a `menu.php`. | F5 mantiene máscara, métricas y reloj del servidor; `menu.php` ofrece **Continuar** o **Abandonar** antes de crear otra. | OK |
| **PN-04** | Cancelar antes de guardar en ABM | Abrir `admin/categorias.php` y `admin/palabras.php` (alta y edición) y pulsar **Cancelar / Volver** o **Cancelar edicion**. | Regresa sin insertar ni modificar registros en la BD. | OK |

---

## 4. Pruebas de componentes – Partición de equivalencia y valores de frontera

| ID | Componente | Clase / Frontera probada | Entrada | Resultado esperado | Estado |
| :---: | :--- | :--- | :--- | :--- | :---: |
| **PF-01** | Registro – Usuario | Frontera inferior inválida (`2`) | `ab` | Rechazado (3 a 30 caracteres). | OK |
| **PF-02** | Registro – Usuario | Fronteras válidas (`3` y `30`) | `abc` / 30 letras | Aceptado. | OK |
| **PF-03** | Registro – Usuario | Frontera superior inválida (`31`) | 31 letras | Rechazado. | OK |
| **PF-04** | Registro – Usuario | Reservado / caracteres inválidos | `administrador`, `ADMINISTRADOR`, `juan perez`, `juan-1` | Rechazado en cliente, AJAX y servidor. | OK |
| **PF-05** | Registro – Contraseña | Fronteras (`5`, `6`, `64`, `65`) | 5 / 6 / 64 / 65 caracteres | `5` y `65` rechazados; `6` y `64` aceptados. | OK |
| **PF-06** | Registro – Correo | Correo duplicado (A-16) | `prueba@correo.com` (existente) | Aceptado si el nombre de usuario es único. | OK |
| **PF-07** | Categoría – Nombre | Fronteras (`1`, `2`, `50`, `51`) | `A` / `TV` / 50 chars / 51 chars | `1` y `51` rechazados; `2` y `50` aceptados. | OK |
| **PF-08** | Palabra – Longitud y dificultad | Frontera inválida (`2`) | `ab` | Rechazada (mínimo 3 letras). | OK |
| **PF-09** | Palabra – Baja (`3` y `5`) | Fronteras de Baja (con `ñ`) | `sol` (3), `ñandu` (5) | Aceptadas → dificultad `baja` (`x1`). | OK |
| **PF-10** | Palabra – Media (`6` y `8`) | Fronteras de Media (con `ñ`) | `cabaña` (6), `ciclismo` (8) | Aceptadas → dificultad `media` (`x2`). | OK |
| **PF-11** | Palabra – Alta (`9` y `50`) | Fronteras de Alta | `algoritmo` (9), 50 letras | Aceptadas → dificultad `alta` (`x3`). | OK |
| **PF-12** | Palabra – Inválidos | Frontera `51`, tildes, espacios, dígitos | 51 letras, `árbol`, `hola mundo`, `abc123` | Rechazadas en cliente y servidor. | OK |
| **PF-13** | Bienvenida – Fecha relativa (A-09) | Fronteras `0`, `1`, `8`, `30`, `31`, `45` días y `0` partidas | Usuarios de prueba + registro frontera `30`/`31` | `hoy`, `ayer`, `hace 8 dias`, `hace 30 dias`, `el dd/mm/yyyy` (`31` y `45`), y mensaje sin % para `0` partidas. | OK |

---

## 5. Pruebas de rutas – Mecánica del juego y cierre transaccional

| ID | Escenario | Secuencia probada | Resultado esperado | Estado |
| :---: | :--- | :--- | :--- | :---: |
| **PR-01** | Ejemplo oficial del enunciado (`gaviota`, media) | `a` → `h` (pista `g`) → `i` → `t` → `p` (pista `v`) → `o`. | `4` adivinadas, `2` pistas, `6` intentos, resultado `ganada`, `4` pts de partida y **`8` pts acumulados** (`x2`). | OK |
| **PR-02** | Letra repetida (A-04) y ya descubierta por pista (A-17) | Reingresar `a` (ya acertada) y `g` (revelada por pista). | Aviso sin sumar intento, punto ni pista. | OK |
| **PR-03** | Palabra completada por pista (A-03) | En `oso` (baja), errar con `x` (pista `o`) y `y` (pista `s`). | Finaliza con `0` adivinadas, `2` pistas → `perdida`, acumula `0` pts. | OK |
| **PR-04** | Empate (A-01) | En `puma` (baja): `p` (+1), `x` (pista `u`), `m` (+1), `y` (pista `a`). | `2 == 2` → `empate`, acumula `2` pts (`x1`). | OK |
| **PR-05** | Ganada dificultad alta (`x3`) | En `computadora` (11 letras, 9 únicas), acertar las 9 letras. | `9` pts de partida, **`27` pts acumulados** (`x3`). | OK |
| **PR-06** | Fin por tiempo con palabra incompleta (A-02 Opción A) | En `gaviota` (media): `a` (+1), `h` (pista `g`), `i` (+1) y vence el tiempo. | Motivo `tiempo`, `2 > 1` → `ganada`, acumula `4` pts (`x2`). | OK |
| **PR-07** | Abandono voluntario (RF-13) | Acertar una letra y confirmar abandono. | Resultado `abandonada`, acumula `0` pts en BD. | OK |
| **PR-08** | Top 10 y desempate (RF-17, A-13) | Consultar `ajax/obtenerTop10.php` y `ranking.php` tras victorias y con `carla` (`#11`). | Orden por `puntaje_acumulado DESC, nombre_usuario ASC` (`camila` antes que `diego`); jugador actual destacado o informado fuera del Top 10. | OK |
| **PR-09** | ABM Baja lógica y reactivación (A-15) | Intentar baja de `Animales` (bloqueada), dar de baja `Música` (`activa = 0`) y volver a crearla; repetir ciclo con palabra `sinfonia`. | Nunca ejecuta `DELETE` físico; preserva filas con `activa = 0` y reactiva sin duplicar. | OK |

---

## 6. Pruebas de error forzado

| ID | Condición forzada | Respuesta verificada | Estado |
| :---: | :--- | :--- | :---: |
| **PE-01** | Iniciar partida en `Música` + `baja` (0 palabras, A-18) | `menu.php` muestra alerta amigable sin crear partida rota. | OK |
| **PE-02** | Llamar `ajax/arriesgarLetra.php` sin sesión, sin partida activa o sin parámetro `letra` | Devuelve JSON controlado (`sesionExpirada`, `sinPartida` o `ok: false`) sin warnings PHP. | OK |
| **PE-03** | Llamar `ajax/finalizarPorTiempo.php` antes de cumplirse el límite real | El servidor rechaza el cierre (`tiempoVencido: false`) y devuelve el tiempo real para resincronizar. | OK |
| **PE-04** | Forzar violación de FK (`id_palabra = 999999`) al cerrar partida ganada | `guardarResultadoTransaccionBD()` ejecuta `rollback()` y devuelve `false`; ni `partidas` ni `usuarios.puntaje_acumulado` se modifican. | OK |
| **PE-05** | Invocar `?editar=99999` en `admin/categorias.php` y `admin/palabras.php` | Muestra mensaje de error claro y mantiene el formulario operativo. | OK |

---

## 7. Pruebas de seguridad

| ID | Amenaza (Unidad 7) | Prueba ejecutada | Resultado verificado | Estado |
| :---: | :--- | :--- | :--- | :---: |
| **PS-01** | SQL Injection | Inyectar `' OR '1'='1` y `'; DROP TABLE ...; --` en login, registro, `verificarUsuario.php`, alta de categoría y filtros GET. | Acceso denegado o texto tratado como literal por `prepare/bind_param`; tablas intactas. | OK |
| **PS-02** | Cross-Site Scripting (XSS) | Cargar `<script>alert('XSS')</script>` en categoría y payload HTML en `txtUsuario`. | Escapado en salida como `&lt;script&gt;...` en `admin/categorias.php`, `menu.php` e `index.php`; cero ejecución de script. | OK |
| **PS-03** | Session Hijacking (timeouts) | Simular `ultima_actividad` > `1800 s` e `inicio_sesion` > `14400 s`. | `Sesion::iniciar()` destruye la sesión y exige nueva autenticación sin errores. | OK |
| **PS-04** | Exposición de secretos en partida | Inspeccionar HTML de `partida.php` y JSON de `estadoPartida.php` / `arriesgarLetra.php` en curso. | La palabra completa nunca aparece en el cliente hasta `finalizada === true`. | OK |
| **PS-05** | Acceso directo a archivos internos | Solicitar vía HTTP `config/config.php`, `clases/Conexion.class.php`, `sql/script_adivina_palabra_titos.sql` y listados de carpetas. | Archivos `.htaccess` configurados con `Require all denied` y `Options -Indexes` (Apache responde `403 Forbidden`). | OK |

---

## 8. Prueba de configuración y compatibilidad

- **Servidor probado:** WAMP Server en Windows 10/11, Apache 2.4, PHP 8.3.14 (extensiones `mysqli`, `mbstring`), MySQL 9.1.0 (`utf8mb4_unicode_ci`, zona horaria `-03:00`).
- **Cliente probado:** Navegadores actuales (Chrome / Edge) con soporte estándar HTML5, CSS3, ES5/ES6 clásico, `XMLHttpRequest` y `localStorage`.
- **Resultado:** Ejecución estable sin advertencias (`E_ALL`), sin dependencias externas ni pasos de compilación.
