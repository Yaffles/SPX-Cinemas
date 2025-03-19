<?php
require_once('config.php');

class Cipher {

    // private static $FIRSTKEY =  '7Qgn2znKY2XRJUXKr1u39yrSxNHwAdtIGSucmbmbNyc=';
    // private static $SECONDKEY = 'sp/i4HoA4wEYMINFwmQNB7AIr7aKg0Tpa2AslaT7utjKzoa+cL+9v0AKC+VCDIcvO41SUn8dyBczlfM6BU9qDA==';

    /**
     * SYMMETRIC ENCRYPTION EXAMPLE
     * ============================
     * STATIC secured_encrypt().
     * Two stage encryption of data - encrypt and then hash
     *
     * Relies of config.php file to declare FIRSTKEY and SECONDKEY constants
     * see: https://www.php.net/manual/en/function.openssl-encrypt.php
     */
    public static function secured_encrypt($data=null) {
        {
        // $first_key = base64_decode(self::$FIRSTKEY);
        $first_key = base64_decode(FIRSTKEY);
        // $second_key = base64_decode(self::$SECONDKEY);
        $second_key = base64_decode(SECONDKEY);

        $method = "aes-256-cbc";

        //Generatea random initialisation vector
        $iv_length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($iv_length);

        // Encryption using AES 256 CBC
        $first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);
        // Hash-based Message Authentication Code
        $second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

        $output = base64_encode($iv.$second_encrypted.$first_encrypted);
        return $output;
        }
    }

    /**
     * STATIC secured_decrypt().
     * Two stage decryption of data
     *
     * Relies of config.php file to declare FIRSTKEY and SECONDKEY constants
     * see: https://www.php.net/manual/en/function.openssl-encrypt.php
     */
    public static function secured_decrypt($input=null)
    {
        if ($input) {
            $first_key = base64_decode(FIRSTKEY);
            $second_key = base64_decode(SECONDKEY);

            $mix = base64_decode($input);

            $method = "aes-256-cbc";
            $iv_length = openssl_cipher_iv_length($method);

            $iv = substr($mix,0,$iv_length);
            $second_encrypted = substr($mix,$iv_length,64);
            $first_encrypted = substr($mix,$iv_length+64);

            $data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
            $second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

            if (hash_equals($second_encrypted,$second_encrypted_new))
                return $data;
        }
        return false;
    }
}

?>