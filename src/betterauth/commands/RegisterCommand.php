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
use betterauth\event\PlayerRegisterEvent;
use betterauth\Loader;
use betterauth\provider\exception\AccountAlreadyRegisteredException;
use betterauth\session\SessionController;
use betterauth\utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class RegisterCommand extends SmartCommand
{
    public function __construct(CommandMessages $commandMessages)
    {
        return parent::__construct(
            'register',
            'register in ther server',
            self::DEFAULT_USAGE_PREFIX,
            ['registrar'],
            $commandMessages
        );
    }

    use MemberPermissionTrait;

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
        $password = $args->getValue('password');
        if ($args->has('password-confirm')) {
            $passwordConfirm = $args->getValue('password-confirm');
            if ($password !== $passwordConfirm) {
                $sender->sendMessage(Loader::getInstance()->getMessages()->get('passwords-dont-match'));
                return;
            }
        }
        Loader::getInstance()->getProvider()->tryRegister(
            $sender,
            $password
        )->then(
                function ($result) use ($password, $sender) {
                    if (!SystemUtils::isValidPlayer($sender)) {
                        return;
                    }
                    try {
                        if ($result instanceof Exception) {
                            throw $result;
                        }
                        SystemUtils::callEvent(new PlayerRegisterEvent($sender, SessionController::getInstance()->getPlayerSession($sender)->getAccount()));
                        $settings = Loader::getInstance()->getSettings();
                        $passwordToShow = $password;
                        if ($settings->isHidePasswordEnabled()) {
                            $percent = $settings->getShowPasswordPercent();
                            $passwordToShow = SystemUtils::hideChars($password, $percent);
                        }
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('account-registered', '{password}', $passwordToShow));
                    } catch (AccountAlreadyRegisteredException $error) {
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('account-alredy-registered'));
                    }
                }
            )->then(
                function () use ($sender) {
                    if (SystemUtils::isValidPlayer($sender)) {
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('generic-reason'));
                    }
                }
            );
    }
}