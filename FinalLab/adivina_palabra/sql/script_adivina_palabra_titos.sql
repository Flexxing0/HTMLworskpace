SET NAMES utf8mb4;
SET time_zone = '-03:00';

DROP DATABASE IF EXISTS adivina_palabra_titos;

CREATE DATABASE adivina_palabra_titos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE adivina_palabra_titos;

-- -----------------------------------------------------------------------------
-- Tabla: usuarios
-- Almacena jugadores registrados. El nombre de usuario es único; el correo
-- electrónico puede repetirse . El administrador no figura aquí.
-- -----------------------------------------------------------------------------
CREATE TABLE usuarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre_usuario VARCHAR(30) NOT NULL,
    correo VARCHAR(120) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    puntaje_acumulado INT UNSIGNED NOT NULL DEFAULT 0,
    fecha_registro DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_usuarios_nombre (nombre_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Tabla: categorias
-- Almacena las categorías de palabras. Usa baja lógica (activa = 0) para
-- preservar siempre la integridad histórica 
-- -----------------------------------------------------------------------------
CREATE TABLE categorias (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_categorias_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Tabla: palabras
-- Solo letras a-z y ñ en minúscula, mínimo 3 letras.
-- Dificultad según longitud: baja (3-5), media (6-8), alta (9 o más).
-- Usa baja lógica (activa = 0) en todos los casos 
-- -----------------------------------------------------------------------------
CREATE TABLE palabras (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    palabra VARCHAR(50) NOT NULL,
    longitud TINYINT UNSIGNED NOT NULL,
    id_categoria INT UNSIGNED NOT NULL,
    activa TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    UNIQUE KEY uq_palabra_categoria (palabra, id_categoria),
    KEY idx_palabras_cat_act_long (id_categoria, activa, longitud),
    CONSTRAINT fk_palabras_categoria
        FOREIGN KEY (id_categoria) REFERENCES categorias (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- Tabla: partidas
-- Registra cada partida finalizada o abandonada junto con sus estadísticas
-- y el puntaje efectivamente sumado al jugador.
-- -----------------------------------------------------------------------------
CREATE TABLE partidas (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_palabra INT UNSIGNED NOT NULL,
    nivel ENUM('baja', 'media', 'alta') NOT NULL,
    tiempo_limite_seg INT UNSIGNED NULL DEFAULT NULL,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME NOT NULL,
    intentos SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    letras_acertadas TINYINT UNSIGNED NOT NULL DEFAULT 0,
    pistas TINYINT UNSIGNED NOT NULL DEFAULT 0,
    letras_arriesgadas VARCHAR(100) NOT NULL DEFAULT '',
    resultado ENUM('ganada', 'perdida', 'empate', 'abandonada') NOT NULL,
    puntaje_partida TINYINT UNSIGNED NOT NULL DEFAULT 0,
    puntaje_sumado SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY idx_partidas_usuario_fecha (id_usuario, fecha_fin),
    CONSTRAINT fk_partidas_usuario
        FOREIGN KEY (id_usuario) REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_partidas_palabra
        FOREIGN KEY (id_palabra) REFERENCES palabras (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- DATOS DE PRUEBA
-- =============================================================================

-- -----------------------------------------------------------------------------
-- Categorías (5 categorías activas; "Música" sin palabras para probar A-18
-- y baja lógica de categoría sin palabras asociadas)
-- -----------------------------------------------------------------------------
INSERT INTO categorias (id, nombre, activa) VALUES
(1, 'Animales', 1),
(2, 'Deportes', 1),
(3, 'Turismo', 1),
(4, 'Tecnología', 1),
(5, 'Música', 1);

-- -----------------------------------------------------------------------------
-- Palabras (37 palabras activas: incluye "gaviota" del enunciado, palabras con
-- "ñ" y casos de frontera de 3, 5, 6, 8 y 9 letras en todas las categorías)
-- -----------------------------------------------------------------------------
INSERT INTO palabras (id, palabra, longitud, id_categoria, activa) VALUES
-- Categoría 1: Animales
(1,  'oso',          3,  1, 1),
(2,  'puma',         4,  1, 1),
(3,  'araña',        5,  1, 1),
(4,  'ñandu',        5,  1, 1),
(5,  'piraña',       6,  1, 1),
(6,  'gaviota',      7,  1, 1),
(7,  'elefante',     8,  1, 1),
(8,  'guacamayo',    9,  1, 1),
(9,  'hipopotamo',   10, 1, 1),
(10, 'rinoceronte',  11, 1, 1),

-- Categoría 2: Deportes
(11, 'box',          3,  2, 1),
(12, 'golf',         4,  2, 1),
(13, 'tenis',        5,  2, 1),
(14, 'futbol',       6,  2, 1),
(15, 'arquero',      7,  2, 1),
(16, 'ciclismo',     8,  2, 1),
(17, 'atletismo',    9,  2, 1),
(18, 'baloncesto',   10, 2, 1),
(19, 'halterofilia', 12, 2, 1),

-- Categoría 3: Turismo
(20, 'mar',          3,  3, 1),
(21, 'mapa',         4,  3, 1),
(22, 'playa',        5,  3, 1),
(23, 'cabaña',       6,  3, 1),
(24, 'montaña',      7,  3, 1),
(25, 'aventura',     8,  3, 1),
(26, 'patagonia',    9,  3, 1),
(27, 'excursion',    9,  3, 1),
(28, 'cordillera',   10, 3, 1),

-- Categoría 4: Tecnología
(29, 'red',          3,  4, 1),
(30, 'byte',         4,  4, 1),
(31, 'cable',        5,  4, 1),
(32, 'codigo',       6,  4, 1),
(33, 'teclado',      7,  4, 1),
(34, 'servidor',     8,  4, 1),
(35, 'algoritmo',    9,  4, 1),
(36, 'navegador',    9,  4, 1),
(37, 'computadora',  11, 4, 1);

-- -----------------------------------------------------------------------------
-- Usuarios de prueba (12 jugadores para validar Top 10, desempate alfabético,
-- correos repetidos y todos los mensajes de bienvenida).
-- Contraseña de todos los usuarios de prueba: 123456
-- Hash generado con password_hash('123456', PASSWORD_DEFAULT) en PHP 8.3.
-- -----------------------------------------------------------------------------
INSERT INTO usuarios (id, nombre_usuario, correo, password_hash, puntaje_acumulado, fecha_registro) VALUES
(1,  'felix',         'felix@unpsjb.edu.ar',  '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 86, DATE_SUB(NOW(), INTERVAL 60 DAY)),
(2,  'sofia',         'sofia@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 81, DATE_SUB(NOW(), INTERVAL 55 DAY)),
(3,  'valentina',     'valen@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 60, DATE_SUB(NOW(), INTERVAL 50 DAY)),
(4,  'camila',        'camila@correo.com',    '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 42, DATE_SUB(NOW(), INTERVAL 48 DAY)),
(5,  'diego',         'diego@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 42, DATE_SUB(NOW(), INTERVAL 45 DAY)),
(6,  'martin',        'martin@correo.com',    '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 36, DATE_SUB(NOW(), INTERVAL 40 DAY)),
(7,  'mateo',         'mateo@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 28, DATE_SUB(NOW(), INTERVAL 35 DAY)),
(8,  'paula',         'paula@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 22, DATE_SUB(NOW(), INTERVAL 30 DAY)),
(9,  'lucia',         'lucia@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 18, DATE_SUB(NOW(), INTERVAL 50 DAY)),
(10, 'bruno',         'bruno@correo.com',     '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 10, DATE_SUB(NOW(), INTERVAL 25 DAY)),
(11, 'carla',         'prueba@correo.com',    '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 5,  DATE_SUB(NOW(), INTERVAL 22 DAY)),
(12, 'nuevo_jugador', 'prueba@correo.com',    '$2y$10$FORsoG061Mjh1mjJvxetFuF52WfArdd7VMPsnfjLuAvBTFZsTJ7zu', 0,  DATE_SUB(NOW(), INTERVAL 2 DAY));

-- -----------------------------------------------------------------------------
-- Partidas históricas de prueba (coherentes con puntaje_acumulado de usuarios)
-- - felix: 10 partidas, 9 ganadas (90%), última partida AYER (ejemplo enunciado)
-- - sofia: última partida HOY
-- - martin: última partida hace 8 días ("hace 8 días")
-- - lucia: última partida hace 45 días (> 30 días -> "el dd/mm/yyyy")
-- - camila y diego: empatados en 42 pts (desempate alfabético en Top 10)
-- - nuevo_jugador: 0 partidas (bienvenida especial sin porcentaje)
-- -----------------------------------------------------------------------------
INSERT INTO partidas (
    id_usuario, id_palabra, nivel, tiempo_limite_seg, fecha_inicio, fecha_fin,
    intentos, letras_acertadas, pistas, letras_arriesgadas,
    resultado, puntaje_partida, puntaje_sumado
) VALUES
-- Partidas de felix (id=1) -> 9 ganadas, 1 perdida = 90%, suma = 86 pts
(1, 6,  'media', 180,  DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 15 DAY), INTERVAL 65 SECOND), 6, 4, 2, 'a,h,i,t,p,o',          'ganada',  4, 8),
(1, 26, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 14 DAY), INTERVAL 90 SECOND), 7, 6, 1, 'a,t,g,o,z,n,i',        'ganada',  6, 18),
(1, 17, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 12 DAY), INTERVAL 110 SECOND),8, 5, 3, 'a,t,r,e,i,u,s,z',      'ganada',  5, 15),
(1, 7,  'media', 180,  DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 10 DAY), INTERVAL 70 SECOND), 6, 5, 1, 'e,l,f,a,z,n',          'ganada',  5, 10),
(1, 13, 'baja',  60,   DATE_SUB(NOW(), INTERVAL 9 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 9 DAY),  INTERVAL 35 SECOND), 5, 4, 1, 'e,n,i,o,s',            'ganada',  4, 4),
(1, 34, 'media', NULL, DATE_SUB(NOW(), INTERVAL 7 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 7 DAY),  INTERVAL 80 SECOND), 7, 5, 2, 'e,r,a,v,i,u,d',        'ganada',  5, 10),
(1, 3,  'baja',  60,   DATE_SUB(NOW(), INTERVAL 6 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 6 DAY),  INTERVAL 40 SECOND), 3, 1, 2, 'e,i,ñ',                'perdida', 1, 0),
(1, 32, 'media', 180,  DATE_SUB(NOW(), INTERVAL 4 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 DAY),  INTERVAL 50 SECOND), 5, 4, 1, 'o,d,i,a,g',            'ganada',  4, 8),
(1, 22, 'baja',  NULL, DATE_SUB(NOW(), INTERVAL 3 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 3 DAY),  INTERVAL 30 SECOND), 4, 3, 1, 'a,l,e,y',              'ganada',  3, 3),
(1, 24, 'media', 180,  TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY), '18:00:00'), TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 DAY), '18:01:15'), 6, 5, 1, 'o,n,t,a,e,ñ',          'ganada',  5, 10),

-- Partidas de sofia (id=2) -> última HOY, suma = 81 pts
(2, 18, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 6 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 6 DAY),  INTERVAL 120 SECOND),9, 8, 1, 'a,l,o,n,c,e,s,z,t',    'ganada',  8, 24),
(2, 19, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 4 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 DAY),  INTERVAL 130 SECOND),9, 8, 1, 'a,l,t,e,r,o,f,z,i',    'ganada',  8, 24),
(2, 37, 'alta',  180,  DATE_SUB(NOW(), INTERVAL 2 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 2 DAY),  INTERVAL 95 SECOND), 9, 7, 2, 'o,m,p,u,e,t,a,i,d',    'ganada',  7, 21),
(2, 28, 'alta',  180,  TIMESTAMP(CURDATE(), '00:01:00'), TIMESTAMP(CURDATE(), '00:02:40'), 8, 4, 4, 'a,e,i,o,u,n,s,t',      'empate',  4, 12),

-- Partidas de valentina (id=3) -> suma = 60 pts
(3, 37, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 8 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 8 DAY),  INTERVAL 105 SECOND),9, 7, 2, 'o,p,u,e,t,a,i,d,r',    'ganada',  7, 21),
(3, 35, 'alta',  180,  DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 5 DAY),  INTERVAL 88 SECOND), 8, 7, 1, 'l,g,o,r,i,t,e,m',      'ganada',  7, 21),
(3, 8,  'alta',  NULL, DATE_SUB(NOW(), INTERVAL 3 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 3 DAY),  INTERVAL 92 SECOND), 7, 6, 1, 'u,a,c,m,y,e,o',        'ganada',  6, 18),

-- Partidas de camila (id=4) -> suma = 42 pts (empata con diego)
(4, 18, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 7 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 7 DAY),  INTERVAL 115 SECOND),9, 7, 2, 'l,o,n,c,i,e,s,u,t',    'ganada',  7, 21),
(4, 9,  'alta',  180,  DATE_SUB(NOW(), INTERVAL 4 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 4 DAY),  INTERVAL 70 SECOND), 7, 7, 0, 'h,i,p,o,t,a,m',        'ganada',  7, 21),

-- Partidas de diego (id=5) -> suma = 42 pts (incluye 1 abandonada = 0 pts)
(5, 10, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 9 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 9 DAY),  INTERVAL 95 SECOND), 7, 7, 0, 'r,i,n,o,c,e,t',        'ganada',  7, 21),
(5, 27, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 6 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 6 DAY),  INTERVAL 140 SECOND),9, 7, 2, 'x,c,u,r,a,s,i,d,o',    'ganada',  7, 21),
(5, 14, 'media', 180,  DATE_SUB(NOW(), INTERVAL 5 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 5 DAY),  INTERVAL 25 SECOND), 2, 1, 1, 'u,a',                  'abandonada', 1, 0),

-- Partidas de martin (id=6) -> hace 8 días, suma = 36 pts
(6, 36, 'alta',  180,  DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 11 DAY), INTERVAL 100 SECOND),8, 6, 2, 'a,v,e,i,g,d,u,o',      'ganada',  6, 18),
(6, 28, 'alta',  NULL, DATE_SUB(NOW(), INTERVAL 8 DAY),  DATE_ADD(DATE_SUB(NOW(), INTERVAL 8 DAY),  INTERVAL 110 SECOND),8, 6, 2, 'o,r,d,i,u,l,e,s',      'ganada',  6, 18),

-- Partidas de mateo (id=7) -> suma = 28 pts
(7, 17, 'alta',  300,  DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 13 DAY), INTERVAL 120 SECOND),8, 6, 2, 't,l,e,i,u,s,m,r',      'ganada',  6, 18),
(7, 33, 'media', 180,  DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 10 DAY), INTERVAL 60 SECOND), 7, 5, 2, 'e,c,l,i,a,u,d',        'ganada',  5, 10),

-- Partidas de paula (id=8) -> suma = 22 pts
(8, 25, 'media', 180,  DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 16 DAY), INTERVAL 80 SECOND), 7, 6, 1, 'v,e,n,t,u,i,r',        'ganada',  6, 12),
(8, 16, 'media', 180,  DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 12 DAY), INTERVAL 72 SECOND), 6, 5, 1, 'i,l,s,m,a,o',          'ganada',  5, 10),

-- Partidas de lucia (id=9) -> hace 45 días (> 30 días -> fecha dd/mm/yyyy), suma = 18 pts
(9, 15, 'media', 180,  DATE_SUB(NOW(), INTERVAL 47 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 47 DAY), INTERVAL 64 SECOND), 6, 6, 0, 'a,r,q,u,e,o',          'ganada',  6, 12),
(9, 14, 'media', 180,  DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 45 DAY), INTERVAL 85 SECOND), 6, 3, 3, 'u,a,b,e,i,l',          'empate',  3, 6),

-- Partidas de bruno (id=10) -> suma = 10 pts
(10, 5, 'media', 60,   DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 15 DAY), INTERVAL 42 SECOND), 5, 5, 0, 'p,i,r,a,ñ',            'ganada',  5, 10),

-- Partidas de carla (id=11) -> suma = 5 pts (puesto 11, fuera del Top 10)
(11, 4, 'baja',  60,   DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_ADD(DATE_SUB(NOW(), INTERVAL 20 DAY), INTERVAL 30 SECOND), 5, 5, 0, 'ñ,a,n,d,u',            'ganada',  5, 5);
