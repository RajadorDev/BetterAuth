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
use betterauth\event\PlayerRegisterEvent;
use betterauth\Loader;
use betterauth\provider\exception\AccountAlreadyRegisteredException;
use betterauth\session\exception\SessionAlreadyLoggedInException;
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

class RegisterCommand extends SmartCommand
{
    public function __construct(CommandMessages $commandMessages)
    {
        return parent::__construct(
            'register',
            'register in ther server',
            self::DEFAULT_USAGE_PREFIX,
            ['registrar', 'registra', 'reg', 'r', 'registe'],
            $commandMessages
        );
    }

    use MemberPermissionTrait;

    protected function prepare()
    {
        $this->setPrefix(Loader::getInstance()->getSettings()->getPrefix());
        $this->registerArgument(0, new PasswordArgument('password', true));
        $this->registerArgument(1, new PasswordArgument('password-confirm', Loader::getInstance()->getSettings()->needToConfirmPassword()));
        $this->registerRules(new OnlyInGameCommandRule(), new NotLoggedInCommandRule(), new CooldownRule(CooldownRule::secondsToMs(1)));
    }

    protected function onRun(CommandSender $sender, string $label, CommandArguments $args)
    {
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
                        SystemUtils::callEvent(new PlayerRegisterEvent($sender, $result));
                        SessionController::getInstance()->acceptLogin($sender, $result, false);
                        $settings = Loader::getInstance()->getSettings();
                        $passwordToShow = $password;
                        if ($settings->isHidePasswordEnabled()) {
                            $percent = $settings->getShowPasswordPercent();
                            $percentLength = SystemUtils::getCharsPercentLength($password, $percent);
                            $passwordToShow = SystemUtils::hideChars($password, $percentLength);
                        }
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('registered-successfully', '{password}', $passwordToShow));
                    } catch (SessionAlreadyLoggedInException $error) {
                        $sender->close('', Loader::getInstance()->getMessages()->get('account-already-registered', null, null, '', false));
                    } catch (AccountAlreadyRegisteredException $error) {
                        $message = Loader::getInstance()->getMessages()->get('account-already-registered');
                        $sender->sendMessage($message);
                    }
                }
            )->catch(
                function () use ($sender) {
                    if (SystemUtils::isValidPlayer($sender)) {
                        $sender->sendMessage(Loader::getInstance()->getMessages()->get('generic-error'));
                    }
                }
            );
    }
}