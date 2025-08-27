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
    public function mediaKeyExpanded(string $keyName, string $type): string
    {
        $inputKey = random_bytes(32);
        $salt = random_bytes(16);
        $encryptionKey = hash_hkdf('sha256', $inputKey, 112, $type, $salt);
        file_put_contents(ROOT . '/keys/' . $keyName . '.key', $inputKey);
        file_put_contents(ROOT . '/keys/' . $keyName . '.hkdf', $encryptionKey);

        return $encryptionKey;
    }

    /**
     * Расщепление
     *
     * @param string $keyName
     * @return array<string, string>
     */
    public function split(string $keyName, string $type): array
    {
        $encryptionKey = file_get_contents(ROOT . '/keys/' . $keyName . '.hkdf');
        if (!$encryptionKey) {
            $encryptionKey = $this->mediaKeyExpanded($keyName, $type);
        }
        $result = [
            'iv' => substr($encryptionKey, 0, 16),
            'cipherKey' => substr($encryptionKey, 16, 32),
            'macKey' => substr($encryptionKey, 48, 32),
            'refKey' => substr($encryptionKey, 80),
        ];

        return $result;
    }
}
