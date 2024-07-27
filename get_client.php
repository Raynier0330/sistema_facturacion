<?php
include 'config.php';

if(isset($_POST['clientCode'])) {
    $clientCode = $_POST['clientCode'];
    
    $sql = "SELECT * FROM clientes WHERE codigo = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $clientCode);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($client = mysqli_fetch_assoc($result)) {
                echo json_encode(['success' => true, 'name' => $client['nombre'], 'exists' => true]);
            } else {
                echo json_encode(['success' => true, 'exists' => false]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al ejecutar la consulta']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Código de cliente no proporcionado']);
}

mysqli_close($conn);

