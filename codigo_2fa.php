<?php
session_start();
if (!isset($_SESSION['Usuario']) || !isset($_SESSION['secret_2fa'])) {
    header("Location: login_form.php");
    exit();
}
$nombreUsuario = htmlspecialchars($_SESSION['Usuario']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Verificación 2FA</title>
  <link rel="stylesheet" href="css/estilosZ.css">
</head>
<body>
  <div class="container-2fa">
    <div class="bienvenido">Bienvenido, <?php echo $nombreUsuario; ?> 👋</div>
    <h2>Introduce el código de Google Authenticator</h2>
    <form method="POST" action="validar_2fa.php">
      <label for="codigo">Código:</label>
      <input type="text" name="codigo_2fa" required maxlength="6" autocomplete="one-time-code">
      <button type="submit">Verificar</button>
    </form>
  </div>
</body>
</html>
