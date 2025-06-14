<?php
session_start();  
include("clases/mysql.inc.php");	
include("clases/SanitizarEntrada.php");
include("comunes/loginfunciones.php");
include("clases/objLoginAdmin.php");

$db = new mod_db();
$tolog = false;

if (isset($_POST["tolog"])) {
    $tolog = $_POST["tolog"];
}

if ($tolog == "true" && $_SERVER['REQUEST_METHOD'] === 'POST') {

    $Usuario = $_POST['usuario'];
    $ClaveKey = $_POST['contrasena'];
    $ipRemoto = $_SERVER['REMOTE_ADDR'];

    $Logearme = new ValidacionLogin($Usuario, $ClaveKey, $ipRemoto, $db);

    if ($Logearme->logger()) {
        $Logearme->autenticar();

        if ($Logearme->getIntentoLogin()) {

            $_SESSION['autenticado'] = "SI";
            $_SESSION['Usuario'] = $Logearme->getUsuario();

            $nombreUsuario = $Logearme->getUsuario();
            $sql = "SELECT id, secret_2fa FROM usuarios WHERE Usuario = '" . addslashes($nombreUsuario) . "'";
            $result = $db->query($sql);
            $row = $result ? $result->fetch(PDO::FETCH_ASSOC) : null;

            if ($row && !empty($row['secret_2fa'])) {
                $_SESSION['secret_2fa'] = $row['secret_2fa'];
                $_SESSION['usuario_id'] = $row['id'];

                $Logearme->registrarIntentos();

                // === Trazabilidad: registrar login exitoso ===
                // Se asume que tienes un objeto PDO en $db->getConexion()
                try {
                    $pdo = $db->getConexion();
                    $stmt = $pdo->prepare("INSERT INTO trazabilidad (usuario_id, accion, ip) VALUES (?, 'login', ?)");
                    $stmt->execute([$row['id'], $ipRemoto]);
                    // Si quieres registrar detalles extra, añade un cuarto parámetro.
                } catch (Exception $e) {
                    // Puedes agregar logs de error si lo deseas, pero no interrumpas el login.
                }
                // === Fin trazabilidad ===

                redireccionar("codigo_2fa.php");
            } else {
                echo "❌ No se encontró un secreto 2FA válido para este usuario.";
                exit();
            }

        } else {
            $Logearme->registrarIntentos();
            $_SESSION["emsg"] = 1;
            redireccionar("login.php");
        }

    } else {
        $_SESSION["emsg"] = 1;
        redireccionar("login.php");
    }

} else {
    redireccionar("login.php");
}
?>