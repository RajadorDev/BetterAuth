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
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class ChangePasswordCommand extends SmartCommand
{
    public function __construct()
    {
        return parent::__construct(
            'changepassword',
            'change your password',
            '/changepassword <new - password> <confirm - password>',
            ['changepass'],
            $messages = null
        );
    }

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->registerArgument(0, new PasswordArgument('password', true));
        $this->registerArgument(1, new PasswordArgument('password-confirm', true));
        $this->registerRules(new OnlyInGameCommandRule(), new NotLoggedInCommandRule());
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        $password = $args->getValue('password');
        $passwordConfirm = $args->getValue('password-confirm');
        if ($passwordConfirm === $password) {
            //TODO: adicionar funcao de trocar senha
            $sender->sendMessage(Loader::getInstance()->getMessages()->get('password-changed', '{password}', $password));
            return;
        }
        $sender->sendMessage(Loader::getInstance()->getMessages()->get('passwords-dont-match'));
        return;
    }
}