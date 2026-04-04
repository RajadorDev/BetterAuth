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

use betterauth\utils\Settings;
use Betterauth\Commands\LoginCommand;
use Betterauth\Commands\RegisterCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use SmartCommand\utils\SingletonTrait;
use betterauth\utils\SystemMessages;

class Loader extends PluginBase
{
 
    use SingletonTrait;

    /** @var SystemMessages */
    protected $messages;

    /** @var Settings */
    protected $settings;
 
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

        $this->saveResource('config.yml');
        
        $this->saveResource('messages.yml');
        $messagesFilePath = $dir . 'messages.yml';

        $this->messages = SystemMessages::create($messagesFilePath);
        $this->settings = new Settings($this->getConfig());
    }

    public function getMessages() : SystemMessages
    {
        return $this->messages;
    }

    public function getSettings() : Settings
    {
        return $this->settings;
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