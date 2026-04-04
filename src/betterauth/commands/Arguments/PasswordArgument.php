<?php

declare(strict_types=1);

/***
 * 
 * ██████╗ ███████╗████████╗████████╗███████╗██████╗      █████╗ ██╗   ██╗████████╗██╗  ██╗
 * ██╔══██╗██╔════╝╚══██╔══╝╚══██╔══╝██╔════╝██╔══██╗    ██╔══██╗██║   ██║╚══██╔══╝██║  ██║
 * ██████╔╝█████╗     ██║      ██║   █████╗  ██████╔╝    ███████║██║   ██║   ██║   ███████║
 * ██╔══██╗██╔══╝     ██║      ██║   ██╔══╝  ██╔══██╗    ██╔══██║██║   ██║   ██║   ██╔══██║
 * ██████╔╝███████╗   ██║      ██║   ███████╗██║  ██║    ██║  ██║╚██████╔╝   ██║   ██║  ██║
 * ╚═════╝ ╚══════╝   ╚═╝      ╚═╝   ╚══════╝╚═╝  ╚═╝    ╚═╝  ╚═╝ ╚═════╝    ╚═╝   ╚═╝  ╚═╝
 *   
 * Created by:
 * 
 * Rajador: https://github.com/RajadorDev
 * 
 * Bietio: https://github.com/Bietio
 * 
 * NATANBX0: https://github.com/NATANBX0
 * 
 **/

namespace Betterauth\Commands\Arguments;

use pocketmine\utils\TextFormat;
use SmartCommand\command\argument\BaseArgument;
use SmartCommand\message\CommandMessages;

class PasswordArgument extends BaseArgument
{
    public function __construct(string $name, bool $required)
    {
        return parent::__construct(
            $name,
            'string',
            $required,
            function (&$given) {
                if (strlen($given) < 8) {
                    return false;
                }
            }
        );
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        $commandMessages->set(CommandMessages::INVALID_ARGUMENT, TextFormat::GRAY . 'Sua senha precisa ter no mínimo ' . TextFormat::RED . ' 8 ' . TextFormat::GRAY . 'caracteres!');
        return parent::getWrongMessage($commandMessages, $argumentUsed);
    }
}