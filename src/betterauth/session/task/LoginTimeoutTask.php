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

namespace betterauth\session\task;

use betterauth\Loader;
use pocketmine\scheduler\Task;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\scheduler\PluginTask;
use pocketmine\Server;
use SmartCommand\utils\SingletonTrait;
use betterauth\session\AuthTimeout;

class LoginTimeoutTask extends PluginTask
{

    use SingletonTrait;

    /** @var boolean */
    private static $enabled = false;

    public static function isEnabled() : bool 
    {
        return self::$enabled;
    }

    /** @var array<int,AuthTimeout> */
    protected $players = [];

    /** @var integer */
    protected $maxTimeTicks;

    public static function init(
        int $maxTimeTicks
    )
    {
        Server::getInstance()->getScheduler()->scheduleRepeatingTask(
            $instance = new self(Loader::getInstance(), $maxTimeTicks),
            1
        );
        self::$enabled = true;

        self::setInstance($instance);
    }

    public function __construct(
        Plugin $owner,
        int $maxTimeTicks
    )
    {
        parent::__construct($owner);
        $this->maxTimeTicks = $maxTimeTicks;
    }

    public function addPlayer(Player $player) 
    {
        $this->players[$player->getLoaderId()] = new AuthTimeout($player, $this->maxTimeTicks);
    }

    public function removePlayer(Player $player)
    {
        unset($this->players[$player->getLoaderId()]);
    }

    public function getPlayerTimeout(Player $player)
    {
        return $this->players[$player->getLoaderId()] ?? null;
    }

    public function onRun($currentTick)
    {
        foreach ($this->players as $timeout) {
            $timeout->tick();
        }
    }
}