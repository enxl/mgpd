<?php
    /**
     * Clase que representa el cronómetro.
     * Autor: Enol Monte Soto
     * Versión: 1
    */
    class Clasificaciones {

        private $documento;

        public function __construct() {
            $this->documento = "xml/circuitoEsquema.xml";
        }

        public function consultar() {
            $datos = file_get_contents($this->documento);
            if($datos === false) {
                return false;
            }
            $xml = new SimpleXMLElement($datos);
            return $xml;
        }

        public function formatearDuracion(string $duracion): string {
            preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+(?:\.\d+)?)S)?/', $duracion, $m);
    
            $horas = isset($m[1]) ? (int)$m[1] : 0;
            $minutos = isset($m[2]) ? (int)$m[2] : 0;
            $segundos = isset($m[3]) ? $m[3] : 0;
    
            $horas = sprintf("%02d", $horas);
            $minutos = sprintf("%02d", $minutos);

            if (is_numeric($segundos) && floor($segundos) == $segundos) {
                $segundos = sprintf("%02d", $segundos);
            }
    
            return "{$horas}h, {$minutos}m, {$segundos}s";
        }

    }
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MotoGP-Clasificaciones</title>
    <meta name ="author" content ="Enol Monte Soto" />
    <meta name ="description" content ="Clasificaciones de MotoGP." />
    <meta name ="keywords" content ="MotoGP, Clasificaciones" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="multimedia/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css"/>
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>

<body>
    <!-- Datos con el contenidos que aparece en el navegador -->
    <header>
        <h1><a href="index.html" title="Página principal">MotoGP-Desktop</a></h1>
        <nav>
            <a href="index.html" title="Página principal">Inicio</a>
            <a href="piloto.html" title="Información del piloto">Piloto</a>
            <a href="circuito.html" title="Información del circuito">Circuito</a>
            <a href="meteorologia.html" title="Información meteorológica">Meteorología</a>
            <a href="clasificaciones.php" title="Clasificaciones en MotoGP" class="active">Clasificaciones</a>
            <a href="juegos.html" title="Juegos en MotoGP-Desktop">Juegos</a>
            <a href="ayuda.html" title="Ayuda de MotoGP-Desktop">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="index.html" title="Inicio">Inicio</a> >> <strong>Clasificaciones</strong></p>
    
    <main>
         <h2>Clasificaciones de MotoGP</h2>
         <?php
            $clasificaciones = new Clasificaciones();
            $xml = $clasificaciones->consultar();
            $tiempo = (string)$xml->vencedor['tiempo'];
            $tiempoFormateado = $clasificaciones->formatearDuracion($tiempo);

            if($xml) {
                echo "<h3>Ganador de la carrera</h3>";
                echo "<p>$xml->vencedor</p>";
                echo "<p>En un tiempo de $tiempoFormateado</p>";
                echo "<h3>Cabeza de clasificación tras la carrera</h3>";
                echo "<ol>";
                foreach ($xml->clasificados->clasificado as $c) {
                    $nombre = (string)$c;
                    echo "<li>$nombre</li>";
                }
                echo "</ol>";

            } else {
                echo "<p>Error al leer el fichero.</p>";
            }    
         ?>
    </main>
</body>
</html>