<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gas_db";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $valor = $_POST["valor"];
    // Definimos el estatus basado en el nivel
    $estatus = ($valor > 550) ? "Nivel Peligroso" : "Nivel Seguro";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) { die("Conexión fallida: " . $conn->connect_error); }

    $sql = "INSERT INTO lecturas (valor, estatus) VALUES ('$valor', '$estatus')";
    
    if ($conn->query($sql) === TRUE) { echo "Registro guardado"; } 
    else { echo "Error: " . $sql . "<br>" . $conn->error; }

    $conn->close();
}
