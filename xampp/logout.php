<?php
// Carga la conexión y las funciones comunes
require 'config.php';
// Borra los datos de la sesión y la destruye
$_SESSION = [];
session_destroy();
// Vuelve a la pantalla de inicio de sesión
header('Location: login.php');
exit;
