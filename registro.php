<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro</title>
    <link rel="stylesheet" href="css/estilosZ.css">
</head>
<body>
  <div class="registro-container">
    <h2>Registro de usuario</h2>
    <form method="POST" action="registrar.php">
      <label>Nombre:</label>
      <input type="text" name="nombre" required>

      <label>Apellido:</label>
      <input type="text" name="apellido" required>
      
      <label>Nombre de usuario:</label>
      <input type="text" name="usuario" required>

      <label>Correo:</label>
      <input type="email" name="correo" required>

      <label>Contraseña:</label>
      <input type="password" name="clave" required>

      <label>Sexo:</label>
      <select name="sexo" required>
        <option value="M">Masculino</option>
        <option value="F">Femenino</option>
      </select>

      <button type="submit">Registrarse</button>
    </form>
  </div>
</body>
</html>
