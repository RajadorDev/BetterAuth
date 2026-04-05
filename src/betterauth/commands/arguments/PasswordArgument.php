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

namespace betterauth\commands\arguments;

use betterauth\Loader;
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
                if (strlen($given) > Loader::getInstance()->getSettings()->getMaxPasswordLength()) {
                    return false;
                }
                if (strlen($given) < Loader::getInstance()->getSettings()->getMinPasswordLength()) {
                    return false;
                }
            }
        );
    }

    public function getWrongMessage(CommandMessages $commandMessages, string $argumentUsed): string
    {
        return parent::getWrongMessage($commandMessages, $argumentUsed);
    }
}