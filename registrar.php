<?php
session_start();
require 'conexion.php';
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

class Registro
{
    private $conn;
    public $error = '';
    public $qrUrl = '';
    public $secret = '';

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function procesar($post)
    {
        $nombre = $post['nombre'] ?? '';
        $apellido = $post['apellido'] ?? '';
        $correo = $post['correo'] ?? '';
        $clave = $post['clave'] ?? '';
        $sexo = $post['sexo'] ?? '';
        $usuario = $post['usuario'] ?? '';

        // Validar campos obligatorios
        if (!$nombre || !$apellido || !$correo || !$usuario || !$clave || !$sexo) {
            $this->error = "Todos los campos son obligatorios.";
            return false;
        }

        // Validar si el correo ya existe
        $sql_check = "SELECT id FROM usuarios WHERE correo = ?";
        $stmt = $this->conn->prepare($sql_check);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $this->error = "❌ Este correo ya está registrado. <a href='registro.php'>Volver</a>";
            $stmt->close();
            return false;
        }
        $stmt->close();

        // Validar si el usuario ya existe
        $sql_check = "SELECT id FROM usuarios WHERE Usuario = ?";
        $stmt = $this->conn->prepare($sql_check);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $this->error = "❌ Este Usuario ya existe. <a href='registro.php'>Volver</a>";
            $stmt->close();
            return false;
        }
        $stmt->close();

        // Generar secreto y hashear contraseña
        $g = new GoogleAuthenticator();
        $secret = $g->generateSecret();
        $clave_hash = password_hash($clave, PASSWORD_DEFAULT);

        // Guardar nuevo usuario
        $sql_insert = "INSERT INTO usuarios (nombre, apellido, Usuario, correo, HashMagic, sexo, secret_2fa)
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql_insert);
        $stmt->bind_param("sssssss", $nombre, $apellido, $usuario, $correo, $clave_hash, $sexo, $secret);

        if ($stmt->execute()) {
            $_SESSION['usuario_id'] = $this->conn->insert_id;
            $_SESSION['Usuario'] = $correo;
            $_SESSION['secret_2fa'] = $secret;

            $this->secret = $secret;
            $this->qrUrl = GoogleQrUrl::generate($correo, $secret, 'Sistema2FA');
            $stmt->close();
            return true;
        } else {
            $this->error = "Error al registrar: " . $stmt->error;
            $stmt->close();
            return false;
        }
    }
}

// --- Flujo principal ---
$registro = new Registro($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo '<!DOCTYPE html>
    <html lang="es">
    <head>
        <title>Resultado del registro</title>
        <link rel="stylesheet" href="css/estilosZ.css">
    </head>
    <body>
      <div class="registro-resultado">';
    if ($registro->procesar($_POST)) {
        echo "<h2>Registro exitoso</h2>";
        echo "<p>Escanea este código QR con Google Authenticator o ingresa el código manualmente:</p>";
        echo "<img src='https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($registro->qrUrl) . "' alt='QR 2FA' />";
        echo "<br><br>";
        echo "<strong>Código secreto manual:</strong><br>";
        echo "<code>" . htmlspecialchars($registro->secret) . "</code>";
        echo "<br><a href='login_form.php'>Ir al login</a>";
    } else {
        echo "<div class='error'>{$registro->error}</div>";
        echo "<a href='registro.php'>Volver al registro</a>";
    }
    echo '</div></body></html>';
} 
$conn->close();
?>
