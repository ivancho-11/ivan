<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="stilos.css">
    <title>Registro e Inicio de Sesión</title>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="form-container">
                    <h2 class="text-end move-left">Registro de Usuario</h2>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <input type="hidden" name="action" value="registro">
                        <div class="mb-3">
                            <label for="nombre">Nombre:</label>
                            <input type="text" id="nombre" name="nombre" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="password">Contraseña:</label>
                            <input type="password" id="password" name="password" required class="form-control">
                        </div>
                        <input type="submit" value="Registrar" class="btn btn-primary">
                    </form>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-container">
                    <h2 class="text-start">Inicio de Sesión</h2>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
                        <input type="hidden" name="action" value="login">
                        <div class="mb-3">
                            <label for="login_email">Email:</label>
                            <input type="email" id="login_email" name="email" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="login_password">Contraseña:</label>
                            <input type="password" id="login_password" name="password" required class="form-control">
                        </div>
                        <input type="submit" value="Iniciar Sesión" class="btn btn-primary">
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>



<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configuración de la base de datos//


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "registrar";

// Crear conexión// 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
} else {
    echo "<p>Conexión exitosa a la base de datos</p>";
}

// Verificar conexión// 

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para registrar un nuevo usuario//

function registrarUsuario($nombre, $email, $password) {
    global $conn;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nombre, $email, $hashedPassword);
    
    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
}

// Función para iniciar sesión// 

function iniciarSesion($email, $password) {
    global $conn;
    
    $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return ["success" => true, "message" => "Autenticación satisfactoria", "user" => $user['nombre']];
        }
    }
    
    return ["success" => false, "message" => "Error en la autenticación"];
}

// Procesar la solicitud// 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $response = [];

    if ($action === 'registro') {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (registrarUsuario($nombre, $email, $password)) {
            $response = ["success" => true, "message" => "Usuario registrado con éxito"];
        } else {
            $response = ["success" => false, "message" => "Error al registrar el usuario"];
        }
    } elseif ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $response = iniciarSesion($email, $password);
    } else {
        $response = ["success" => false, "message" => "Acción no válida"];
    }

    // Mostrar la respuesta //

    echo json_encode($response);

    // Mostrar la respuesta de manera legible //

if (isset($response['message'])) {
    echo "<script>alert('" . addslashes($response['message']) . "');</script>";
}
}

// Cerrar la conexión //

$conn->close();
?>