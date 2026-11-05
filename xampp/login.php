<?php
// Carga la conexión y las funciones comunes
require 'config.php';

// Si ya hay sesión iniciada, va a la página de inicio
if (isset($_SESSION['usuario_id'])) { header('Location: index.php'); exit; }

// $error guarda el mensaje a mostrar y $email conserva lo que se escribió
$error = '';
$email = '';

// Se ejecuta solo cuando se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    // Verifica el token de seguridad
    if (!csrf_valido()) {
        $error = 'La sesión expiró. Recarga la página e inténtalo de nuevo.';
    } else {
        try {
            // Busca al usuario por su correo
            $st = db()->prepare('SELECT id, nombre, password_hash FROM usuarios WHERE email = ?');
            $st->execute([$email]);
            $u = $st->fetch();

            // Compara la contraseña escrita con la guardada (cifrada)
            if ($u && password_verify($pass, $u['password_hash'])) {
                // Renueva el ID de sesión por seguridad
                session_regenerate_id(true);
                // Guarda los datos del usuario en la sesión y va al inicio
                $_SESSION['usuario_id'] = $u['id'];
                $_SESSION['nombre']     = $u['nombre'];
                header('Location: index.php');
                exit;
            }
            // Si llega aquí, los datos no coinciden
            $error = 'Correo o contraseña incorrectos.';
        } catch (PDOException $ex) {
            $error = 'No se pudo conectar. Revisa que MySQL esté activo en XAMPP.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Iniciar sesión</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <!-- Pantalla dividida: texto a la izquierda y formulario a la derecha -->
  <main class="auth">
    <section class="auth-hero" aria-hidden="true"><p>Sigue<br>moviéndote.</p></section>
    <section class="auth-panel">
      <h1>Inicia sesión</h1>
      <p class="sub">Entra con tu correo y contraseña.</p>

      <!-- Aviso al venir del registro -->
      <?php if (($_GET['registro'] ?? '') === 'ok'): ?>
        <div class="ok" role="status"><p>Cuenta creada. Ya puedes iniciar sesión.</p></div>
      <?php endif; ?>
      <!-- Mensaje de error -->
      <?php if ($error): ?>
        <div class="alert" role="alert"><p><?= e($error) ?></p></div>
      <?php endif; ?>

      <!-- Formulario de inicio de sesión (el campo oculto es el token de seguridad) -->
      <form method="post" novalidate>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <label>Correo electrónico<input type="email" name="email" value="<?= e($email) ?>" required autocomplete="email"></label>
        <label>Contraseña<input type="password" name="password" required autocomplete="current-password"></label>
        <button type="submit">Iniciar sesión</button>
      </form>

      <!-- Enlace al registro -->
      <p class="alt">¿Aún no tienes cuenta? <a href="register.php">Regístrate</a></p>
    </section>
  </main>
</body>
</html>
