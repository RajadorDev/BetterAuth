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

namespace betterauth\room;

use betterauth\Loader;
use betterauth\utils\Settings;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\Server;

class LoggedOutRoom 
{

    /** @var Level */
    protected $world;

    /** @var Level|null $targetWorld */
    protected $targetWorld;

    /** @var boolean */
    protected $allowMove;

    /**
     * @param Settings $settings
     * @param Loader $plugin
     * @return LoggedOutRoom|null
     */
    public static function createFromSettings(Settings $settings, Loader $plugin) 
    {
        $server = Server::getInstance();
        if ($settings->getBool(Settings::LOGGED_OUT_ROOM_ENABLED, true, true)) {
            $worldName = $settings->getString(Settings::LOGGED_OUT_WORLD, '', true);

            if (!$server->loadLevel($worldName)) {
                $plugin->getLogger()->warning("Mapa $worldName não existe, portantanto o sistema de sala de espera de login foi desativado!");
                return null;
            }

            $world = $server->getLevelByName($worldName);

            $targetWorldName = $settings->getValue(Settings::LOGGED_OUT_TARGET_WORLD, true, null, false);

            if (!is_null($targetWorldName) && $targetWorldName !== '') {
                $server->loadLevel((string) $targetWorldName);
                $targetWorld = $server->getLevelByName((string) $targetWorldName) ?? null;
            } else {
                $targetWorld = $server->getDefaultLevel();
            }

            return new self(
                $world,
                $settings->getBool(Settings::LOGGET_OUT_ALLOW_MOVE, true, true),
                $targetWorld
            );
        }
        return null;
    }

    /**
     * @param Level $world
     * @param boolean $allowMove
     * @param Level|null $targetWorld
     */
    public function __construct(
        Level $world,
        bool $allowMove,
        $targetWorld = null
    )
    {
        $this->world = $world;
        $this->allowMove = $allowMove;
        $this->targetWorld = $targetWorld;
    }

    public function getSpawn() : Position
    {
        return $this->world->getSafeSpawn();
    }

    public function getTargetSpawn()
    {
        if ($this->targetWorld) {
            return $this->targetWorld->getSafeSpawn();
        }
        return null;
    }

    public function mustTeleportToTargetWorld(Player $player) : bool 
    {
        return ($this->targetWorld instanceof Level && $this->targetWorld !== $player->getLevel());
    }

    public function canMove() : bool 
    {
        return $this->allowMove;
    }
}