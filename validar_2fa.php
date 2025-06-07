<?php
session_start();
require 'vendor/autoload.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if (!isset($_SESSION['Usuario']) || !isset($_SESSION['secret_2fa'])) {
    header("Location: login_form.php");
    exit();
}

// Validar entrada del usuario
$codigo = $_POST['codigo_2fa'] ?? '';
$secret = $_SESSION['secret_2fa'] ?? '';

if (empty($codigo) || empty($secret)) {
    echo "⚠️ Código o secreto faltante. <a href='codigo_2fa.php'>Volver</a>";
    exit();
}

// Inicializar verificador
$g = new GoogleAuthenticator();

// Validar el código
if ($g->checkCode($secret, $codigo)) {
    $_SESSION['verificado_2fa'] = true;
    header("Location: formularios/PanelControl.php");
    exit();
} else {
    echo "❌ Código incorrecto. <a href='codigo_2fa.php'>Intentar de nuevo</a><br>";
    echo "💡 Asegúrate de escanear el QR generado en el registro más reciente y que la hora del dispositivo esté sincronizada.";
}
?>
