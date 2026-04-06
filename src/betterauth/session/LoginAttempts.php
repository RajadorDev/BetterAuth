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
use pocketmine\Player;

class LoginAttempts 
{

    /** @var array<string,int> */
    protected static $maxLoginAttempts = [];

    public static function hashPlayer(Player $player) : string 
    {
        return self::hash($player->getName(), $player->getAddress());
    }

    public static function hash(string $username, string $address) : string 
    {
        $username = strtolower($username);
        return "$username:$address";
    }

    /**
     * @param Player $player
     * @return boolean True if the player can do more attempts. But if false, it means that the player was kicked
     */
    public static function addAttempt(Player $player) : bool
    {
        if (!isset(self::$maxLoginAttempts[$loaderId = $player->getLoaderId()])) {
            self::$maxLoginAttempts[$loaderId] = 0;
        }

        self::$maxLoginAttempts[$loaderId] += 1;
        
        if (self::$maxLoginAttempts[$loaderId] >= Loader::getInstance()->getSettings()->getMaxLoginAttempts()) {
            unset(self::$maxLoginAttempts[$loaderId]);
            $message = Loader::getInstance()->getMessages()->get('screen-max-attempts', null, null, '', false);
            $player->close('', $message);
            return false;
        }
        return true;
    }

    public static function clear(Player $player)
    {
        unset(self::$maxLoginAttempts[$player->getLoaderId()]);
    }
}