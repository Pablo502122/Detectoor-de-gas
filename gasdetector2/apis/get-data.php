<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli("localhost", "root", "", "gas_db");

if ($conn->connect_error) {
    echo json_encode(["error" => "Conexión fallida"]);
    exit;
}

// Últimas 20 lecturas para la gráfica (ordenadas ASC para que la gráfica fluya de izq a der)
$result = $conn->query("SELECT id, valor, estatus, fecha FROM lecturas ORDER BY id DESC LIMIT 20");
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$rows = array_reverse($rows); // Reordenar ASC

echo json_encode($rows);
$conn->close();
?>
