<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registrar";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Permitir CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Establecer el encabezado de contenido como JSON
header('Content-Type: application/json');

// Verificar el método de solicitud HTTP
$method = $_SERVER['REQUEST_METHOD'];

// Ruta de la API
switch ($method) {
    case 'GET':
        obtenerUsuarios();
        break;
    case 'POST':
        registrarUsuario();
        break;
    case 'OPTIONS':
        // Manejar la solicitud OPTIONS para CORS
        http_response_code(200);
        break;
    default:
        http_response_code(405);
        echo json_encode(["message" => "Método no permitido"]);
        break;
}

// Función para obtener usuarios
function obtenerUsuarios() {
    global $conn;

    $sql = "SELECT id, nombre, email FROM usuarios";
    $result = $conn->query($sql);

    $usuarios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }
    echo json_encode($usuarios);
}

// Función para registrar un usuario
function registrarUsuario() {
    global $conn;

    // Recibir los datos enviados en la solicitud
    $data = json_decode(file_get_contents('php://input'), true);
    $nombre = $data['nombre'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Validar datos
    if (empty($nombre) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["message" => "Datos incompletos"]);
        return;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $email, $hashedPassword);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode(["message" => "Usuario registrado con éxito"]);
    } else {
        http_response_code(500);
        echo json_encode(["message" => "Error al registrar usuario"]);
    }
}

// Cerrar la conexión
$conn->close();
?>