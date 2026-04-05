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

use pocketmine\event\Event;
use pocketmine\Player;
use pocketmine\Server;

final class SystemUtils
{

    /**
     * @param mixed $var
     * @return boolean
     */
    public static function isValidPlayer($var) : bool
    {
        return ($var instanceof Player && $var->isOnline());
    }

    /**
     * @template PocketMineEvent Event
     * @param PocketMineEvent $event
     * @return PocketMineEvent
     */
    public static function callEvent(Event $event) : Event
    {
        Server::getInstance()->getPluginManager()->callEvent($event);
        return $event;
    }

    /**
     * @param Player $player
     * @param boolean $setAsFreeze
     * @return void
     */
    public static function freezePlayer(Player $player, bool $setAsFreeze) 
    {
        $player->setDataProperty(
            Player::DATA_NO_AI,
            Player::DATA_TYPE_BYTE,
            (int) $setAsFreeze
        );
    }

    /**
     * @param string $rawPassword
     * @param integer $maxViewLength
     * @return string
     */
    public static function hideChars(string $rawPassword, int $maxViewLength) : string 
    {
        return 
        substr($rawPassword, 0, $maxViewLength) 
        . 
        str_repeat('*', strlen($rawPassword) - $maxViewLength);
    }

    /**
     * @param string $chars
     * @param integer $percent
     * @return integer
     */
    public static function getCharsPercentLength(string $chars, int $percent) : int
    {
        $length = strlen($chars);
        return (int) floor(
            ($length / 100) * $percent
        );
    }

}