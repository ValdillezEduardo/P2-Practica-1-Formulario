<?php
// Carga la conexión y las funciones comunes
require 'config.php';

// Si ya hay sesión iniciada, va a la página de inicio
if (isset($_SESSION['usuario_id'])) { header('Location: index.php'); exit; }

// $errores guarda los mensajes a mostrar y $v conserva lo que se escribió
$errores = [];
$v = ['nombre' => '', 'apellido' => '', 'email' => ''];

// Se ejecuta solo cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica el token de seguridad
    if (!csrf_valido()) { $errores[] = 'La sesión expiró. Recarga la página e inténtalo de nuevo.'; }

    // Lee y limpia los datos enviados
    $v['nombre']   = trim($_POST['nombre'] ?? '');
    $v['apellido'] = trim($_POST['apellido'] ?? '');
    $v['email']    = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';

    // Validaciones de los campos
    if ($v['nombre'] === '' || $v['apellido'] === '') $errores[] = 'Escribe tu nombre y apellido.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errores[] = 'Escribe un correo válido.';
    if (strlen($pass) < 8) $errores[] = 'La contraseña debe tener al menos 8 caracteres.';
    if ($pass !== $pass2)  $errores[] = 'Las contraseñas no coinciden.';

    // Si no hay errores, guarda el usuario en la base de datos
    if (!$errores) {
        try {
            // Inserta el usuario; la contraseña se guarda cifrada
            $st = db()->prepare(
                'INSERT INTO usuarios (nombre, apellido, email, password_hash) VALUES (?, ?, ?, ?)'
            );
            $st->execute([$v['nombre'], $v['apellido'], $v['email'], password_hash($pass, PASSWORD_DEFAULT)]);
            // Cuenta creada: envía al inicio de sesión
            header('Location: login.php?registro=ok');
            exit;
        // Código 23000: el correo ya existe (es único en la tabla)
        } catch (PDOException $ex) {
            $errores[] = ($ex->getCode() === '23000')
                ? 'Ya existe una cuenta con ese correo. Inicia sesión.'
                : 'No se pudo crear la cuenta. Revisa que MySQL esté activo en XAMPP.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear cuenta</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <!-- Pantalla dividida: texto a la izquierda y formulario a la derecha -->
  <main class="auth">
    <section class="auth-hero" aria-hidden="true"><p>Hazlo<br>ahora.</p></section>
    <section class="auth-panel">
      <h1>Crea tu cuenta</h1>
      <p class="sub">Regístrate para guardar tus compras y tus favoritos.</p>

      <!-- Mensajes de error -->
      <?php if ($errores): ?>
        <div class="alert" role="alert">
          <?php foreach ($errores as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <!-- Formulario de registro (el campo oculto es el token de seguridad) -->
      <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="row">
          <label>Nombre<input type="text" name="nombre" value="<?= e($v['nombre']) ?>" required autocomplete="given-name"></label>
          <label>Apellido<input type="text" name="apellido" value="<?= e($v['apellido']) ?>" required autocomplete="family-name"></label>
        </div>
        <label>Correo electrónico<input type="email" name="email" value="<?= e($v['email']) ?>" required autocomplete="email"></label>
        <label>Contraseña<input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
        <label>Repite la contraseña<input type="password" name="password2" minlength="8" required autocomplete="new-password"></label>
        <button type="submit">Crear cuenta</button>
      </form>

      <!-- Enlace al inicio de sesión -->
      <p class="alt">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    </section>
  </main>
</body>
</html>
