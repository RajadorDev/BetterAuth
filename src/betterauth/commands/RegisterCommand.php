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
            '/register <password>',
            ['registrar'],
            $messages = null
        );
    }

    protected function prepare()
    {
        $this->registerArgument(0, new PasswordArgument('password', true));
        $this->registerArgument(0, new PasswordArgument('password-confirm', false));
        $this->registerRules(new OnlyInGameCommandRule(), new NotLoggedInCommandRule());
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $passwrod = $args->getValue('password');
        if (Loader::getInstance()->getSettings()->needToConfirmPassword()) {
            if ($args->has('password-confirm')) {
                $passwrodConfirm = $args->getValue('password-confirm');
                if ($passwrod === $passwrodConfirm) {
                    //TODO: adicionar funcao para registrar
                    $sender->sendMessage(Loader::getInstance()->getMessages()->get('registered-successfully'));
                    return;
                }
                $sender->sendMessage(Loader::getInstance()->getMessages()->get('passwords-dont-match'));
                return;
            }
            $sender->sendMessage(Loader::getInstance()->getMessages()->get('password-confirm-required'));
            return;
        }
    }
}