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

namespace betterauth\utils;

use pocketmine\Player;

/**
 * PocketMine 2 sucks. I need to get the clientId manually
 */
class PlayersClientIdMap 
{

    /** @var array<int,float> */
    protected static $clientIdMap = [];

    /**
     * @param integer $playerId
     * @param float $clientId
     * @return void
     */
    public static function set(int $playerId, float $clientId)
    {
        self::$clientIdMap[$playerId] = $clientId;
    }

    /**
     * @param Player|int $input
     * @return float|null
     */
    public static function get($input) 
    {
        if ($input instanceof Player) {
            $input = $input->getLoaderId();
        }
        assert(is_int($input));
        return self::$clientIdMap[$input] ?? null;
    }
}