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

use betterauth\command\rule\NotLoggedInCommandRule;
use Betterauth\Commands\Arguments\PasswordArgument;
use betterauth\Loader;
use betterauth\provider\Account;
use betterauth\session\SessionController;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\utils\MemberPermissionTrait;

class RegisterCommand extends SmartCommand
{
    use MemberPermissionTrait;
    public function __construct()
    {
        return parent::__construct(
            'register',
            'register in ther server',
            self::DEFAULT_USAGE_PREFIX,
            ['registrar'],
            $messages = null
        );
    }

    protected function prepare()
    {
        $this->registerArgument(0, new PasswordArgument('password', true));
        $this->registerArgument(1, new PasswordArgument('password-confirm', Loader::getInstance()->getSettings()->needToConfirmPassword()));
        $this->registerRules(new OnlyInGameCommandRule(), new NotLoggedInCommandRule());
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        if (!is_null(SessionController::getInstance()->getPlayerSession($sender))) {
            return;
        }
        $passwrod = $args->getValue('password');
        if($args->has('password-confirm')) {
            $passwrodConfirm = $args->getValue('password-confirm');
            if ($passwrod !== $passwrodConfirm) {
                $sender->sendMessage(Loader::getInstance()->getMessages()->get('passwords-dont-match'));
                return;
            }
        }
        Account::create(
            strtolower($sender->getName()),
            $passwrod,
            $sender->getAddress(),
            $sender->getClientId()
        );
        $sender->sendMessage(Loader::getInstance()->getMessages()->get('registered-successfully'));
        return;
    }
}