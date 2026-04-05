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
use betterauth\commands\arguments\PasswordArgument;
use betterauth\event\PlayerChangePasswordEvent;
use betterauth\Loader;
use betterauth\provider\exception\AccountNotFoundException;
use betterauth\provider\exception\WrongPasswordException;
use betterauth\session\SessionController;
use betterauth\utils\SystemUtils;
use Exception;
use pocketmine\command\CommandSender;
use SmartCommand\command\CommandArguments;
use SmartCommand\command\rule\defaults\CooldownRule;
use SmartCommand\command\rule\defaults\OnlyInGameCommandRule;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\CommandMessages;
use SmartCommand\utils\MemberPermissionTrait;

class ChangePasswordCommand extends SmartCommand
{
    public function __construct(CommandMessages $commandMessages)
    {
        return parent::__construct(
            'changepassword',
            'change your password',
            self::DEFAULT_USAGE_PREFIX,
            ['changepass'],
            $commandMessages
        );
    }

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->setPrefix(Loader::getInstance()->getSettings()->getPrefix());
        $this->registerArgument(0, new PasswordArgument('password', true));
        $this->registerArgument(1, new PasswordArgument('password-confirm', true));
        $this->registerRules(new OnlyInGameCommandRule(), new NotLoggedInCommandRule(), new CooldownRule(1000, true));
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
        SystemUtils::callEvent(new PlayerChangePasswordEvent($sender, SessionController::getInstance()->getPlayerSession($sender)->getAccount()));
        $password = $args->getValue('password');
        $passwordConfirm = $args->getValue('password-confirm');
        $name = $sender->getName();
        Loader::getInstance()->getProvider()->changePassword(
            $name,
            $password,
            $passwordConfirm
        )->then(
                function ($result) use ($password, $sender) {
                    if (!SystemUtils::isValidPlayer($sender)) {
                        return;
                    }
                    try {
                        if ($result instanceof Exception) {
                            throw $result;
                        }
                        $settings = Loader::getInstance()->getSettings();
                        $passwordToShow = $password;
                        if ($settings->isHidePasswordEnabled()) {
                            $percent = $settings->getShowPasswordPercent();
                            $passwordToShow = SystemUtils::hideChars($password, $percent);
                        }
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('change-password-successfully', '{password}', $passwordToShow));
                    } catch (WrongPasswordException $error) {
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('wrong-password-confirm'));
                    } catch (AccountNotFoundException $error) {
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('account-not-found'));
                    }
                }
            )->catch(
                function () use ($sender) {
                    if (SystemUtils::isValidPlayer($sender)) {
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('generic-reason'));
                    }
                }
            );
    }
}