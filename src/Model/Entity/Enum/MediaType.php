<?php
declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPEnum.php to edit this template
 */
namespace App\Model\Entity\Enum;

/**
 * MediaType
 *
 * @author Mansur
 */
enum MediaType: string
{
    case AUDIO = 'audio';
    case DOCUMENT = 'document';
    case IMAGE = 'image';
    case VIDEO = 'video';

    /**
     * Application Info
     *
     * @return string
     */
    public function applicationInfo(): string
    {
        return match ($this) {
            self::AUDIO => 'WhatsApp Audio Keys',
            self::DOCUMENT => 'WhatsApp Document Keys',
            self::IMAGE => 'WhatsApp Image Keys',
            self::VIDEO => 'WhatsApp Video Keys',
        };
    }
}
