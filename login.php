<?php
require "config.php";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $correo = strtolower(trim($_POST["correo"] ?? ""));
    $clave  = $_POST["clave"] ?? "";

    $q = $conexion->prepare("SELECT id, nombre, clave FROM usuarios WHERE correo = ?");
    $q->bind_param("s", $correo);
    $q->execute();
    $u = $q->get_result()->fetch_assoc();

    if ($u && password_verify($clave, $u["clave"])) {
        session_regenerate_id(true);
        $_SESSION["usuario_id"] = $u["id"];
        $_SESSION["nombre"] = $u["nombre"];
        header("Location: index.php");
        exit;
    }
    $error = "Correo o contraseña incorrectos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión · Tienda Online</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <header><h1>Tienda Online    EVA0903 5-J</h1></header>
  <main class="angosto">
    <section class="panel">
      <h2>Iniciar sesión</h2>
      <?php if ($error): ?><p class="msg err"><?= e($error) ?></p><?php endif; ?>
      <form method="post">
        <label for="correo">Correo</label>
        <input id="correo" name="correo" type="email" required>
        <label for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" required>
        <button class="btn" type="submit">Entrar</button>
      </form>
      <p class="muted">¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
    </section>
  </main>
</body>
</html>
