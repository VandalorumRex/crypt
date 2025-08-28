<?php
declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */
namespace VandalorumRex\Crypt\Model\Entity\Dto;

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
}
