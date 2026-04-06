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
use betterauth\session\LoginAttempts;
use betterauth\session\SessionController;
use betterauth\session\task\LoginTimeoutTask;
use betterauth\utils\Settings;
use betterauth\utils\SystemMessages;
use betterauth\utils\SystemUtils;
use pocketmine\event\Listener;
use pocketmine\Server;

final class LoginListener implements Listener
{

    /** @var Settings */
    protected $settings;

    /** @var SystemMessages */
    protected $messages;
    
    /**
     * @param Settings $settings
     * @param SystemMessages $messages
     */
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

        $controller = SessionController::getInstance();
        $playerName = $player->getName();
        $playerName = strtolower($playerName);
        foreach (Server::getInstance()->getOnlinePlayers() as $target) {
            if (!$controller->isLoggedIn($target) && strtolower($target->getName()) === $playerName) {
                $closeMessage = $this->messages->get('session-already-created', '{username}', $target->getName());
                $target->close('', $closeMessage);
            }
        }

        if ($this->settings->hideLoggoutPlayersNametag()) {
            $player->setNameTagVisible(true);
        }
    }

    /**
     * @priority LOWEST
     */
    public function onLogout(PlayerLoggedOutEvent $event)
    {
        
        $player = $event->getPlayer();
        
        if (!$event->wasDisconnected()) {

            if (!Loader::getInstance()->allowNotLoggedInPlayerMove()) {
                SystemUtils::freezePlayer($player, true);
            }

            if (LoginTimeoutTask::isEnabled()) {
                LoginTimeoutTask::getInstance()->addPlayer($player);
            }

            if ($this->settings->hideLoggoutPlayersNametag()) {
                $player->setNameTagVisible(false);
            }

        }

        LoginAttempts::clear($player);
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

        if ($this->settings->hideLoggoutPlayersNametag()) {
            $player->setNameTagVisible(false);
        }
    }
}