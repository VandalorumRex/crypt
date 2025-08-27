<?php
declare(strict_types=1);

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Command;

use App\Service\EncodeService;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\CommandFactoryInterface;
use Cake\Console\ConsoleIo;

/**
 * CakePHP EncodeCommand
 *
 * @author Mansur
 */
class EncodeCommand extends Command
{
    /**
     * Конструктор
     *
     * @param \App\Service\EncodeService $encode
     * @param \Cake\Console\CommandFactoryInterface|null $factory
     */
    public function __construct(protected EncodeService $encode, ?CommandFactoryInterface $factory = null)
    {
        parent::__construct($factory);
    }

    /**
     * Выполнение
     *
     * @param \Cake\Console\Arguments $args
     * @param \Cake\Console\ConsoleIo $io
     * @return int
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        if ($args->getArgumentAt(4) !== null) {
            $result = $this->encode->{$args->getArgumentAt(0)}($args->getArgumentAt(1), $args->getArgumentAt(2), $args->getArgumentAt(3), $args->getArgumentAt(4));
        } elseif ($args->getArgumentAt(3) !== null) {
            $result = $this->encode->{$args->getArgumentAt(0)}($args->getArgumentAt(1), $args->getArgumentAt(2), $args->getArgumentAt(3));
        } elseif ($args->getArgumentAt(2) !== null) {
            $result = $this->encode->{$args->getArgumentAt(0)}($args->getArgumentAt(1), $args->getArgumentAt(2));
        } elseif ($args->getArgumentAt(1) !== null) {
            $result = $this->encode->{$args->getArgumentAt(0)}($args->getArgumentAt(1));
        } else {
            $result = $this->encode->{$args->getArgumentAt(0)}();
        }
        print_r($result);

        return 0;
    }
}
