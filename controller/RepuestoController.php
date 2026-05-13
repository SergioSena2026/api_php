<?php
// para que React pueda leer este PHP
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Incluimos los archivos necesarios
include_once '../config/Database.php';
include_once '../models/Repuesto.php';

// Inicializamos la base de datos
$database = new Database();
$db = $database->getConnection();

// Inicializamos el objeto Repuesto
$repuesto = new Repuesto($db);

// Detectamos qué tipo de petición llega
$metodo = $_SERVER['REQUEST_METHOD'];

// switch depende del método
switch($metodo) {
    case 'GET':
        // Consultamos los datos
        $stmt = $repuesto->leer();
        $num = $stmt->rowCount(); // Contador. cuántos registros hay

        // Si hay datos, armamos el JSON
        if($num > 0) {
            $repuestos_arr = array();
            // Recorremos los resultados fila por fila
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // extract($row) crea variables automáticas con los nombres de las columnas
                extract($row);

                // Creamos un formato limpio para enviar a React
                $item = array(
                    "id" => $id,
                    "nombre_repuesto" => $nombre_repuesto,
                    "cantidad" => $cantidad,
                    "precio" => $precio
                );
                // Lo metemos en nuestra lista principal
                array_push($repuestos_arr, $item);
            }

            // Respondemos con código 200 y el JSON con los datos
            http_response_code(200);
            echo json_encode($repuestos_arr);
        } else {
            // Si no hay datos, enviamos código 404 y mensaje
            http_response_code(404);
            echo json_encode(array("message" => "No se encontraron repuestos."));
        }
        break;

    case 'POST':
        // Leemos el JSON de la petición
        $datos = json_decode(file_get_contents("php://input"));

        // Verificamos que los datos no lleguen vacíos
        if(!empty($datos->nombre_repuesto) && !empty($datos->cantidad) && !empty($datos->precio)) {
            
            // Pasamos los datos del JSON al modelo
            $repuesto->nombre_repuesto = $datos->nombre_repuesto;
            $repuesto->cantidad = $datos->cantidad;
            $repuesto->precio = $datos->precio;

            // Intentamos ejecutar la inserción en la base de datos
            if($repuesto->crear()) {
                http_response_code(201); // 201 significa Creado
                echo json_encode(array("message" => "Repuesto guardado en el inventario."));
            } else {
                http_response_code(503); // Error de servidor
                echo json_encode(array("message" => "Error técnico al guardar el repuesto."));
            }
        } else {
            http_response_code(400); // Bad Request datos incompletos
            echo json_encode(array("message" => "Faltan datos. Asegúrate de enviar nombre, cantidad y precio."));
        }
        break;
    
    case 'PUT':
        // Lógica para ACTUALIZAR
        $datos = json_decode(file_get_contents("php://input"));
        
        // Para actualizar necesitamos obligatoriamente el ID
        if(!empty($datos->id)) {
            $repuesto->id = $datos->id;
            $repuesto->nombre_repuesto = $datos->nombre_repuesto;
            $repuesto->cantidad = $datos->cantidad;
            $repuesto->precio = $datos->precio;

            if($repuesto->actualizar()) {
                echo json_encode(array("message" => "Repuesto actualizado con éxito."));
            } else {
                echo json_encode(array("message" => "No se pudo actualizar el repuesto."));
            }
        }
        break;

    case 'DELETE':
        // Lógica para ELIMINAR
        $datos = json_decode(file_get_contents("php://input"));
        
        if(!empty($datos->id)) {
            $repuesto->id = $datos->id;
            if($repuesto->eliminar()) {
                echo json_encode(array("message" => "Repuesto eliminado."));
            } else {
                echo json_encode(array("message" => "Error al eliminar."));
            }
        }
        break;
}
?>


        