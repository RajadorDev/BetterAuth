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

namespace Betterauth\Commands;

use betterauth\command\rule\NotLoggedInCommandRule;
use Betterauth\Commands\Arguments\PasswordArgument;
use betterauth\Loader;
use betterauth\session\SessionController;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class LoginCommand extends SmartCommand
{
    public function __construct()
    {
        return parent::__construct(
            'login',
            'Log-in the server',
            '/login <password>',
            ['logar'],
            $messages = null
        );
    }

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->registerArgument(0, new PasswordArgument('password', true));
        $this->registerRules(new OnlyInGameCommandRule(), new NotLoggedInCommandRule());
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        if (SessionController::getInstance()->isLoggedIn($sender)) {
            $sender->sendMessage(Loader::getInstance()->getMessages()->get('player-auto-loged-in'));
            return;
        }
    }
}