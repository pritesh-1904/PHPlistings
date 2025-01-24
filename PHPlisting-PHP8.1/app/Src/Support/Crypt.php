<?php

/**
 * @package    phpListings
 * @author     phpListings Team <info@phplistings.com>
 * @copyright  2020 phpListings.com
 * @license    https://www.phplistings.com/eula
 */

namespace App\Src\Support;

class Crypt
{

    protected static $algorithm = 'aes-128-cbc';

    public static function encode($data, $encryptionKey, $authenticationKey)
    {
        $initVectorLength = openssl_cipher_iv_length(self::$algorithm);
        $initVector = openssl_random_pseudo_bytes($initVectorLength);
        $encryptedData = openssl_encrypt(
            json_encode($data),
            self::$algorithm,
            $encryptionKey,
            OPENSSL_RAW_DATA,
            $initVector
        );
        $hmac = hash_hmac('sha256', $initVector . $encryptedData, $authenticationKey, true);

        return base64_encode($hmac . $initVector . $encryptedData);
    }

    public static function decode($data, $encryptionKey, $authenticationKey)
    {
        $data = base64_decode($data);
        $initVectorLength = openssl_cipher_iv_length(self::$algorithm);

        if (false === $data) {
            return false;
        }

        $hmac = substr($data, 0, $sha2len = 32);
        $initVector =  substr($data, $sha2len, $initVectorLength);
        $encryptedData = substr($data, $sha2len + $initVectorLength);
        $calculatedHash = hash_hmac('sha256', $initVector . $encryptedData, $authenticationKey, true);

        if (hash_equals($hmac, $calculatedHash)) {
            $decryptedData = openssl_decrypt(
                $encryptedData,
                self::$algorithm,
                $encryptionKey,
                OPENSSL_RAW_DATA,
                $initVector
            );

            return json_decode($decryptedData, true);
        }

        return false;
    }

}
