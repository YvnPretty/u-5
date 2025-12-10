<?php
// api/api_estudiantes.php

// Simulación de una conexión a la base de datos (reemplaza con tu conexión real)
function getPDOConnection() {
    $host = 'localhost'; // O tu host de DB
    $dbname = 'tu_base_de_datos'; // Nombre de tu base de datos
    $username = 'tu_usuario_db'; // Usuario de tu DB
    $password = 'tu_contrasena_db'; // Contraseña de tu DB
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (\PDOException $e) {
        // En un entorno de producción, no mostrarías $e->getMessage() directamente.
        // Lo registrarías y mostrarías un mensaje genérico.
        // header('Content-Type: application/json');
        // http_response_code(500);
        // echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos.']);
        // exit;
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

// Configurar cabeceras para respuesta JSON
header('Content-Type: application/json');
// Permitir solicitudes de cualquier origen (para desarrollo). 
// En producción, deberías restringirlo a tu dominio frontend.
header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Manejar solicitudes OPTIONS (preflight) para CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

$action = $_GET['action'] ?? ''; // Acción solicitada por el frontend
$pdo = getPDOConnection(); // Obtener conexión PDO

switch ($action) {
    case 'get_all':
        try {
            $stmt = $pdo->query("SELECT id_estudiante as id, nombre, carrera, email FROM t_estudiantes ORDER BY nombre ASC");
            $estudiantes = $stmt->fetchAll();
            echo json_encode(['status' => 'success', 'data' => $estudiantes]);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al obtener estudiantes: ' . $e->getMessage()]);
        }
        break;

    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);

            // Validación básica (deberías expandirla)
            if (empty($input['id']) || empty($input['nombre']) || empty($input['carrera']) || empty($input['email'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
                exit;
            }
            // Verificar si la matrícula ya existe
            $stmtCheck = $pdo->prepare("SELECT id_estudiante FROM t_estudiantes WHERE id_estudiante = :id");
            $stmtCheck->bindParam(':id', $input['id']);
            $stmtCheck->execute();
            if ($stmtCheck->fetch()) {
                http_response_code(409); // Conflict
                echo json_encode(['status' => 'error', 'message' => 'La matrícula ya existe.']);
                exit;
            }

            try {
                $sql = "INSERT INTO t_estudiantes (id_estudiante, nombre, carrera, email) VALUES (:id, :nombre, :carrera, :email)";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $input['id']);
                $stmt->bindParam(':nombre', $input['nombre']);
                $stmt->bindParam(':carrera', $input['carrera']);
                $stmt->bindParam(':email', $input['email']);
                
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Estudiante agregado exitosamente.', 'data' => ['id' => $input['id']]]);
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error al agregar estudiante.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido para esta acción. Se esperaba POST.']);
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Podrías usar PUT, pero POST es más simple con formularios HTML/JS básicos
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validación básica
            if (empty($input['id']) || empty($input['nombre']) || empty($input['carrera']) || empty($input['email'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios para actualizar.']);
                exit;
            }

            try {
                $sql = "UPDATE t_estudiantes SET nombre = :nombre, carrera = :carrera, email = :email WHERE id_estudiante = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $input['id']);
                $stmt->bindParam(':nombre', $input['nombre']);
                $stmt->bindParam(':carrera', $input['carrera']);
                $stmt->bindParam(':email', $input['email']);

                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['status' => 'success', 'message' => 'Estudiante actualizado exitosamente.']);
                    } else {
                        // No encontró el ID o los datos eran los mismos
                        http_response_code(404); // O 200 con un mensaje específico
                        echo json_encode(['status' => 'info', 'message' => 'No se encontró el estudiante o no hubo cambios.']);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error al actualizar estudiante.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido. Se esperaba POST (o PUT).']);
        }
        break;

    case 'delete':
         if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Podrías usar DELETE
            $input = json_decode(file_get_contents('php://input'), true);
            $id_estudiante = $input['id'] ?? null;

            if (empty($id_estudiante)) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'ID de estudiante no proporcionado.']);
                exit;
            }

            try {
                $sql = "DELETE FROM t_estudiantes WHERE id_estudiante = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id_estudiante);

                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
                        echo json_encode(['status' => 'success', 'message' => 'Estudiante eliminado exitosamente.']);
                    } else {
                        http_response_code(404);
                        echo json_encode(['status' => 'error', 'message' => 'Estudiante no encontrado.']);
                    }
                } else {
                    http_response_code(500);
                    echo json_encode(['status' => 'error', 'message' => 'Error al eliminar estudiante.']);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Error de base de datos: ' . $e->getMessage()]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Método no permitido. Se esperaba POST (o DELETE).']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
        break;
}
?>
