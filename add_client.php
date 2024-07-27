<?php
include 'config.php';

if(isset($_POST['clientCode']) && isset($_POST['clientName'])) {
    $clientCode = $_POST['clientCode'];
    $clientName = $_POST['clientName'];
    
    $sql = "INSERT INTO clientes (codigo, nombre) VALUES (?, ?)";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ss", $clientCode, $clientName);
        if(mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Cliente agregado con éxito']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al agregar el cliente']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Datos de cliente incompletos']);
}

mysqli_close($conn);
