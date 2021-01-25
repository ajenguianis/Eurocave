<?php

namespace App\Services;


use Exception;
use RangeException;

/**
 * Class EncryptService
 * @package App\Services
 */
class EncryptService
{
    const secret_key = 'XJ%l#h[ERX}[6hir;w*CR/kdjCqgDja#';
    const secret_iv = 'TORecw:sJkqR5Kdl)w)}pI#!G~Wq[IW7';
    /**
     * Encode Data
     * So we can send it securely
     * to magento
     * @param $string
     * @return bool|string
     */
    public static function encodeData($string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', self::secret_key);
        $iv = substr(hash('sha256', self::secret_iv), 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);

        return $output;
    }

    /**
     * Decoding data
     * @param $string
     * @return bool|string
     */
    public static function decodeData($string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash('sha256', self::secret_key);
        $iv = substr(hash('sha256', self::secret_iv), 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        return $output;
    }
}