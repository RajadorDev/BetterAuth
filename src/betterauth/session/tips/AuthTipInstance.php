<?php

declare (strict_types=1);
 
/***
 *   
 * Rajador Developer
 * 
 * ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
 * ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
 * ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/ 

namespace betterauth\session\tips;

use pocketmine\Player;

class AuthTipInstance
{

    /** @var Player */
    protected $player;

    /** @var string */
    protected $tip, $popup;

    /** @var integer */
    protected $currentTicks = 0;

    public function __construct(
        Player $player,
        string $tip, string $popup
    )
    {
        $this->player = $player;
        $this->tip = $tip;
        $this->popup = $popup;
    }

    public function tick()
    {
        $this->currentTicks += 1;

        if ($this->currentTicks >= 20) {
            $this->currentTicks = 0;
            $this->sendTips();
        }
    }

    protected function sendTips()
    {
        $player = $this->player;
        $player->sendTip($this->tip);
        $player->sendPopup($this->popup);
    }
    
}