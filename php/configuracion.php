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

            if(empty($tablas)) {
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
            // TODO;
        }

        public function eliminarBD() {

            $this->db->close();
            $db = new mysqli($this->servername, $this->username, $this->password);

            if ($db->connect_error) {
                exit("<p>ERROR de conexión: " . $db->connect_error . "</p>");
            }

            $query = "DROP DATABASE `$this->dbname`;";

            if ($db->query($query)) {
                echo "<p>Eliminada la base de datos '$this->dbname'</p>";
            } else {
                echo "<p>No se ha podido eliminar la base de datos '$this->dbname'. Error: " . $db->error . "</p>";
            }

            $db->close();

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