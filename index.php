<?php
require "config.php";

// Solo usuarios con sesión iniciada
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
$uid = $_SESSION["usuario_id"];
$mensaje = "";
$tipo = "";

// Guardar pedido
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $marca     = (int)($_POST["marca"] ?? 0);
    $producto  = trim($_POST["producto"] ?? "");
    $cantidad  = (int)($_POST["cantidad"] ?? 0);
    $direccion = trim($_POST["direccion"] ?? "");
    $notas     = trim($_POST["notas"] ?? "");

    if ($marca < 1 || $producto === "" || $direccion === "" || $cantidad < 1 || $cantidad > 99) {
        $mensaje = "Selecciona categoría y marca, y completa producto, cantidad (1–99) y dirección.";
        $tipo = "err";
    } else {
        $ins = $conexion->prepare(
            "INSERT INTO pedidos (usuario_id, marca_id, producto, cantidad, direccion, notas) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $ins->bind_param("iisiss", $uid, $marca, $producto, $cantidad, $direccion, $notas);
        $ins->execute();
        $mensaje = "Pedido guardado.";
        $tipo = "ok";
    }
}

$categorias = $conexion->query("SELECT id, nombre FROM categorias ORDER BY id");

$q = $conexion->prepare(
    "SELECT p.creado, c.nombre AS categoria, m.nombre AS marca, p.producto, p.cantidad
     FROM pedidos p
     JOIN marcas m ON m.id = p.marca_id
     JOIN categorias c ON c.id = m.categoria_id
     WHERE p.usuario_id = ? ORDER BY p.id DESC"
);
$q->bind_param("i", $uid);
$q->execute();
$pedidos = $q->get_result()->fetch_all(MYSQLI_ASSOC);
?>
