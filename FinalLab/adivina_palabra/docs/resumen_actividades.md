# Resumen de Actividades Realizadas – Adivine la palabra (B)

**Universidad Nacional de la Patagonia San Juan Bosco – Facultad de Ingeniería**  
**Materia:** Laboratorio de Programación y Lenguajes  
**Alumno:** Félix Aldo TITOS  

---

## 1. Enfoque de desarrollo

La aplicación **Adivine la palabra (B)** se desarrolló de manera incremental en etapas verificables, aplicando exclusivamente los estándares y tecnologías vistos durante la cursada (**HTML5, CSS3, JavaScript nativo, PHP orientado a objetos, MySQLi OO sobre InnoDB y AJAX con `XMLHttpRequest` + JSON**), sin frameworks ni librerías externas, sobre el entorno **WAMP Server** (Windows, Apache 2.4, PHP 8.3.14, MySQL 9.1.0).

---

## 2. Actividades realizadas organizadas por unidad de la cursada

### Unidad 1 – Introducción a sistemas basados en plataforma web
- Relevamiento completo del enunciado oficial y formalización de **21 requerimientos funcionales (RF-01 a RF-21)**, incluyendo el control de acceso estricto entre roles.
- Identificación, análisis y resolución documentada de **20 puntos de ambigüedad (A-01 a A-20)** antes de escribir código.
- Definición de una arquitectura cliente-servidor en tres capas sobre WAMP: presentación (HTML5/CSS3/JS), lógica de aplicación (clases PHP OO y endpoints AJAX) y persistencia relacional (MySQL).
- Organización modular del proyecto en directorios con responsabilidades claras (`admin/`, `ajax/`, `clases/`, `config/`, `css/`, `docs/`, `js/`, `sql/`).

### Unidad 2 – Diseño y desarrollo de páginas web estáticas (HTML5 y CSS3)
- Maquetación de todas las vistas con etiquetas semánticas de **HTML5** (`header`, `nav`, `section`, `article`, `aside`, `footer`).
- Construcción de formularios accesibles con agrupadores (`fieldset`, `legend`) y tipos/atributos nativos de validación (`type="email"`, `type="password"`, `type="radio"`, `select`, `progress`, `required`, `maxlength`, `placeholder`, `autofocus`).
- Diseño de una única hoja de estilos externa (`css/estilos.css`) en **CSS3** utilizando clases e identificadores, Flexbox, Grid y media queries (`@media`) para asegurar legibilidad en pantallas de escritorio y dispositivos más estrechos.

### Unidad 3 – Tecnología de desarrollo web del lado del cliente (JavaScript)
- Implementación de validaciones en cliente (`js/utilidades.js`, `js/registro.js`, `js/menu.js`, `js/admin.js`) previas al envío de cada formulario.
- Manipulación dinámica del DOM (`getElementById`, `getElementsByName`, `createElement`, `appendChild`, `innerHTML`, `textContent`, `classList`) para generar el teclado virtual (`A`–`Z` y `Ñ`), renderizar las casillas de la palabra enmascarada, listar letras arriesgadas y poblar el Top 10, evitando el uso de `document.write`.
- Implementación del temporizador visual en `js/partida.js` (`setInterval` / `clearInterval` sincronizado con el servidor) y persistencia de preferencias de dificultad y tiempo en `localStorage`.

### Unidad 4 – Tecnología de desarrollo web del lado del servidor (PHP OO)
- Diseño e implementación de 6 clases orientadas a objetos en `clases/*.class.php` (`Conexion`, `Sesion`, `Usuario`, `Categoria`, `Palabra`, `Partida`), con atributos privados, constructores, getters/setters y métodos estáticos de acceso a datos.
- Desarrollo de páginas autoprocesadas (`index.php`, `registro.php`, `menu.php`, `admin/categorias.php`, `admin/palabras.php`) procesando `$_POST` y `$_GET`.
- Gestión centralizada de sesiones con `$_SESSION`: regeneración de identificador al iniciar sesión, almacenamiento seguro de la partida activa en el servidor (garantizando que la palabra oculta no viaje al navegador) y control de autorización por rol.

### Unidad 5 – Gestión de la información (MySQL y MySQLi OO)
- Diseño del modelo entidad-relación y creación del script `sql/script_adivina_palabra_titos.sql` con la base de datos **`adivina_palabra_titos`** (incluyendo el apellido del alumno) y 4 tablas **InnoDB** (`utf8mb4_unicode_ci`): `usuarios`, `categorias`, `palabras` y `partidas`.
- Definición de claves primarias, claves foráneas (`ON DELETE RESTRICT`), unicidades (`nombre_usuario`, `categorias.nombre`, `(palabra, id_categoria)`), restricciones `CHECK` e índices de consulta (incluido el índice de ranking Top 10).
- Carga de datos iniciales realistas (5 categorías, 37 palabras con casos de frontera y `ñ`, 12 usuarios y 28 partidas históricas coherentes con los puntajes acumulados).
- Acceso a datos con **MySQLi orientada a objetos**: consultas preparadas (`prepare`, `bind_param`, `execute`, `get_result`, `fetch_object`), liberación sistemática de recursos (`free()`, `close()`) y transacciones explícitas (`begin_transaction`, `commit`, `rollback`) para garantizar atomicidad al registrar la partida y actualizar `usuarios.puntaje_acumulado`.

### Unidad 6 – Introducción a la Web 2.0 (AJAX)
- Implementación de 6 endpoints asíncronos en `ajax/*.php` consumidos desde JavaScript mediante el objeto nativo **`XMLHttpRequest`** (`open`, `onreadystatechange`, verificación de `readyState == 4 && status == 200`, intercambio de respuestas JSON con `json_encode` en PHP y `JSON.parse` en JS).
- Cobertura interactiva sin recargas de página para: verificación de nombre de usuario en registro, recuperación de estado de partida (incluyendo F5), envío de letras arriesgadas, abandono de partida, validación de fin por tiempo y carga inmediata del Top 10 tras el resultado final.

### Unidad 7 – Recursos para seguridad y prueba de las aplicaciones web
- Protección de contraseñas mediante hash `bcrypt` con sal aleatoria (`password_hash` / `password_verify`), tanto para jugadores como para el usuario administrador (`ADMIN_PASSWORD_HASH`).
- Prevención sistemática de **SQL Injection** (100% sentencias preparadas), **XSS** (`htmlspecialchars` en PHP y `escaparHTML` / `textContent` en JS), **Session Fixation** (`session_regenerate_id(true)`) y **Session Hijacking** (cookies `HttpOnly`/`SameSite=Lax` y doble expiración por inactividad y absoluta).
- Protección del árbol de archivos en Apache mediante `.htaccess` (`Options -Indexes` en raíz y `Require all denied` en `config/`, `clases/` y `sql/`).
- Ejecución de un plan integral de pruebas cubriendo contenido/BD, interfaz, navegación, componentes (partición de equivalencia, análisis de valores de frontera, prueba de rutas y error forzado) y seguridad, corrigiendo los hallazgos detectados durante la auditoría.

---

## 3. Cronología de etapas desarrolladas

| Etapa | Alcance principal | Verificación realizada |
| :---: | :--- | :--- |
| **0** | Análisis del enunciado, relevamiento de estilo y definición de ambigüedades | Confirmación de entorno WAMP y decisiones A-01 a A-20 |
| **1** | Diseño de arquitectura, modelo ER, diagrama de clases y contratos AJAX | Revisión de RF-01 a RF-21 y política de baja lógica A-15 |
| **2** | Script SQL `adivina_palabra_titos` y datos de prueba | Importación en MySQL 9.1.0 y chequeo de integridad/UTF-8 |
| **3** | Núcleo POO en PHP (`config/` y `clases/*.class.php`) | Lint PHP y pruebas unitarias sobre las 6 clases y motor `gaviota` |
| **4** | Registro, login, sesión, bienvenida y menú principal | Pruebas de flujo en WAMP, AJAX de usuario y `localStorage` |
| **5** | Pantalla de partida interactiva y endpoints AJAX del juego | Prueba completa de `gaviota`, pistas, tiempo, abandono y F5 |
| **6** | Cierre transaccional de partida, `obtenerTop10.php` y `ranking.php` | Pruebas de multiplicadores, desempate, rollback y Top 10 |
| **7** | Panel de administrador (`admin/index.php`, `categorias.php`, `palabras.php`, `js/admin.js`) | Pruebas de ABM, baja lógica A-15, reactivación y RF-21 |
| **8** | Auditoría de seguridad y batería integral de pruebas (U7) | 22 archivos `php -l`, 56 aserciones de fronteras, seguridad y error forzado |
| **9** | Elaboración de la documentación técnica y manual de usuario (`docs/`) | Revisión de entregables frente al enunciado oficial |
