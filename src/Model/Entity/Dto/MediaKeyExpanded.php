<?php
declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */
namespace App\Model\Entity\Dto;

/**
 * @author Mansur
 */
class MediaKeyExpanded
{
    public string $iv;
    public string $cipherKey;
    public string $macKey;
    public string $refKey;

    /**
     * Конструктор
     *
     * @param string $encryptionKey
     */
    public function __construct(string $encryptionKey)
    {
        $this->iv = substr($encryptionKey, 0, 16);
        $this->cipherKey = substr($encryptionKey, 16, 32);
        $this->macKey = substr($encryptionKey, 48, 32);
        $this->refKey = substr($encryptionKey, 80);
    }

    /**
     * encryptFileAESCBC
     *
     * @param string $inputFile
     * @param string $outputFile
     * @return string|false
     */
    public function encryptFileAESCBC(string $inputFile, string $outputFile): string|false {
        // Generate a secure, random IV
        $cipher = 'AES-256-CBC';
        //$ivLength = openssl_cipher_iv_length($cipher);
        //$iv = openssl_random_pseudo_bytes($ivLength);

        // Read the content of the input file
        $plaintext = file_get_contents($inputFile);
        if ($plaintext === false) {
            return false; // Error reading file
        }

        // Encrypt the plaintext
        $encryptedData = openssl_encrypt($plaintext, $cipher, $this->cipherKey, OPENSSL_RAW_DATA, $this->iv);
        if ($encryptedData === false) {
            return false; // Error during encryption
        }

        // Prepend the IV to the encrypted data and base64 encode for storage
        $finalEncryptedContent = base64_encode($this->iv . $encryptedData);

        // Write the encrypted content to the output file
        if (file_put_contents($outputFile, $finalEncryptedContent) === false) {
            return false; // Error writing file
        }

        return $finalEncryptedContent; // Encryption successful
    }

    public function mac(string $data_to_sign = ''): string
    {
        /*if (!$data_to_sign) {
            $data_to_sign = $iv . $enc;
        }*/
        // 2. Generate HMAC-SHA256
        // The 'true' argument ensures raw binary output, which is generally preferred for cryptographic operations.
        $hmac_full = hash_hmac('sha256', $data_to_sign, $this->macKey, true);

        // 3. Extract the first 10 bytes as mac
        $mac = substr($hmac_full, 0, 10);

        return $mac;
    }
}
