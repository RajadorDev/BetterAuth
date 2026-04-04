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

namespace betterauth;

use Betterauth\Commands\LoginCommand;
use Betterauth\Commands\RegisterCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use SmartCommand\utils\SingletonTrait;

class Loader extends PluginBase
{
 
    use SingletonTrait;
 
    public function onLoad()
    {
        self::setInstance($this);
    }

    public function onEnable()
    {
        if (!file_exists($dir = $this->getDataFolder()))
        {
            mkdir($dir);
        }
    }

    /**
     * @param string $identifier
     * @param mixed $defaultValue
     * @param boolean $warnConsole
     * @return mixed
     */
    public function getConfigValue(string $identifier, $defaultValue = null, bool $warnConsole = true)
    {
        $settings = $this->getConfig();
        if ($settings->exists($identifier)) {
            return $settings->get($identifier);
        } else if ($warnConsole) {
            $this->getLogger()->warning("Setting with id $identifier does not found!");
        }
        return $defaultValue;
    }

    public function registerListener(Listener $listener)
    {
        Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
    }

    public function registerCommands() {
        $cm = $this->getServer()->getCommandMap();
        $cm->register('register', new RegisterCommand());
        $cm->register('login', new LoginCommand());
    }
}