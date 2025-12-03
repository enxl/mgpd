<?php
class Db
{
    private $servername = "localhost";
    private $username = "DBUSER2025";
    private $password = "DBPSWD2025";
    private $dbname = "UO287616_DB";

    private $db;

    public function __construct()
    {
        $this->db = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->db->connect_error) {
            exit("<p>Error conectando a MySQL: " . $this->db->connect_error . "</p>");
        }
    }

    public function insertarUsuario($profesion, $edad, $genero, $pericia_informatica)
    {
        $stmt = $this->db->prepare("INSERT INTO usuarios (profesion, edad, genero, pericia_informatica) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sisi", $profesion, $edad, $genero, $pericia_informatica);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function insertarDispositivo($nombre)
    {
        $stmt = $this->db->prepare("SELECT id_dispositivo FROM dispositivos WHERE nombre = ?");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $stmt->bind_result($id_existente);

        if ($stmt->fetch()) {
            $stmt->close();
            return $id_existente;
        }
        $stmt->close();

        $stmt = $this->db->prepare("INSERT INTO dispositivos (nombre) VALUES (?)");
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $id_nuevo = $stmt->insert_id;
        $stmt->close();

        return $id_nuevo;
    }


    public function insertarResultado($id_usuario, $id_dispositivo, $tiempo, $completada, $comentarios_usuario, $propuestas_mejora, $valoracion_usuario)
    {
        $stmt = $this->db->prepare("
            INSERT INTO resultados 
            (id_usuario, id_dispositivo, tiempo, completada, comentarios_usuario, propuestas_mejora, valoracion_usuario)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iidissi", $id_usuario, $id_dispositivo, $tiempo, $completada, $comentarios_usuario, $propuestas_mejora, $valoracion_usuario);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function insertarObservacion($id_resultado, $comentario)
    {
        $stmt = $this->db->prepare("
            INSERT INTO observaciones_facilitador (id_resultado, comentario)
            VALUES (?, ?)
        ");
        $stmt->bind_param("is", $id_resultado, $comentario);
        $stmt->execute();
        $id = $stmt->insert_id;
        $stmt->close();
        return $id;
    }

    public function anadirValoracionUsuario($id_resultado, $valoracion_usuario)
    {
        $stmt = $this->db->prepare("
        UPDATE resultados
        SET valoracion_usuario = ?
        WHERE id_resultado = ?
    ");
        $stmt->bind_param("ii", $valoracion_usuario, $id_resultado);
        $stmt->execute();
        $ok = $stmt->affected_rows > 0;
        $stmt->close();
        return $ok;
    }

    public function cerrar()
    {
        $this->db->close();
    }
}
?>