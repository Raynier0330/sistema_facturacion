<?php
require('fpdf/fpdf.php');
require('config.php');
require('functions.php');

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial','B',15);
        $this->Cell(80);
        $this->Cell(30,10,'Factura - La Rubia',0,0,'C');
        $this->Ln(20);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial','I',8);
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

if(isset($_GET['id'])) {
    $invoiceId = $_GET['id'];

    // Obtener datos de la factura
    $sql = "SELECT * FROM facturas WHERE id = ?";
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $invoiceId);
        if(mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            if($invoice = mysqli_fetch_assoc($result)) {
                $pdf = new PDF();
                $pdf->AliasNbPages();
                $pdf->AddPage();
                $pdf->SetFont('Arial','',12);

                // Información de la factura
                $pdf->Cell(0,10,'Número de Factura: '.$invoice['numero'],0,1);
                $pdf->Cell(0,10,'Fecha: '.$invoice['fecha'],0,1);
                $pdf->Cell(0,10,'Cliente: '.$invoice['nombre_cliente'],0,1);
                $pdf->Cell(0,10,'Código de Cliente: '.$invoice['codigo_cliente'],0,1);
                $pdf->Ln(10);

                // Encabezados de la tabla
                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(60,10,'Artículo',1);
                $pdf->Cell(30,10,'Cantidad',1);
                $pdf->Cell(50,10,'Precio',1);
                $pdf->Cell(50,10,'Total',1);
                $pdf->Ln();

                // Detalles de la factura
                $pdf->SetFont('Arial','',12);
                $sql = "SELECT * FROM detalles_factura WHERE id_factura = ?";
                if($stmt2 = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt2, "i", $invoiceId);
                    if(mysqli_stmt_execute($stmt2)) {
                        $result2 = mysqli_stmt_get_result($stmt2);
                        while($item = mysqli_fetch_assoc($result2)) {
                            $pdf->Cell(60,10,$item['nombre_articulo'],1);
                            $pdf->Cell(30,10,$item['cantidad'],1);
                            $pdf->Cell(50,10,'$'.number_format($item['precio'], 2),1);
                            $pdf->Cell(50,10,'$'.number_format($item['total'], 2),1);
                            $pdf->Ln();
                        }
                    }
                    mysqli_stmt_close($stmt2);
                }

                // Total
                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(140,10,'Total:',1);
                $pdf->Cell(50,10,'$'.number_format($invoice['total'], 2),1);

                // Comentario
                if(!empty($invoice['comentario'])) {
                    $pdf->Ln(20);
                    $pdf->SetFont('Arial','',12);
                    $pdf->MultiCell(0,10,'Comentario: '.$invoice['comentario']);
                }

                $pdf->Output('D', 'Factura_'.$invoice['numero'].'.pdf');
            } else {
                echo "No se encontró la factura.";
            }
        } else {
            echo "Error al ejecutar la consulta.";
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error al preparar la consulta.";
    }
} else {
    echo "ID de factura no proporcionado.";
}

mysqli_close($conn);
