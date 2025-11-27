<?php

    class BaseDatos {

        private $db;
 
        public function conectarBaseDatos() {
            $servername = "localhost";
            $username = "DBUSER2025";
            $password = "DBPSWD2025";
            $this->db = new mysqli($servername, $username, $password);
            if($this->comprobarConexion()) {
                $cadenaSQL = "CREATE DATABASE IF NOT EXISTS UO287616_DB COLLATE utf8_spanish_ci";
                if($this->db->query($cadenaSQL) === TRUE){
                    echo "<p>Base de datos creada con éxito</p>";
                } else { 
                    echo "<p>Error en la creación de la Base de Datos. Error: " . $this->db->error . "</p>";
                    exit();
                }
            } else {
                echo "<p>No se ha podido conectar con la base de datos.</p>";
            }
        }

        private function comprobarConexion():bool {
            if($this->db->connect_error) {
                return false; 
            } else {
                return true;
            }
        }

    }

    $baseDatos = new BaseDatos();
    $baseDatos->conectarBaseDatos();

?>