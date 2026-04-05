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

namespace betterauth\event;

use betterauth\provider\Account;
use pocketmine\event\player\PlayerEvent;
use pocketmine\Player;

abstract class PlayerAccountEvent extends PlayerEvent
{

    /** @var Account */
    protected $account;

    public function __construct(
        Player $player,
        Account $account
    )
    {
        $this->player = $player;
        $this->account = $account;
    }

    public function getAccount() : Account
    {
        return $this->account;
    }
    
}