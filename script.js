$(document).ready(function() {
    let itemCount = 0;

    // Establecer la fecha y hora actual
    function setCurrentDateTime() {
        const now = new Date();
        const dateTime = now.toLocaleString('es-ES');
        $('#date').val(dateTime);
    }

    setCurrentDateTime();
    setInterval(setCurrentDateTime, 1000);  // Actualizar cada segundo

    $('#addItem').click(function() {
        itemCount++;
        const newItem = `
            <div class="row mb-3 item">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="itemName[]" placeholder="Nombre del artículo" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control quantity" name="itemQuantity[]" placeholder="Cantidad" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control price" name="itemPrice[]" placeholder="Precio" step="0.01" required>
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control itemTotal" name="itemTotal[]" placeholder="Total" readonly>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm removeItem">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        $('#itemsContainer').append(newItem);
    });

    $(document).on('click', '.removeItem', function() {
        $(this).closest('.item').remove();
        calculateGrandTotal();
    });

    $(document).on('input', '.quantity, .price', function() {
        calculateItemTotal($(this).closest('.item'));
        calculateGrandTotal();
    });

    function calculateItemTotal(item) {
        const quantity = parseFloat(item.find('.quantity').val()) || 0;
        const price = parseFloat(item.find('.price').val()) || 0;
        const total = quantity * price;
        item.find('.itemTotal').val(total.toFixed(2));
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        $('.itemTotal').each(function() {
            grandTotal += parseFloat($(this).val()) || 0;
        });
        $('#total').val(grandTotal.toFixed(2));
    }

    $('#clientCode').on('blur', function() {
        const clientCode = $(this).val();
        if (clientCode) {
            $.ajax({
                url: 'get_client.php',
                method: 'POST',
                data: { clientCode: clientCode },
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        if(response.exists) {
                            $('#clientName').val(response.name);
                        } else {
                            const clientName = prompt("Cliente no encontrado. Ingrese el nombre del nuevo cliente:");
                            if (clientName) {
                                $.ajax({
                                    url: 'add_client.php',
                                    method: 'POST',
                                    data: { clientCode: clientCode, clientName: clientName },
                                    dataType: 'json',
                                    success: function(addResponse) {
                                        if(addResponse.success) {
                                            $('#clientName').val(clientName);
                                            alert("Nuevo cliente agregado con éxito.");
                                        } else {
                                            alert("Error al agregar el nuevo cliente: " + addResponse.message);
                                        }
                                    },
                                    error: function() {
                                        alert('Error de conexión al agregar el nuevo cliente');
                                    }
                                });
                            } else {
                                $('#clientName').val('');
                            }
                        }
                    } else {
                        alert(response.message);
                        $('#clientName').val('');
                    }
                },
                error: function() {
                    alert('Error de conexión al buscar el cliente');
                }
            });
        }
    });

    $('#invoiceForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'save_invoice.php',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Factura guardada con éxito');
                    window.open(response.pdfUrl, '_blank');
                    // Limpiar el formulario después de guardar
                    $('#invoiceForm')[0].reset();
                    $('#itemsContainer').empty();
                    setCurrentDateTime();
                } else {
                    alert('Error al guardar la factura: ' + response.message);
                }
            },
            error: function() {
                alert('Error de conexión al guardar la factura');
            }
        });
    });
});
