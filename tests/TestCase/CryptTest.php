<?php
declare(strict_types=1);

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace VandalorumRex\Crypt\Test\TestCase;

use Cake\TestSuite\TestCase;
use VandalorumRex\Crypt\Crypt;

/**
 * Description of CryptTest
 *
 * @author Mansur
 */
class CryptTest extends TestCase
{
    public function testHkdf(): void
    {
        $crypt = new Crypt();
        /** @var string $hkdf */
        $hkdf = $crypt->hkdf('audio', 'audio');
        $this->assertEquals(112, strlen($hkdf));
        /** @var array<string, mixed> $hkdf2 */
        $hkdf2 = $crypt->hkdf('test', 'test');
        $this->assertArrayHasKey('error', $hkdf2);
    }
}
