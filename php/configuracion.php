<?php
class Configuracion
{
    private $servername = "localhost";
    private $username = "DBUSER2025";
    private $password = "DBPSWD2025";
    private $dbname = "UO287616_DB";

    private $db;

    public function __construct()
    {
        $this->db = new mysqli($this->servername, $this->username, $this->password);
        if ($this->db->connect_error) {
            exit("<p>Error conectando a MySQL: " . $this->db->connect_error . "</p>");
        }
    }

    private function existeBD(): bool
    {
        $resultado = $this->db->query("SHOW DATABASES LIKE '$this->dbname'");
        return ($resultado && $resultado->num_rows > 0);
    }

    public function crearBD(): string
    {

        if ($this->existeBD()) {
            return "La base de datos ya existe.";
        }

        $query = "CREATE DATABASE IF NOT EXISTS `$this->dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
        if (!$this->db->query($query)) {
            return "Error creando la base de datos: " . $this->db->error;
        }

        $this->db->select_db($this->dbname);

        $scripts = [
            "CREATE TABLE IF NOT EXISTS usuarios (
                id_usuario INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                profesion VARCHAR(100) NOT NULL,
                edad TINYINT UNSIGNED NOT NULL CHECK (edad BETWEEN 0 and 120),
                genero VARCHAR(100) NOT NULL,
                pericia_informatica TINYINT UNSIGNED NOT NULL CHECK (pericia_informatica BETWEEN 0 and 10)
            ) ENGINE=InnoDB;",

            "CREATE TABLE IF NOT EXISTS dispositivos (
                id_dispositivo TINYINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                nombre VARCHAR(20) NOT NULL UNIQUE
            ) ENGINE=InnoDB;",

            "CREATE TABLE IF NOT EXISTS resultados (
                id_resultado INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                id_usuario INT UNSIGNED,
                id_dispositivo TINYINT UNSIGNED,
                tiempo FLOAT NOT NULL,
                completada BOOLEAN NOT NULL,
                comentarios_usuario TEXT,
                propuestas_mejora TEXT,
                valoracion_usuario TINYINT NOT NULL CHECK (valoracion_usuario BETWEEN 0 and 10),
                FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
                FOREIGN KEY (id_dispositivo) REFERENCES dispositivos(id_dispositivo)
            ) ENGINE=InnoDB;",

            "CREATE TABLE IF NOT EXISTS observaciones_facilitador (
                id_observacion INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
                id_resultado INT UNSIGNED NOT NULL,
                comentario TEXT NOT NULL,
                FOREIGN KEY (id_resultado) REFERENCES resultados(id_resultado)
            ) ENGINE=InnoDB;",

            "INSERT INTO usuarios (profesion, edad, genero, pericia_informatica) 
            VALUES ('Tester', 30, 'No especificado', 8);",

            "INSERT INTO dispositivos (nombre) 
            VALUES ('PC de Pruebas');"
        ];

        foreach ($scripts as $sql) {
            if (!$this->db->query($sql)) {
                return "Error creando tablas: " . $this->db->error;
            }
        }

        return "Base de datos y tablas creadas correctamente.";
    }

    public function reiniciarBD(): string
    {
        if (!$this->existeBD()) {
            return "La base de datos no existe.";
        }

        $this->db->select_db($this->dbname);

        $tablas = $this->obtenerTodasLasTablas();
        if (empty($tablas)) {
            return "No se han encontrado tablas.";
        }

        foreach ($tablas as $tabla) {
            $this->db->query("DELETE FROM `$tabla`");
            $this->db->query("ALTER TABLE `$tabla` AUTO_INCREMENT = 1");
        }

        return "La base de datos se ha reiniciado correctamente.";
    }

    public function eliminarBD(): string
    {
        if (!$this->existeBD()) {
            return "La base de datos no existe.";
        }
        $query = "DROP DATABASE IF EXISTS `$this->dbname`";
        if ($this->db->query($query)) {
            return "La base de datos ha sido eliminada.";
        } else {
            return "No se pudo eliminar la base de datos. Error: " . $this->db->error;
        }
    }

    public function exportarBD(): string
    {
        if (!$this->existeBD()) {
            return "La base de datos no existe.";
        }

        $this->db->select_db($this->dbname);

        $query = "SELECT * FROM resultados";
        $resultado = $this->db->query($query);

        if (!$resultado) {
            return "Error consultando la tabla resultados: " . $this->db->error;
        }

        if ($resultado->num_rows === 0) {
            return "No se han encontrado datos para exportar.";
        }

        $filename = "resultados_" . date("Ymd_His") . ".csv";

        $file = fopen($filename, 'w');
        if ($file === false) {
            return "No se pudo crear el archivo CSV.";
        }

        $fields = $resultado->fetch_fields();
        $headers = [];
        foreach ($fields as $field) {
            $headers[] = $field->name;
        }
        fputcsv($file, $headers);

        while ($row = $resultado->fetch_assoc()) {
            foreach ($row as $key => $value) {
                if ($value === null || $value === '') {
                    $row[$key] = 'Desconocido';
                }
            }
            fputcsv($file, $row);
        }

        fclose($file);

        return "Exportación completada. Archivo generado: $filename";
    }


    public function obtenerTodasLasTablas(): array
    {
        $resultado = $this->db->query("SHOW TABLES");
        $tablas = [];
        while ($fila = $resultado->fetch_array()) {
            $tablas[] = $fila[0];
        }
        return $tablas;
    }
}

$conf = new Configuracion();
$mensaje = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['crear'])) {
        $mensaje = $conf->crearBD();
    } elseif (isset($_POST['reiniciar'])) {
        $conf->eliminarBD();
        $conf->crearBD();
        $mensaje = "La base de datos se ha reiniciado correctamente.";
    } elseif (isset($_POST['eliminar'])) {
        $mensaje = $conf->eliminarBD();
    } elseif (isset($_POST['exportar'])) {
        $mensaje = $conf->exportarBD();
    }
}
?>

<!DOCTYPE HTML>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Pruebas De Usabilidad</title>
    <meta name="author" content="Enol Monte Soto" />
    <meta name="description" content="Pruebas de usabilidad de la web MotoGP-Desktop." />
    <meta name="keywords" content="MotoGP, Ayuda" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <header>
        <h1>MotoGP-Desktop</h1>
    </header>

    <h2>Configuración de la base de datos.</h2>

    <form method="post">
        <button type="submit" name="crear">Crear BD</button>
        <button type="submit" name="reiniciar">Reiniciar BD</button>
        <button type="submit" name="eliminar">Eliminar BD</button>
        <button type="submit" name="exportar">Exportar BD</button>
    </form>

    <?php if (!empty($mensaje)) { ?>
        <p><?php echo $mensaje; ?></p>
    <?php } ?>

</body>

</html>