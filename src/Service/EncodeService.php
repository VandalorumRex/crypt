<?php
declare(strict_types=1);

/**
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */
namespace App\Service;

use App\Model\Entity\Dto\MediaKeyExpanded;
use App\Model\Entity\Enum\MediaType;
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
     * @param 'audio'|'document'|'image'|'video' $type
     * @return string
     */
    public function hkdf(string $keyName, string $type): string
    {
        $inputKey = random_bytes(32);
        $salt = random_bytes(16);
        $mediaType = MediaType::from($type);
        $encryptionKey = hash_hkdf('sha256', $inputKey, 112, $mediaType->applicationInfo(), $salt);
        file_put_contents(ROOT . '/keys/' . $keyName . '.key', $inputKey);
        file_put_contents(ROOT . '/keys/' . $keyName . '.hkdf', $encryptionKey);

        return $encryptionKey;
    }

    /**
     * Расщепление
     *
     * @param string $keyName
     * @param 'audio'|'document'|'image'|'video' $type
     * @return \App\Model\Entity\Dto\MediaKeyExpanded
     */
    public function split(string $keyName, string $type): MediaKeyExpanded
    {
        $encryptionKey = file_get_contents(ROOT . '/keys/' . $keyName . '.hkdf');
        if (!$encryptionKey) {
            $encryptionKey = $this->hkdf($keyName, $type);
        }
        $result = new MediaKeyExpanded($encryptionKey);

        return $result;
    }

    public function encrypt(string $inputFile, string $keyName, string $type) {
        $encryptionKey = file_get_contents(ROOT . '/keys/' . $keyName . '.hkdf');
        if (!$encryptionKey) {
            $encryptionKey = $this->hkdf($keyName, $type);
        }
        $split = new MediaKeyExpanded($encryptionKey);
        return $split->encryptFileAESCBC($inputFile, $inputFile . '.enc');
    }

    /**public function mac(string $inputFile, string $keyName, string $type) {
        $encryptionKey = file_get_contents(ROOT . '/keys/' . $keyName . '.hkdf');
        if (!$encryptionKey) {
            $encryptionKey = $this->hkdf($keyName, $type);
        }
        $split = new MediaKeyExpanded($encryptionKey);
        return $split->encryptFileAESCBC($inputFile, $inputFile . '.enc');
    }*/
}
