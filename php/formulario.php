<?php
class Formulario
{

    private $preguntas;
    private $errores;
    private $respuestas = [];

    public function __construct()
    {
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
        $this->guardarRespuestas();
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
        $id_usuario = 1;       
        $id_dispositivo = 1;   
        $tiempo = 0;           
        $completada = 1;
        $comentarios = implode("; ", $this->respuestas);
        $propuestas = "";      
        $valoracion = 5;

        $db = new mysqli("localhost", "DBUSER2025", "DBPSWD2025", "UO287616_DB");
        $stmt = $db->prepare("INSERT INTO resultados 
            (id_usuario, id_dispositivo, tiempo, completada, comentarios_usuario, propuestas_mejora, valoracion_usuario) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidissi", $id_usuario, $id_dispositivo, $tiempo, $completada, $comentarios, $propuestas, $valoracion);
        $stmt->execute();
        $stmt->close();
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
        echo "<p><input type='submit' value='Enviar'/></p>";
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