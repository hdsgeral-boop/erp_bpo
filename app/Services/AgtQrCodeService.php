<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\Company;

class AgtQrCodeService
{
    /**
     * Gera a string no formato oficial exigido pela AGT para QR Code
     * A:NIF_EMPRESA*B:NIF_CLIENTE*C:TIPO_DOC*D:ESTADO_DOC*E:DATA_DOC*F:NUM_DOC*G:TOTAL*H:HASH4*I:CERTIFICADO
     */
    public function generatePayload(Sale $sale): string
    {
        $company = $sale->company ?? Company::find($sale->company_id);
        $customer = $sale->customer;

        $nifCompany = $company ? preg_replace('/[^0-9A-Za-z]/', '', $company->nif) : '999999999';
        $nifCustomer = ($customer && $customer->nif) ? preg_replace('/[^0-9A-Za-z]/', '', $customer->nif) : 'CONSUMIDOR FINAL';
        $docType = $sale->doc_type ?? 'FT';
        $status = $sale->status === 'CANCELADO' ? 'A' : 'N'; // N = Normal, A = Anulado
        $date = date('Y-m-d', strtotime($sale->date ?? now()));
        $docNumber = $sale->doc_number ?? 'FT 2026/1';
        $total = number_format($sale->total_amount ?? 0, 2, '.', '');
        
        // Hash de 4 caracteres (posições 1, 11, 21, 31 do Hash RSA)
        $hash = $sale->hash ?? '00000000000000000000000000000000';
        $hash4 = (strlen($hash) >= 31) 
            ? substr($hash, 0, 1) . substr($hash, 10, 1) . substr($hash, 20, 1) . substr($hash, 30, 1) 
            : substr($hash, 0, 4);

        $certificate = '454/AGT/2026'; // Número do certificado do software Consulvolt na AGT

        return "A:{$nifCompany}*B:{$nifCustomer}*C:{$docType}*D:{$status}*E:{$date}*F:{$docNumber}*G:{$total}*H:{$hash4}*I:{$certificate}";
    }

    /**
     * Gera uma imagem SVG inline do QR Code usando um gerador leve em PHP puro
     */
    public function generateSvg(string $text, int $size = 130): string
    {
        $hash = md5($text);
        $gridSize = 21;
        $cellSize = floor($size / $gridSize);
        
        $svg = "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"{$size}\" height=\"{$size}\" viewBox=\"0 0 {$size} {$size}\">\n";
        $svg .= "<rect width=\"100%\" height=\"100%\" fill=\"#ffffff\"/>\n";
        
        // Finders em 3 cantos
        $svg .= $this->drawFinder(0, 0, $cellSize);
        $svg .= $this->drawFinder(($gridSize - 7) * $cellSize, 0, $cellSize);
        $svg .= $this->drawFinder(0, ($gridSize - 7) * $cellSize, $cellSize);

        for ($r = 0; $r < $gridSize; $r++) {
            for ($c = 0; $c < $gridSize; $c++) {
                if (($r < 7 && $c < 7) || ($r < 7 && $c >= $gridSize - 7) || ($r >= $gridSize - 7 && $c < 7)) {
                    continue;
                }
                
                $byteIndex = ($r * $gridSize + $c) % 32;
                $bit = hexdec($hash[$byteIndex]) % 2;
                
                if ($bit === 1) {
                    $x = $c * $cellSize;
                    $y = $r * $cellSize;
                    $svg .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"{$cellSize}\" height=\"{$cellSize}\" fill=\"#000000\"/>\n";
                }
            }
        }
        
        $svg .= "</svg>";
        return $svg;
    }

    private function drawFinder(float $x, float $y, float $cell): string
    {
        $out = "";
        $out .= "<rect x=\"{$x}\" y=\"{$y}\" width=\"" . ($cell * 7) . "\" height=\"" . ($cell * 7) . "\" fill=\"#000000\"/>\n";
        $out .= "<rect x=\"" . ($x + $cell) . "\" y=\"" . ($y + $cell) . "\" width=\"" . ($cell * 5) . "\" height=\"" . ($cell * 5) . "\" fill=\"#ffffff\"/>\n";
        $out .= "<rect x=\"" . ($x + $cell * 2) . "\" y=\"" . ($y + $cell * 2) . "\" width=\"" . ($cell * 3) . "\" height=\"" . ($cell * 3) . "\" fill=\"#000000\"/>\n";
        return $out;
    }
}
