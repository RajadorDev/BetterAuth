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

namespace betterauth\event;

use betterauth\session\Session;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

class PlayerLoginSucessfullyEvent extends PlayerEvent
{

    public static $handlerList = null;

    /** @var Session */
    protected $session;

    /** @var boolean */
    protected $wasLoggedInAutomatically;

    public function __construct(
        Player $player,
        Session $session,
        bool $wasLoggedInAutomatically
    )
    {
        $this->player = $player;
        $this->session = $session;
        $this->wasLoggedInAutomatically = $wasLoggedInAutomatically;
    }

    public function getSession() : Session
    {
        return $this->session;
    }

    public function wasLoggedInAutomatically() : bool 
    {
        return $this->wasLoggedInAutomatically;
    }
}