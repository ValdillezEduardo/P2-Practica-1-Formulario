<?php
// Devuelve en JSON las marcas de una categoría (lo usa js/app.js)
require "config.php";
header("Content-Type: application/json; charset=utf-8");

$cat = (int)($_GET["categoria"] ?? 0);
$q = $conexion->prepare("SELECT id, nombre FROM marcas WHERE categoria_id = ? ORDER BY nombre");
$q->bind_param("i", $cat);
$q->execute();
echo json_encode($q->get_result()->fetch_all(MYSQLI_ASSOC));
