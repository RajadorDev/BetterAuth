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

namespace betterauth\session;

use betterauth\Loader;
use betterauth\session\task\LoginTimeoutTask;
use pocketmine\Player;

class AuthTimeout
{

    /** @var Player */
    protected $player;

    /** @var integer */
    protected $maxTimeTicks;

    /** @var integer */
    protected $currentLoggedOutTicks = 0;

    public function __construct(
        Player $player,
        int $maxTimeTicks
    )
    {
        $this->player = $player;
        $this->maxTimeTicks = $maxTimeTicks;
    }

    public function getPlayer() : Player 
    {
        return $this->player;
    }

    public function tick()
    {
        $this->currentLoggedOutTicks++;
        if ($this->currentLoggedOutTicks >= $this->maxTimeTicks) {
            $this->destroy();
            $this->getPlayer()->close('', Loader::getInstance()->getMessages()->get('login-timeout-screen', null, null, '', false));
        }
    }

    public function destroy()
    {
        LoginTimeoutTask::getInstance()->removePlayer($this->player);
    }


}