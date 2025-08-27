<?php
declare(strict_types=1);

/**
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace App\Service;

/**
 * Description of EncodeService
 *
 * @author Mansur
 */
class EncodeService
{
    /**
     * Формирует HKDF-ключ из исходного ключа
     *
     * @param string $keyName
     * @return string
     */
    public function hkdf(string $keyName): string
    {
        //$inputKey = file_get_contents(ROOT . '/keys/' . $keyName);
        $inputKey = random_bytes(32);
        $salt = random_bytes(16);
        $encryptionKey = hash_hkdf('sha256', $inputKey, 112, 'aes-256-encryption', $salt);
        file_put_contents(ROOT . '/keys/' . $keyName . '.key', $inputKey);
        file_put_contents(ROOT . '/keys/' . $keyName . '.hkdf', $encryptionKey);

        return $encryptionKey;
    }
}
