<?php
session_start();
require_once "Db.php";

if (!isset($_SESSION["id_resultado"])) {
    die("No hay resultado asociado.");
}

$db = new Db();

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $comentario = trim($_POST["comentario"]);
    $valoracion = isset($_POST["valoracion"]) ? intval($_POST["valoracion"]) : null;

    if ($comentario === "") {
        $mensaje = "Escribe un comentario.";
    } elseif ($valoracion === null || $valoracion < 0 || $valoracion > 10) {
        $mensaje = "La valoración debe estar entre 0 y 10.";
    } else {
        $db->insertarObservacion($_SESSION["id_resultado"], $comentario);
        $db->anadirValoracionUsuario($_SESSION["id_resultado"], $valoracion);
        unset($_SESSION["id_resultado"]);
        $mensaje = "Observación y valoración guardadas correctamente.";
    }
}
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

<h2>Observaciones del facilitador</h2>
<form method="POST">
    <label>Comentario adicional:</label><br>
    <textarea name="comentario" rows="6" cols="60"></textarea><br><br>

    <label>Valoración del usuario (0-10):</label><br>
    <input type="number" name="valoracion" min="0" max="10"><br><br>

    <input type="submit" value="Guardar observación">
</form>

<p><?= $mensaje ?></p>

</body>
</html>
