<?php
class Cronometro
{

    private $tiempo;
    private $inicio;

    public function __construct()
    {
        $this->tiempo = 0;
        $this->inicio = 0;
    }

    public function arrancar()
    {
        $this->inicio = microtime(true);
    }

    public function parar()
    {
        $ahora = microtime(true);
        $this->tiempo = $ahora - $this->inicio;
    }

    public function mostrar()
    {
        $totalSegundos = $this->tiempo;
        $minutos = floor($totalSegundos / 60);
        $segundos = floor($totalSegundos % 60);
        $decimas = floor(($totalSegundos - floor($totalSegundos)) * 10);
        return sprintf("%02d:%02d.%01d", $minutos, $segundos, $decimas);
    }

    public function getTiempoSegundos()
    {
        return $this->tiempo;
    }

}
?>