# Guía de Instalación y Puesta en Marcha en WAMP – Adivine la palabra (B)

**Universidad Nacional de la Patagonia San Juan Bosco – Facultad de Ingeniería**  
**Materia:** Laboratorio de Programación y Lenguajes  
**Alumno:** Félix Aldo TITOS  

> **Nota:** La aplicación no utiliza frameworks, gestores de paquetes (Composer/npm) ni librerías externas. Solo requiere un entorno **WAMP Server** estándar con PHP 8.x y MySQL 8.x/9.x.

---

## 1. Requisitos previos

- **WAMP Server** instalado en Windows (verificado en **Apache 2.4**, **PHP 8.3.14** y **MySQL 9.1.0**).
- Servicios de Apache y MySQL iniciados (ícono de WAMP en color **verde**).
- Extensiones estándar de PHP habilitadas por defecto en WAMP: `mysqli` y `mbstring`.

---

## 2. Copiar los archivos del proyecto

1. Copiar la carpeta completa `adivina_palabra` dentro del directorio raíz de Apache en WAMP (por defecto `C:\wamp64\www\adivina_palabra\`) o dentro del directorio de un VirtualHost configurado.
2. Verificar que se conserven todas las subcarpetas (`admin/`, `ajax/`, `clases/`, `config/`, `css/`, `docs/`, `js/`, `sql/`) y los archivos `.htaccess`.

---

## 3. Crear e importar la base de datos (`adivina_palabra_titos`)

### Opción A: Usando phpMyAdmin (recomendada)
1. Abrir `http://localhost/phpmyadmin/` e iniciar sesión con usuario `root` (sin contraseña, según configuración estándar de WAMP).
2. Hacer clic en la pestaña **Importar**.
3. Seleccionar el archivo `sql/script_adivina_palabra_titos.sql`.
4. Verificar que el conjunto de caracteres del archivo sea **`utf-8`**.
5. Hacer clic en **Importar** (o **Continuar**). El script crea automáticamente la base `adivina_palabra_titos`, sus 4 tablas InnoDB (`usuarios`, `categorias`, `palabras`, `partidas`) y los datos iniciales de prueba.

### Opción B: Desde consola MySQL
```bat
mysql -u root --default-character-set=utf8mb4 -e "SOURCE C:/wamp64/www/adivina_palabra/sql/script_adivina_palabra_titos.sql"
```
*(Si se usa Windows PowerShell, evitar `Get-Content | mysql` sin UTF-8 porque puede alterar tildes y `ñ`; preferir la opción `SOURCE` o phpMyAdmin.)*

---

## 4. Verificar configuración de conexión

Abrir `config/config.php` y comprobar que los parámetros coincidan con el servidor MySQL local:

```php
define('BD_HOST', 'localhost');
define('BD_USUARIO', 'root');
define('BD_PASSWORD', '');
define('BD_NOMBRE', 'adivina_palabra_titos');
define('BD_PUERTO', 3306);
```

*(Si el entorno WAMP utiliza otro puerto para MySQL —por ejemplo `3308` cuando coexistió MariaDB— o contraseña para `root`, actualizar únicamente `BD_PUERTO` y/o `BD_PASSWORD` en este archivo.)*

---

## 5. Verificar soporte de `.htaccess` en Apache

En una instalación estándar de WAMP, `AllowOverride All` viene habilitado para `www/`. Para comprobar que la protección está activa, intentar abrir `http://localhost/adivina_palabra/config/config.php` desde el navegador: Apache debe responder **403 Forbidden**.

---

## 6. Acceso y cuentas de prueba precargadas

Abrir en el navegador:

```text
http://localhost/adivina_palabra/
```

### Credenciales disponibles tras importar el script SQL

| Rol | Usuario | Contraseña | Caso de prueba que representa |
| :--- | :--- | :--- | :--- |
| **Administrador** | `administrador` | `@adminJuegoPalabras#5555` | Acceso exclusivo al Panel de Administración (ABM categorías y palabras). |
| **Jugador** | `felix` | `123456` | Puesto `#1` (`86 pts`), 9 de 10 ganadas (`90%`), última partida **ayer** (ejemplo del enunciado). |
| **Jugador** | `sofia` | `123456` | Puesto `#2` (`81 pts`), última partida **hoy**. |
| **Jugador** | `camila` / `diego` | `123456` | Empatados en `42 pts` (verifica desempate alfabético A-13 en Top 10). |
| **Jugador** | `martin` | `123456` | Última partida hace `8` días (`"hace 8 dias"`, A-09). |
| **Jugador** | `lucia` | `123456` | Última partida hace `45` días (`"el dd/mm/yyyy"`, > 30 días, A-09). |
| **Jugador** | `carla` | `123456` | Puesto `#11` (`5 pts`, fuera del Top 10), comparte correo con `nuevo_jugador` (A-16). |
| **Jugador** | `nuevo_jugador` | `123456` | `0` partidas jugadas (mensaje de bienvenida sin porcentaje, A-08). |

### Categorías y palabras iniciales
- **5 categorías:** `Animales` (10 palabras, incluye `gaviota` y palabras con `ñ`), `Deportes` (9), `Turismo` (9), `Tecnología` (9) y `Música` (`0` palabras, lista para probar A-18 y baja lógica de categoría sin palabras).

---

## 7. Recordatorio de entrega (examen final)

- **Fecha límite:** sábado 28 de noviembre de 2026 hasta las 15:00.
- **Correo destino:** `lpyl.cr.unpsjb@outlook.com`.
- **Nota sobre Gmail:** si se envía desde Gmail y el servicio bloquea adjuntos `.zip` que contienen archivos `.php`/`.js`, subir el paquete completo a Google Drive / OneDrive con permisos de descarga y enviar el enlace por correo.
