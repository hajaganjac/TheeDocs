<?php
function escapePdfText(string $text): string
{
    $text = str_replace('\\', '\\\\', $text);
    $text = str_replace(['(', ')'], ['\\(', '\\)'], $text);
    return $text;
}

function generateShippingLabelPdf(array $order, array $items, string $storeName): string
{
    $y = 770;
    $lineHeight = 20;
    $content = [];
    $content[] = 'BT';
    $content[] = '/F1 20 Tf';
    $content[] = '40 ' . $y . ' Td (' . escapePdfText($storeName . ' Shipping Label') . ') Tj';
    $content[] = '0 -' . $lineHeight . ' Td /F1 12 Tf (Order #' . escapePdfText((string)$order['id']) . ') Tj';
    $content[] = '0 -' . $lineHeight . ' Td (Customer: ' . escapePdfText($order['customer_name']) . ') Tj';
    $content[] = '0 -' . $lineHeight . ' Td (Email: ' . escapePdfText($order['email']) . ') Tj';
    $content[] = '0 -' . $lineHeight . ' Td (Ship To:) Tj';

    $addressLines = preg_split('/\r?\n/', $order['address']);
    foreach ($addressLines as $line) {
        $content[] = '0 -' . $lineHeight . ' Td (' . escapePdfText($line) . ') Tj';
    }

    $content[] = '0 -' . $lineHeight . ' Td (Items:) Tj';
    foreach ($items as $item) {
        $label = sprintf('%s x%d - $%0.2f', $item['product']['name'], $item['quantity'], $item['lineTotal']);
        $content[] = '0 -' . $lineHeight . ' Td (' . escapePdfText($label) . ') Tj';
    }

    $content[] = '0 -' . $lineHeight . ' Td (Total: $' . number_format($order['total'], 2) . ') Tj';
    $content[] = '0 -' . ($lineHeight * 2) . ' Td (Thank you for shopping with ' . escapePdfText($storeName) . '!) Tj';
    $content[] = 'ET';

    $contentStream = implode("\n", $content);
    $objects = [];
    $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
    $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
    $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>';
    $objects[] = '<< /Length ' . strlen($contentStream) . " >>\nstream\n" . $contentStream . "\nendstream";
    $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';

    $pdf = "%PDF-1.4\n";
    $offsets = [0];
    foreach ($objects as $index => $object) {
        $offsets[$index + 1] = strlen($pdf);
        $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
    }
    $xrefPos = strlen($pdf);
    $count = count($objects) + 1;
    $pdf .= "xref\n0 $count\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i < $count; $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
    }
    $pdf .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";

    return $pdf;
}
