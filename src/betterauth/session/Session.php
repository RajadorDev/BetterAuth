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

use betterauth\provider\Account;
use pocketmine\Player;

class Session
{

    /** @var Player */
    protected $player;

    /** @var Account */
    protected $account;

    /** @var boolean */
    protected $loggedInAutomatically;

    /**
     * @param Player $player
     * @param Account $account
     * @param boolean $loggedInAutomatically
     * @return Session
     */
    public static function create(Player $player, Account $account, bool $loggedInAutomatically) : Session
    {
        return new self($player, $account, $loggedInAutomatically);
    }

    public function __construct(
        Player $player,
        Account $account,
        bool $loggedInAutomatically
    )
    {
        $this->account = $account;
        $this->player = $player;
        $this->loggedInAutomatically = $loggedInAutomatically;
    }

    public function getPlayer() : Player
    {
        return $this->player;
    }

    public function getAccount() : Account
    {
        return $this->account;
    }

    public function wasLoggedInAutomatically() : bool 
    {
        return $this->loggedInAutomatically;
    }

    public function destroy(bool $disconnected)
    {
        SessionController::getInstance()->logout($this, $disconnected);
    }


}