<?php

namespace App\Services;

use Carbon\Carbon;

/**
 * AgtSignatureService
 *
 * Implementa a Assinatura Digital de Documentos Comerciais conforme as Regras Técnicas da AGT (Angola).
 * Algoritmo: RSA 1024-bits com SHA-1.
 * Formato de Concatenação: InvoiceDate;SystemEntryDate;InvoiceNo;GrossTotal;HashDocAnterior;
 * Retorna Hash de 172 caracteres em Base64 e Código de Controlo de 4 caracteres.
 */
class AgtSignatureService
{
    /**
     * Assina um documento comercial ou de conferência.
     *
     * @param string $invoiceDate Data do documento (YYYY-MM-DD)
     * @param string $systemEntryDate Data e Hora de Gravação (YYYY-MM-DDTHH:MM:SS)
     * @param string $invoiceNo Número do documento (Ex: FT FAC/1)
     * @param float $grossTotal Total do documento em Kwanzas (AOA)
     * @param string|null $previousHash Hash do documento anterior na mesma série/exercício (vazio se 1º)
     * @param string|null $privateKeyPem Chave Privada RSA PEM (opcional; usa chave padrão se nula)
     * @return array ['hash' => string, 'hash_control' => string, 'control_code' => string]
     */
    public function signDocument(
        string $invoiceDate,
        string $systemEntryDate,
        string $invoiceNo,
        float $grossTotal,
        ?string $previousHash = null,
        ?string $privateKeyPem = null
    ): array {
        // Formatar datas rigorosamente
        $formattedDate = Carbon::parse($invoiceDate)->format('Y-m-d');
        $formattedEntry = Carbon::parse($systemEntryDate)->format('Y-m-d\TH:i:s');
        $formattedTotal = number_format($grossTotal, 2, '.', '');
        $prevHashClean = trim($previousHash ?? '');

        // Concatenação conforme Decreto AGT:
        // InvoiceDate;SystemEntryDate;InvoiceNo;GrossTotal;HashDocAnterior;
        $signingString = sprintf(
            "%s;%s;%s;%s;%s;",
            $formattedDate,
            $formattedEntry,
            $invoiceNo,
            $formattedTotal,
            $prevHashClean
        );

        // Obter chave privada
        $privateKey = $this->getPrivateKey($privateKeyPem);

        if (!$privateKey) {
            // Em ambiente de teste/fallback sem OpenSSL RSA configurado:
            $syntheticHash = base64_encode(sha1($signingString . 'CONSULVOLT_SECRET_KEY', true));
            // Garantir exatamente 172 caracteres em Base64 para simulador
            $syntheticHash = str_pad($syntheticHash, 172, '0', STR_PAD_RIGHT);
            $controlCode = $syntheticHash[0] . $syntheticHash[10] . $syntheticHash[20] . $syntheticHash[30];

            return [
                'hash' => $syntheticHash,
                'hash_control' => '1',
                'control_code' => $controlCode,
                'signing_string' => $signingString,
                'is_simulated' => true
            ];
        }

        // Assinar via OpenSSL RSA com SHA-1
        $binarySignature = '';
        openssl_sign($signingString, $binarySignature, $privateKey, OPENSSL_ALGO_SHA1);
        $base64Hash = base64_encode($binarySignature);

        // Extração dos 4 caracteres para impressão na fatura (1ª, 11ª, 21ª e 31ª posição)
        $controlCode = $base64Hash[0] . $base64Hash[10] . $base64Hash[20] . $base64Hash[30];

        return [
            'hash' => $base64Hash,
            'hash_control' => '1',
            'control_code' => $controlCode,
            'signing_string' => $signingString,
            'is_simulated' => false
        ];
    }

    /**
     * Formata a menção legal obrigatória para impressão fiscal conforme norma AGT.
     *
     * @param string $controlCode Ex: "PbRc"
     * @param string $certNumber Ex: "000/AGT/2026"
     * @return string Ex: "PbRc-Processado por programa validado n.º 000/AGT/2026"
     */
    public function formatPrintMention(string $controlCode, string $certNumber = '142/AGT/2019'): string
    {
        return sprintf("%s-Processado por programa validado n.º %s", $controlCode, $certNumber);
    }

    /**
     * Carrega a chave privada RSA.
     */
    protected function getPrivateKey(?string $customPem = null)
    {
        if ($customPem && str_contains($customPem, 'BEGIN PRIVATE KEY')) {
            return openssl_pkey_get_private($customPem);
        }

        // Tenta carregar do ficheiro de chave configurado em storage
        $keyPath = storage_path('app/keys/agt_private_key.pem');
        if (file_exists($keyPath)) {
            $pemContent = file_get_contents($keyPath);
            return openssl_pkey_get_private($pemContent);
        }

        return false;
    }
}
