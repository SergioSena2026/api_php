<?php
class Repuesto {
    // La conexión
    private $conn;
    // El nombre de la tabla en tu MySQL
    private $table_name = "repuestos";

    // Atributos de la tabla
    public $id;
    public $nombre_repuesto;
    public $cantidad;
    public $precio;

    // le pasamos la conexión a la base de datos
    public function __construct($db) {
        $this->conn = $db;
    }

    // Función para leer todos los repuestos
    public function leer() {
        $query = "SELECT * FROM " . $this->table_name;
        // evita inyecciones SQL
        $stmt = $this->conn->prepare($query);
        // Ejecutamos la consulta
        $stmt->execute();
        return $stmt;
    }

    // Función para crear un nuevo repuesto
    public function crear() {
        // Consulta SQL con parámetros
        $query = "INSERT INTO " . $this->table_name . " 
                SET nombre_repuesto=:nombre, cantidad=:cantidad, precio=:precio";

        $stmt = $this->conn->prepare($query);

        // Limpieza de datos para evitar inyecciones
        $this->nombre_repuesto = htmlspecialchars(strip_tags($this->nombre_repuesto));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->precio = htmlspecialchars(strip_tags($this->precio));

        // Vinculamos los valores reales a los marcadores de la consulta
        $stmt->bindParam(":nombre", $this->nombre_repuesto);
        $stmt->bindParam(":cantidad", $this->cantidad);
        $stmt->bindParam(":precio", $this->precio);

        // Ejecutamos
        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Función para ACTUALIZAR un repuesto existente
    public function actualizar() {
        // Consulta SQL WHERE id = :id 
        $query = "UPDATE " . $this->table_name . " 
                  SET nombre_repuesto = :nombre, 
                      cantidad = :cantidad, 
                      precio = :precio 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        // Limpieza de datos para evitar inyecciones
        $this->nombre_repuesto = htmlspecialchars(strip_tags($this->nombre_repuesto));
        $this->cantidad = htmlspecialchars(strip_tags($this->cantidad));
        $this->precio = htmlspecialchars(strip_tags($this->precio));
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vinculación de parámetros
        $stmt->bindParam(':nombre', $this->nombre_repuesto);
        $stmt->bindParam(':cantidad', $this->cantidad);
        $stmt->bindParam(':precio', $this->precio);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Función para ELIMINAR un repuesto
    public function eliminar() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Limpiamos el ID antes de procesar
        $this->id = htmlspecialchars(strip_tags($this->id));

        // Vinculamos el ID del repuesto que queremos borrar
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>