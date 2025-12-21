<?php
session_start();
require_once "Db.php";

class Registro
{
    private $errores = [];
    private $valores = [];
    private $db;

    public function __construct()
    {
        $this->db = new Db();
        $this->procesar();
    }

    private function procesar()
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {

            if (empty($_POST["profesion"]))
                $this->errores["profesion"] = "Indique la profesión.";
            else
                $this->valores["profesion"] = $_POST["profesion"];

            if (empty($_POST["edad"]) || !is_numeric($_POST["edad"]))
                $this->errores["edad"] = "Edad no válida.";
            else
                $this->valores["edad"] = intval($_POST["edad"]);

            if (empty($_POST["genero"]))
                $this->errores["genero"] = "Seleccione un género.";
            else
                $this->valores["genero"] = $_POST["genero"];

            if (!isset($_POST["pericia"]) || !is_numeric($_POST["pericia"]))
                $this->errores["pericia"] = "Seleccione pericia informática.";
            else
                $this->valores["pericia"] = intval($_POST["pericia"]);

            $dispositivoValido = ["Ordenador", "Tablet", "Móvil"];
            if (empty($_POST["dispositivo"]) || !in_array($_POST["dispositivo"], $dispositivoValido)) {
                $this->errores["dispositivo"] = "Seleccione un dispositivo válido.";
            } else {
                $this->valores["dispositivo"] = $_POST["dispositivo"];
            }

            if (empty($this->errores)) {
                $this->guardarEnBD();
            }
        }
    }

    private function guardarEnBD()
    {
        $idUsuario = $this->db->insertarUsuario(
            $this->valores["profesion"],
            $this->valores["edad"],
            $this->valores["genero"],
            $this->valores["pericia"]
        );

        $idDispositivo = $this->db->insertarDispositivo(
            $this->valores["dispositivo"]
        );

        $_SESSION["id_usuario"] = $idUsuario;
        $_SESSION["id_dispositivo"] = $idDispositivo;

        header("Location: formulario.php");
        exit();
    }

    public function mostrar()
    {
        function v($arr, $key)
        { return $arr[$key] ?? ""; }

        function e($arr, $key)
        { return isset($arr[$key]) ? "<span>{$arr[$key]}</span>" : ""; }

        echo "
        <form method='POST'>
            <h3>Datos del usuario</h3>

            <label for='profesion'>Profesión:</label><br>
            <input type='text' id='profesion' name='profesion' value='" . v($this->valores, "profesion") . "'> 
            " . e($this->errores, "profesion") . "<br><br>

            <label for='edad'>Edad:</label><br>
            <input type='number' id='edad' name='edad' value='" . v($this->valores, "edad") . "'> 
            " . e($this->errores, "edad") . "<br><br>

            <label for='genero'>Género:</label><br>
            <select id='genero' name='genero'>
                <option value=''>Seleccione</option>
                <option value='Hombre' " . (v($this->valores, "genero") == "Hombre" ? "selected" : "") . ">Hombre</option>
                <option value='Mujer' " . (v($this->valores, "genero") == "Mujer" ? "selected" : "") . ">Mujer</option>
                <option value='Otro' " . (v($this->valores, "genero") == "Otro" ? "selected" : "") . ">Otro</option>
            </select> 
            " . e($this->errores, "genero") . "<br><br>

            <label for='pericia'>Pericia informática (0–10):</label><br>
            <input type='number' id='pericia' name='pericia' min='0' max='10' value='" . v($this->valores, "pericia") . "'> 
            " . e($this->errores, "pericia") . "<br><br>

            <h3>Datos del dispositivo</h3>

            <label for='dispositivo'>Seleccione el dispositivo:</label><br>
            <select id='dispositivo' name='dispositivo'>
                <option value=''>Seleccione</option>
                <option value='Ordenador' " . (v($this->valores, "dispositivo") == "Ordenador" ? "selected" : "") . ">Ordenador</option>
                <option value='Tablet' " . (v($this->valores, "dispositivo") == "Tablet" ? "selected" : "") . ">Tablet</option>
                <option value='Móvil' " . (v($this->valores, "dispositivo") == "Móvil" ? "selected" : "") . ">Móvil</option>
            </select>
            " . e($this->errores, "dispositivo") . "<br><br>

            <input type='submit' value='Iniciar prueba'>
        </form>";
    }
}

$reg = new Registro();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Pruebas De Usabilidad</title>
    <meta name="author" content="Enol Monte Soto" />
    <meta name="description" content="Pruebas de usabilidad de la web MotoGP-Desktop." />
    <meta name="keywords" content="MotoGP, Ayuda" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../multimedia/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <header>
        <h1>MotoGP-Desktop</h1>
    </header>
    <main>
    <h2>Registro previo a la encuesta</h2>

    <?php $reg->mostrar(); ?>
    </main>
</body>

</html>
