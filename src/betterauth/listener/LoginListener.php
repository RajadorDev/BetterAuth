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

use betterauth\event\PlayerLoggedOutEvent;
use betterauth\event\PlayerLoginSucessfullyEvent;
use betterauth\utils\Settings;
use betterauth\utils\SystemMessages;
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
    }

    /**
     * @priority LOWEST
     */
    public function onLogout(PlayerLoggedOutEvent $event)
    {}
}