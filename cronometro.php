<?php
    session_start();
    /**
     * Clase que representa el cronómetro.
     * Autor: Enol Monte Soto
     * Versión: 1
     */
    class Cronometro {

        private $tiempo;
        private $inicio;

        public function __construct() {
            $this->tiempo = 0;
            $this->inicio = 0;
        }

        public function arrancar() {
            $this->inicio = microtime(true);
        }

        public function parar() {
            $ahora = microtime(true);
            $this->tiempo = $ahora - $this->inicio;
        }

        public function mostrar() {
            $totalSegundos = $this->tiempo;
            $minutos = floor($totalSegundos / 60);
            $segundos = floor($totalSegundos % 60);
            $decimas = floor(($totalSegundos - floor($totalSegundos)) * 10);
            return sprintf("%02d:%02d.%01d", $minutos, $segundos, $decimas);
        }
    
    }

    // Guarda crono en sesión (si no hay).
    if (!isset($_SESSION['cronometro'])) {
        $_SESSION['cronometro'] = new Cronometro();
    }
    
    $cronometro = $_SESSION['cronometro'];
    $salida = "";

    if (count($_POST)>0) {   

        if(isset($_POST['botonArrancar'])) {
            $cronometro->arrancar();
        }

        if(isset($_POST['botonParar'])) {
            $cronometro->parar();
        }

        if(isset($_POST['botonMostrar'])) {
            $salida = $cronometro->mostrar();
        }

        $_SESSION['cronometro'] = $cronometro;
    }
?>
<!DOCTYPE HTML>

<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MotoGP-Cronómetro</title>
    <meta name ="author" content ="Enol Monte Soto" />
    <meta name ="description" content ="Cronómetro." />
    <meta name ="keywords" content ="MotoGP, Juegos, Cronómetro" />
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
            <a href="clasificaciones.php" title="Clasificaciones en MotoGP">Clasificaciones</a>
            <a href="juegos.html" title="Juegos en MotoGP-Desktop"  class="active">Juegos</a>
            <a href="ayuda.html" title="Ayuda de MotoGP-Desktop">Ayuda</a>
        </nav>
    </header>
        <p>Estás en: <a href="index.html" title="Inicio">Inicio</a> >> <a href="juegos.html" title="Inicio">Juegos</a> >>
        <strong>Cronómetro PHP</strong></p>
        <main>
            <h2>Cronómetro</h2>
            <p><?php echo $salida ?></p>
            <form action="#" method="post" name="cronometro">
                <input type = "submit" name = "botonArrancar" value = "Arrancar"/>
                <input type = "submit" name = "botonParar" value = "Parar"/>
                <input type = "submit" name = "botonMostrar" value = "Mostrar"/>
            </form>
        </main>
    </body>
</html>