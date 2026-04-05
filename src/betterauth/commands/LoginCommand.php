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
use betterauth\provider\exception\AccountNotFoundException;
use betterauth\provider\exception\WrongPasswordException;
use betterauth\session\exception\SessionAlreadyLoggedInException;
use betterauth\session\SessionController;
use betterauth\utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class LoginCommand extends SmartCommand
{
    public function __construct(CommandMessages $commandMessages)
    {
        return parent::__construct(
            'login',
            'Log-in the server',
            self::DEFAULT_USAGE_PREFIX,
            ['logar'],
            $commandMessages
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
        $password = $args->getValue('password');
        Loader::getInstance()->getProvider()->tryLogin($sender, $password)->then(
            function ($result) use ($sender, $password) {
                if (!SystemUtils::isValidPlayer($sender)) {
                    return;
                }

                try {
                    if ($result instanceof Exception) {
                        throw $result;
                    }
                    try {
                        SessionController::getInstance()->acceptLogin($sender, $result, false);
                    } catch (SessionAlreadyLoggedInException $error) {
                        $sender->close('', 'Já tem alguém logado com seu nome');
                    }
                } catch (AccountNotFoundException $error) {
                    $sender->sendMessage(Loader::getInstance()->getMessages()->get('account-fot-found'));
                } catch (WrongPasswordException $error) {
                    $sender->sendMessage((Loader::getInstance()->getMessages()->get('wrong-password')));
                }

            }
        )->catch(function () use ($sender) {
            if (SystemUtils::isValidPlayer($sender)) {
                return;
            }

            $sender->sendMessage(Loader::getInstance()->getMessages()->get('generic-reason'));
        });
    }
}