<?php
declare(strict_types=1);

/**
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace App\Service;

use App\Model\Entity\Dto\MediaKeyExpanded;
use App\Model\Entity\Enum\MediaType;
use Exception;

/**
 * Description of CryptService
 *
 * @author Mansur
 */
class CryptService
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
        $hkdfPath = ROOT . '/keys/' . $keyName . '.hkdf';
        if (file_exists($hkdfPath)) {
            $encryptionKey = file_get_contents($hkdfPath);
        } else {
            $encryptionKey = $this->hkdf($keyName, $type);
        }
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
}
