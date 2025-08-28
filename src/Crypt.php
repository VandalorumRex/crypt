<?php
declare(strict_types=1);

/**
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace VandalorumRex\Crypt;

use Exception;
use VandalorumRex\Crypt\Model\Entity\Dto\MediaKeyExpanded;
use VandalorumRex\Crypt\Model\Entity\Enum\MediaType;


/**
 * Description of Crypt
 *
 * @author Mansur
 */
class Crypt
{
    /**
     * Формирует HKDF-ключ из исходного ключа
     *
     * @param string $keyName
     * @param 'audio'|'document'|'image'|'video' $type
     * @return string
     * @throws \Exception
     */
    public function hkdf(string $keyName, string $type): string
    {
        $keyPath = ROOT . '/keys/' . $keyName . '.key';
        if (file_exists($keyPath)) {
            $inputKey = file_get_contents($keyPath);
        } else {
            $inputKey = random_bytes(32);
            file_put_contents(ROOT . '/keys/' . $keyName . '.key', $inputKey);
        }
        if (!$inputKey) {
            throw new Exception('Не удалось создать ключ');
        }
        $salt = '';//random_bytes(16);
        $mediaType = MediaType::from($type);
        $encryptionKey = hash_hkdf('sha256', $inputKey, 112, $mediaType->applicationInfo(), $salt);
        //file_put_contents(ROOT . '/keys/' . $keyName . '.hkdf', $encryptionKey);

        return $encryptionKey;
    }

    /**
     * Шифруем файл
     *
     * @param string $inputFile
     * @param string $keyName
     * @param 'audio'|'document'|'image'|'video' $type
     * @return string Путь к зашифрованному файлу
     * @throws \Exception
     */
    public function encryptFile(string $inputFile, string $keyName, string $type): string
    {
        if (file_exists($inputFile)) {
            $data = file_get_contents($inputFile);
        } else {
            throw new Exception('Нет файла');
        }
        if ($data === false) {
            throw new Exception('Нет удалось получить данные');
        }
        $encryptionKey = $this->hkdf($keyName, $type);
        if (!$encryptionKey) {
            throw new Exception('Не удалось получить расширенный ключ');
        }
        $mediaKeyExpanded = new MediaKeyExpanded($encryptionKey);

        // Шифруем AES-CBC (с PKCS7 padding встроено в openssl_encrypt)
        $enc = openssl_encrypt($data, 'AES-256-CBC', $mediaKeyExpanded->cipherKey, OPENSSL_RAW_DATA, $mediaKeyExpanded->iv);

        // Подписываем iv + enc
        $macFull = hash_hmac('sha256', $mediaKeyExpanded->iv . $enc, $mediaKeyExpanded->macKey, true);
        $mac = substr($macFull, 0, 10);

        // Склеиваем enc + mac
        $join = $enc . $mac;
        $ouputFile = $inputFile . '.encrypted';
        $result = file_put_contents($ouputFile, $join);
        if (!$result) {
            throw new Exception('Не удалось создать шифрованный файл');
        }

        return $ouputFile;
    }

    /**
     * Дэшифруем файл
     *
     * @param string $inputFile
     * @param string $keyName
     * @param 'audio'|'document'|'image'|'video' $type
     * @return string
     * @throws \Exception
     */
    public function decryptFile(string $inputFile, string $keyName, string $type): string
    {
        $encryptionKey = $this->hkdf($keyName, $type);
        if (!$encryptionKey) {
            throw new Exception('Не удалось получить расширенный ключ');
        }
        $mediaKeyExpanded = new MediaKeyExpanded($encryptionKey);
        $mediaData = file_get_contents($inputFile);
        if ($mediaData === false) {
            throw new Exception('Не удалось получить данные из файла');
        }
        $file = substr($mediaData, 0, -10);
        $mac = substr($mediaData, -10);
        //Validate HMAC-SHA256(macKey, iv + file) and compare first 10 bytes
        $hmacFull = hash_hmac('sha256', $mediaKeyExpanded->iv . $file, $mediaKeyExpanded->macKey, true);
        $hmac = substr($hmacFull, 0, 10);
        if (!hash_equals($hmac, $mac)) {
            throw new Exception('MAC verification failed');
        }
        // 6. Decrypt file with AES-CBC using cipherKey and iv, then unpad
        $plaintext = openssl_decrypt(
            $file,
            'AES-256-CBC',
            $mediaKeyExpanded->cipherKey,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            $mediaKeyExpanded->iv,
        );
        if ($plaintext === false) {
            throw new Exception('OpenSSL decryption failed');
        }
        // Remove PKCS#7 padding
        //$unPadded = pkcs7_unpad($plaintext);
        //$unPadded = substr($plaintext, 32);
        $len = strlen($plaintext);
        $pad = ord($plaintext[$len-1]);
        $unPadded = substr($plaintext, 0, strlen($plaintext) - $pad);
        $ouputFile = $inputFile . '.decrypted';
        $result = file_put_contents($ouputFile, $unPadded);
        if (!$result) {
            throw new Exception('Не удалось создать дэшифрованный файл');
        }

        return $ouputFile;
    }
}
