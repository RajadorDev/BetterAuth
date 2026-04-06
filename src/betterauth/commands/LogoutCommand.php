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

namespace betterauth\commands;

use betterauth\commands\rule\NotLoggedInCommandRule;
use betterauth\Loader;
use betterauth\session\SessionController;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class LogoutCommand extends SmartCommand
{
    use MemberPermissionTrait;
    public function __construct(CommandMessages $commandMessages)
    {
        return parent::__construct(
            'logout',
            'logout of ther server',
            self::DEFAULT_USAGE_PREFIX,
            [],
            $commandMessages
        );
    }

    protected function prepare()
    {
        $this->registerRules(new OnlyInGameCommandRule(), new CooldownRule(CooldownRule::secondsToMs(1)));
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        if ($session = SessionController::getInstance()->getPlayerSession($sender)) {
            $session->destroy(false);
            $sender->sendMessage(Loader::getInstance()->getMessages()->get('logout'));
            return;
        }

        $sender->sendMessage($this->getMessages()->get('no-logged-in'));
    }
}