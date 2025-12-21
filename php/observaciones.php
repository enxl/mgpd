<?php
session_start();
require_once "Db.php";

if (!isset($_SESSION["id_resultado"])) {
    die("No hay resultado asociado.");
}

$db = new Db();
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $comentario_facilitador = trim($_POST["comentario_facilitador"]);

    if ($comentario_facilitador === "") {
        $mensaje = "Escribe un comentario del facilitador.";
    } else {
        $db->insertarObservacion($_SESSION["id_resultado"], $comentario_facilitador);
        unset($_SESSION["id_resultado"]);
        $mensaje = "Observación del facilitador guardada correctamente.";
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
<main>
<h2>Observaciones del facilitador</h2>
<form method="POST">
    <label>Comentario del facilitador:</label><br>
    <textarea name="comentario_facilitador" rows="6" cols="60"></textarea><br><br>

    <input type="submit" value="Guardar">
</form>

<p><?= $mensaje ?></p>

</body>
</main>
</html>