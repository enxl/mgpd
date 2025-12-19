<?php
require_once "Db.php";
require_once "cronometro.php";

class Formulario
{
    private $preguntas;
    private $errores;
    private $respuestas = [];
    private $db;
    private $cronometro;

    public function __construct()
    {
        session_start();

        $this->preguntas = array(
            "¿Qué número de dorsal tiene Fabio Di Giannantonio?",
            "¿Por cuántos equipos pasó Fabio Di Giannantonio?",
            "¿Qué es, según esta web, una 'chicane'?",
            "Indique el titular de una noticia que aparezca en el sitio.",
            "¿En qué posición quedó Fabio Di Giannantonio en 2024?",
            "¿Cuántos habitantes tiene la localidad cercana al circuito de Sachsenring?",
            "¿Qué temperatura hizo el día de la carrera?",
            "¿Hubo mas de un 50% de humedad en los días de entrenamiento?",
            "¿Cuántos puntos hizo Fabio Di Giannantonio en 2024?",
            "Nombre un equipo que aparezca en el juego de cartas.",
            "Comentario del usuario sobre la experiencia:",
            "Valoración del usuario (0-10):"
        );

        $this->db = new Db();
        $this->cronometro = new Cronometro();

        if (!isset($_SESSION['cronometro_inicio'])) {
            $this->cronometro->arrancar();
            $_SESSION['cronometro_inicio'] = $this->cronometro->getTiempoInicio();
        } else {
            $this->cronometro->setTiempoInicio($_SESSION['cronometro_inicio']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->guardarRespuestas();
        }
    }

    private function guardarRespuestas()
    {
        if (count($_POST) > 0) {
            $todasContestadas = true;

            for ($i = 1; $i <= 10; $i++) {
                if (empty($_POST["p$i"])) {
                    $this->errores[$i] = "Debe responder a esta pregunta.";
                    $todasContestadas = false;
                } else {
                    $this->respuestas[$i] = $_POST["p$i"];
                }
            }

            if (empty($_POST["p11"])) {
                $this->errores[11] = "Debe escribir un comentario.";
                $todasContestadas = false;
            } else {
                $this->respuestas[11] = $_POST["p11"];
            }

            if (empty($_POST["p12"]) || !is_numeric($_POST["p12"])) {
                $this->errores[12] = "Debe ingresar una valoración numérica.";
                $todasContestadas = false;
            } else {
                $valoracion = intval($_POST["p12"]);
                if ($valoracion < 0 || $valoracion > 10) {
                    $this->errores[12] = "La valoración debe estar entre 0 y 10.";
                    $todasContestadas = false;
                } else {
                    $this->respuestas[12] = $valoracion;
                }
            }

            if (empty($_POST["p13"])) {
                $this->errores[13] = "Debe ingresar propuestas.";
                $todasContestadas = false;
            } else {
                $this->respuestas[13] = $_POST["p13"];
            }

            if ($todasContestadas) {
                $this->guardarEnBD();
            }
        }
    }

    private function guardarEnBD()
    {
        $this->cronometro->parar();
        $tiempo = $this->cronometro->getTiempoSegundos();

        $idUsuario = $_SESSION["id_usuario"] ?? null;
        $idDispositivo = $_SESSION["id_dispositivo"] ?? null;

        $comentarioUsuario = $this->respuestas[11] ?? null;
        $valoracion = $this->respuestas[12] ?? null;
        $propuestasMejora = $this->respuestas[13] ?? null;

        if (!$idUsuario || !$idDispositivo) {
            exit("<p>Error: no se encontraron IDs de sesión.</p>");
        }

        $idResultado = $this->db->insertarResultado(
            $idUsuario,
            $idDispositivo,
            $tiempo,
            true,
            $comentarioUsuario,
            $propuestasMejora,
            $valoracion
        );

        unset($_SESSION['cronometro_inicio']);
        $_SESSION["id_resultado"] = $idResultado;
        header("Location: observaciones.php");
        exit();
    }

    public function cargarFormulario()
    {
        echo "<form method='POST' action='#'>";

        for ($i = 1; $i <= 10; $i++) {
            $pregunta = $this->preguntas[$i - 1];
            $valor = isset($this->respuestas[$i]) ? htmlspecialchars($this->respuestas[$i]) : "";
            $error = isset($this->errores[$i]) ? "<span>{$this->errores[$i]}</span>" : "";
            echo "
                <p><strong>$i. $pregunta</strong></p>
                <p>
                    <input type='text' name='p$i' value='$valor'/>
                    $error
                </p>
            ";
        }

        $pregunta11 = $this->preguntas[10];
        $valor11 = isset($this->respuestas[11]) ? htmlspecialchars($this->respuestas[11]) : "";
        $error11 = isset($this->errores[11]) ? "<span>{$this->errores[11]}</span>" : "";
        echo "
            <p><strong>11. $pregunta11</strong></p>
            <p>
                <textarea name='p11' rows='4' cols='60'>$valor11</textarea>
                $error11
            </p>
        ";

        $pregunta12 = $this->preguntas[11];
        $valor12 = isset($this->respuestas[12]) ? htmlspecialchars($this->respuestas[12]) : "";
        $error12 = isset($this->errores[12]) ? "<span>{$this->errores[12]}</span>" : "";
        echo "
            <p><strong>12. $pregunta12</strong></p>
            <p>
                <input type='number' name='p12' min='0' max='10' value='$valor12'/>
                $error12
            </p>
        ";

        $pregunta13 = "Propuestas de mejora:";
        $valor13 = isset($this->respuestas[13]) ? htmlspecialchars($this->respuestas[13]) : "";
        $error13 = isset($this->errores[13]) ? "<span class='error'>{$this->errores[13]}</span>" : "";

        echo "
            <p><strong>13. $pregunta13</strong></p>
            <p>
                <textarea name='p13' rows='4' cols='60'>$valor13</textarea>
                $error13
            </p>
        ";


        echo "<p><input type='submit' value='Terminar prueba'/></p>";
        echo "</form>";
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
    <link rel="icon" href="../multimedia/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <style>
        .error {
            color: red;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <header>
        <h1>MotoGP-Desktop</h1>
    </header>
    <h2>Pruebas de usabilidad</h2>
    <?php
    $formulario = new Formulario();
    $formulario->cargarFormulario();
    ?>
</body>

</html>