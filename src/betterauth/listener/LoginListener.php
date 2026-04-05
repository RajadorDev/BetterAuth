<?php

declare (strict_types=1);
 
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

namespace betterauth\listener;

use betterauth\event\PlayerAutomaticallyLoginFailEvent;
use betterauth\event\PlayerChangePasswordEvent;
use betterauth\event\PlayerLoggedOutEvent;
use betterauth\event\PlayerLoginSucessfullyEvent;
use betterauth\event\PlayerRegisterEvent;
use betterauth\Loader;
use betterauth\session\AuthTimeout;
use betterauth\session\task\LoginTimeoutTask;
use betterauth\utils\Settings;
use betterauth\utils\SystemMessages;
use betterauth\utils\SystemUtils;
use pocketmine\event\Listener;

final class LoginListener implements Listener
{

    /** @var Settings */
    protected $settings;

    /** @var SystemMessages */
    protected $messages;
    
    public function __construct(Settings $settings, SystemMessages $messages)
    {
        $this->settings = $settings;
        $this->messages = $messages;
    }

    /**
     * @priority LOWEST
     */
    public function onLoginSucessfully(PlayerLoginSucessfullyEvent $event)
    {
        $player = $event->getPlayer();
        $messageId = $event->wasLoggedInAutomatically() ? 'auto-login-sucessfully' : 'login-sucessfully';
        $this->messages->send($player, $messageId);

        if (!Loader::getInstance()->allowNotLoggedInPlayerMove()) {
            SystemUtils::freezePlayer($player, false);
        }

        if (LoginTimeoutTask::isEnabled()) {
            LoginTimeoutTask::getInstance()->removePlayer($player);
        }
    }

    /**
     * @priority LOWEST
     */
    public function onLogout(PlayerLoggedOutEvent $event)
    {
        if (!Loader::getInstance()->allowNotLoggedInPlayerMove() && !$event->wasDisconnected()) {
            SystemUtils::freezePlayer($event->getPlayer(), true);
        }

        if (LoginTimeoutTask::isEnabled() && !$event->wasDisconnected()) {
            LoginTimeoutTask::getInstance()->addPlayer($event->getPlayer());
        }
    }

    /**
     * @priority LOWEST
     */
    public function onChangePassword(PlayerChangePasswordEvent $event) 
    {}

    /**
     * @priority LOWEST
     */
    public function onRegisterAccount(PlayerRegisterEvent $event)
    {}

    /**
     * @priority LOWEST
     */
    public function onAutoLoginFail(PlayerAutomaticallyLoginFailEvent $event)
    {
        $player = $event->getPlayer();
        $provider = Loader::getInstance()->getProvider();
        $provider->isRegistered(
            $player->getName()
        )->then(
            function (bool $result) use ($player) {

                if (!SystemUtils::isValidPlayer($player)) {
                    return;
                }

                if ($result) {
                    $message = $this->messages->get('waiting-login');
                } else {
                    $confimationFormat = '';
                    if ($this->settings->needToConfirmPassword()) {
                        $confimationFormat = '<confirmar_senha: string>';
                    }
                    $message = $this->messages->get('waiting-register', '{confirmation}', $confimationFormat);
                }
                $player->sendMessage($message);
            }
        )->catch(
            function () use ($player) {
                if (SystemUtils::isValidPlayer($player)) {
                    $player->sendMessage(
                        $this->messages->get('generic-error')
                    );
                }
            }
        );

        if ($this->settings->isAutoLoginEnabled()) {
            Loader::getInstance()->teleportWhenJoin($player);
        }

        if (LoginTimeoutTask::isEnabled()) {
            LoginTimeoutTask::getInstance()->addPlayer($player);
        }
    }
}