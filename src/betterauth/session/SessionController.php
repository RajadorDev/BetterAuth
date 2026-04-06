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

use betterauth\event\PlayerLoggedOutEvent;
use betterauth\event\PlayerLoginSucessfullyEvent;
use betterauth\provider\Account;
use betterauth\session\exception\SessionAlreadyLoggedInException;
use betterauth\utils\SystemUtils;
use pocketmine\Player;
use SmartCommand\utils\SingletonTrait;

final class SessionController 
{

    use SingletonTrait;

    /** @var array<string,Session> */
    protected $sessionsByName = [];

    /** @var array<int,Session> */
    protected $sessionsPerLoaderId = [];

    public static function init()
    {
        self::setInstance(new self);
    }

    /**
     * @param Player $player
     * @return boolean
     */
    public function isLoggedIn(Player $player) : bool
    {
        return isset($this->sessionsPerLoaderId[$player->getLoaderId()]);
    }

    /**
     * TODO
     * @param Player $player
     * @return Session|null
     */
    public function getPlayerSession(Player $player)
    {
        return $this->sessionsPerLoaderId[$player->getLoaderId()] ?? null;
    }

    /**
     * @param string $username
     * @return Session|null
     */
    public function getSessionByUsername(string $username)
    {
        return $this->sessionsByName[strtolower($username)] ?? null;
    }

    /**
     * @param Player $player
     * @param Account $account
     * @param boolean $wasLoggedInAutomatically
     * @return Session
     * @throws SessionAlreadyLoggedInException
     */
    public function acceptLogin(Player $player, Account $account, bool $wasLoggedInAutomatically) : Session
    {
        $playerName = $player->getName();
        $playerName = strtolower($playerName);

        $loaderId = $player->getLoaderId();

        if (isset($this->sessionsByName[$playerName]) || isset($this->sessionsPerLoaderId[$loaderId])) {
            throw new SessionAlreadyLoggedInException("Session $playerName:$loaderId is already registered");
        }
        
        $session =
        $this->sessionsPerLoaderId[$loaderId]
        =
        $this->sessionsByName[$playerName] 
        = Session::create($player, $account, $wasLoggedInAutomatically);

        SystemUtils::callEvent(new PlayerLoginSucessfullyEvent($player, $session, $wasLoggedInAutomatically));
        return $session;
    }

    public function logout(Session $session, bool $disconnected)
    {
        unset($this->sessionsPerLoaderId[$session->getPlayer()->getLoaderId()]);
        unset($this->sessionsByName[strtolower($session->getPlayer()->getName())]);
        SystemUtils::callEvent(new PlayerLoggedOutEvent($session->getPlayer(), $session, $disconnected));
    }


}