<?php
function generateInvoiceNumber() {
    $prefix = 'INV';
    $uniqueId = uniqid();
    return $prefix . '-' . $uniqueId;
}
