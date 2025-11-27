/*
    Script SQL que crea la estructura de la base de datos relativa a la información obtenida
    mediante las pruebas de usabilidad.
    Autor: Enol Monte Soto
    Fecha: NOV-2025
    Versión: 1
*/
CREATE TABLE usuarios (
    id_usuario INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    profesion VARCHAR(100) NOT NULL,
    edad TINYINT UNSIGNED NOT NULL CHECK (edad BETWEEN 0 and 120),
    genero VARCHAR(100) NOT NULL,
    pericia_informatica TINYINT UNSIGNED NOT NULL CHECK (pericia_informatica BETWEEN 0 and 10)
);

CREATE TABLE dispositivos (
    id_dispositivo TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(20) NOT NULL UNIQUE
);

CREATE TABLE resultados (
    id_resultado INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT UNSIGNED NOT NULL,
    id_dispositivo TINYINT UNSIGNED NOT NULL,
    tiempo FLOAT NOT NULL,
    completada BOOLEAN NOT NULL,
    comentarios_usuario TEXT,
    propuestas_mejora TEXT,
    valoracion_usuario TINYINT NOT NULL CHECK (valoracion_usuario BETWEEN 0 and 10),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_dispositivo) REFERENCES dispositivos(id_dispositivo)
);

CREATE TABLE observaciones_facilitador (
    id_observacion INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    id_resultado INT UNSIGNED NOT NULL,
    comentario TEXT NOT NULL,
    FOREIGN KEY (id_resultado) REFERENCES resultados(id_resultado)
);
