<?php
// Carga la conexión y las funciones comunes
require 'config.php';
// Indica si hay un usuario con sesión iniciada
$logueado = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Inicio</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <!-- Barra superior con la marca y el menú -->
  <header class="topbar">
    <strong class="brand">NIKE</strong>
    <nav>
      <!-- Con sesión: saludo y cerrar sesión. Sin sesión: entrar o registrarse -->
      <?php if ($logueado): ?>
        <span>Hola, <?= e($_SESSION['nombre']) ?></span>
        <a href="logout.php">Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php">Iniciar sesión</a>
        <a href="register.php">Crear cuenta</a>
      <?php endif; ?>
    </nav>
  </header>
  <!-- Contenido principal -->
  <main class="home">
    <h1>Bienvenido a la tienda</h1>
    <p>Ropa, calzado y más.</p>
  </main>
</body>
</html>
