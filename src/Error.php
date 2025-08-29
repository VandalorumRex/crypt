<?php
declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPInterface.php to edit this template
 */

namespace VandalorumRex\Crypt;

/**
 * @author Mansur
 */
interface Error
{
    public const string KEY_TYPE_INVALID = 'Неверный тип ключа';
    public const string FAILED_TO_CREATE_KEY = 'Не удалось создать ключ';
}
