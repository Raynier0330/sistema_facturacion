<?php
include 'config.php';
include 'functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $invoiceNumber = generateInvoiceNumber();
    $date = $_POST['date'];
    $clientCode = $_POST['clientCode'];
    $clientName = $_POST['clientName'];
    $total = $_POST['total'];
    $comment = $_POST['comment'];

    // Iniciar transacción
    mysqli_begin_transaction($conn);

    try {
        // Insertar la factura
        $sql = "INSERT INTO facturas (numero, fecha, codigo_cliente, nombre_cliente, total, comentario) VALUES (?, ?, ?, ?, ?, ?)";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssssds", $invoiceNumber, $date, $clientCode, $clientName, $total, $comment);
            mysqli_stmt_execute($stmt);
            $invoiceId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Insertar los items de la factura
            $itemNames = $_POST['itemName'];
            $itemQuantities = $_POST['itemQuantity'];
            $itemPrices = $_POST['itemPrice'];
            $itemTotals = $_POST['itemTotal'];

            $sql = "INSERT INTO detalles_factura (id_factura, nombre_articulo, cantidad, precio, total) VALUES (?, ?, ?, ?, ?)";
            if ($stmt = mysqli_prepare($conn, $sql)) {
                for ($i = 0; $i < count($itemNames); $i++) {
                    mysqli_stmt_bind_param($stmt, "isidd", $invoiceId, $itemNames[$i], $itemQuantities[$i], $itemPrices[$i], $itemTotals[$i]);
                    mysqli_stmt_execute($stmt);
                }
                mysqli_stmt_close($stmt);
            }

            // Confirmar transacción
            mysqli_commit($conn);

            echo json_encode([
                'success' => true, 
                'message' => 'Factura guardada con éxito', 
                'invoiceNumber' => $invoiceNumber,
                'invoiceId' => $invoiceId,
                'pdfUrl' => 'create_invoice.php?id=' . $invoiceId
            ]);
        } else {
            throw new Exception("Error al preparar la consulta de factura");
        }
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        mysqli_rollback($conn);
        echo json_encode(['success' => false, 'message' => 'Error al guardar la factura: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método de solicitud inválido']);
}

mysqli_close($conn);
