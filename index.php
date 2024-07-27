<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Facturación - La Rubia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Facturación - La Rubia</h1>
        <form id="invoiceForm">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="date" class="form-label">Fecha y Hora</label>
                    <input type="text" class="form-control" id="date" name="date" readonly>
                </div>
                <div class="col-md-6">
                    <label for="clientCode" class="form-label">Código del Cliente</label>
                    <input type="text" class="form-control" id="clientCode" name="clientCode" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="clientName" class="form-label">Nombre del Cliente</label>
                <input type="text" class="form-control" id="clientName" name="clientName" required>
            </div>
            <div id="itemsContainer">
                <!-- Aquí se agregarán dinámicamente los items -->
            </div>
            <button type="button" class="btn btn-secondary mb-3" id="addItem">
                <i class="fas fa-plus"></i> Agregar Artículo
            </button>
            <div class="mb-3">
                <label for="total" class="form-label">Total a Pagar</label>
                <input type="number" class="form-control" id="total" name="total" readonly>
            </div>
            <div class="mb-3">
                <label for="comment" class="form-label">Comentario</label>
                <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Guardar e Imprimir
            </button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>