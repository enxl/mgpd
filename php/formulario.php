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
            "¿Quién ganó la carrera de Sachsenring 2024?",
            "¿Quién quedó como segundo clasificado en el mundial tras la carrera?",
            "¿Cuántos habitantes tiene la localidad cercana al circuto de Sachsenring?",
            "¿Qué temperatura hizo el día de la carrera?",
            "¿Hubo mas de un 50% de humedad en los días de entrenamiento?",
            "¿Cuántos puntos hizo Fabio Di Giannantonio en 2024?",
            "Nombre un equipo que aparezca en el juego de cartas."
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
            for ($i = 1; $i <= count($this->preguntas); $i++) {
                if (empty($_POST["p$i"])) {
                    $this->errores[$i] = "Debe responder a esta pregunta.";
                    $todasContestadas = false;
                } else {
                    $this->respuestas[$i] = $_POST["p$i"];
                }
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

    if (!$idUsuario || !$idDispositivo) {
        exit("<p>Error: no se encontraron IDs de sesión.</p>");
    }

    // Guardar resultado en BD
    $idResultado = $this->db->insertarResultado(
        $idUsuario,
        $idDispositivo,
        $tiempo,
        true,
        "",
        "",
        5
    );

    unset($_SESSION['cronometro_inicio']); 
    $_SESSION["id_resultado"] = $idResultado;
    header("Location: observaciones.php");
    exit();
}

    public function cargarFormulario()
    {
        echo "<form method='POST' action='#'>";
        foreach ($this->preguntas as $i => $pregunta) {
            $num = $i + 1;
            $valor = isset($this->respuestas[$num]) ? htmlspecialchars($this->respuestas[$num]) : "";
            $error = isset($this->errores[$num]) ? "<span>{$this->errores[$num]}</span>" : "";
            echo "
                <p>$num. $pregunta</p>
                <p>
                    <input type='text' name='p$num' value='$valor'/>
                    $error
                </p>
            ";
        }
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