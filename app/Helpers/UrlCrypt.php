<?php

namespace App\Helpers;

class UrlCrypt
{
    private static $salt = 'ConsulvoltERP2026';

    /**
     * Criptografa / Obfusca um ID numérico para string de URL limpa e leve
     */
    public static function encode($id): string
    {
        if (!is_numeric($id)) {
            return (string)$id;
        }

        $str = $id . '|' . substr(md5(self::$salt . $id), 0, 6);
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    /**
     * Descodifica uma string obfuscada de volta para o ID original.
     * Retorna o ID original se já for numérico ou se a verificação for bem-sucedida.
     */
    public static function decode($hash)
    {
        if (is_numeric($hash)) {
            return (int)$hash;
        }

        try {
            $decoded = base64_decode(strtr($hash, '-_', '+/'));
            if (!$decoded || !str_contains($decoded, '|')) {
                return $hash;
            }

            list($id, $check) = explode('|', $decoded, 2);
            $expectedCheck = substr(md5(self::$salt . $id), 0, 6);

            if ($check === $expectedCheck) {
                return (int)$id;
            }
        } catch (\Exception $e) {}

        return $hash;
    }
}
