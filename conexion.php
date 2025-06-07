<?php
$host = "localhost";
$usuario = "labo2fa";
$clave = "GoLoNdRiNa56(/)"; // Deja vacío si estás usando WAMP sin contraseña
$bd = "autenticador2fa"; // Cámbialo por el nombre real de tu base de datos

$conn = new mysqli($host, $usuario, $clave, $bd);

if ($conn->connect_error) {
    die("Error en la conexión: " . $conn->connect_error);
}
?>
