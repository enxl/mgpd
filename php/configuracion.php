<?php
    class Configuracion {

        private $servername = "localhost";
        private $username   = "DBUSER2025";
        private $password   = "DBPSWD2025";
        private $dbname     = "UO287616_DB";
        
        private $db;

        public function __construct() {
            $this->db = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
            if ($this->db->connect_error) {
                exit("<p>Error conectando a MySQL: " . $this->db->connect_error . "</p>");
            }
        }

        public function reiniciarBD() {
            $tablas = $this->obtenerTodasLasTablas();

            if(count($tablas) == 0) {
                echo "<p>No se han encontrado tablas.</p>";
                return;
            }

            foreach ($tablas as $tabla) {
                $consulta = "DELETE FROM `$tabla`";

                if (!$this->db->query($consulta)) {
                    echo "<p>Error reiniciando la tabla $tabla: " . $this->db->error . "</p>";
                }

                $this->db->query("ALTER TABLE `$tabla` AUTO_INCREMENT = 1");
            }   

            echo "<p>Todas las tablas han sido reiniciadas.</p>";
        }


        public function exportarBD() {

        }

        public function eliminarBD() {
            $consulta = "DROP DATABASE IF EXISTS uo287616_db";
            if($this->db->query(query)) {
                 echo "<p>Base de datos eliminada correctamente.</p>";
            } else {
                 echo "<p>Error eliminando base de datos: " . $this->db->error . "</p>";
            };
        }

        public function obtenerTodasLasTablas(): array {
            $resultado = $this->db->query("SHOW TABLES");
            $tablas = [];

            while ($fila = $resultado->fetch_array()) {
                $tablas[] = $fila[0];
            }
        
            return $tablas;
        }

    }

    $conf= new Configuracion();
?>