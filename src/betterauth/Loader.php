<?php

declare(strict_types=1);

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

use betterauth\commands\ChangePasswordCommand;
use betterauth\commands\LogoutCommand;
use betterauth\utils\Settings;
use Betterauth\Commands\LoginCommand;
use Betterauth\Commands\RegisterCommand;
use betterauth\listener\AuthListener;
use betterauth\listener\LoginListener;
use betterauth\provider\AccountProvider;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use SmartCommand\utils\SingletonTrait;
use betterauth\utils\SystemMessages;
use betterauth\provider\types\file\FileAccountProvider;
use betterauth\room\LoggedOutRoom;
use betterauth\session\SessionController;
use betterauth\session\task\LoginTimeoutTask;
use betterauth\session\tips\AuthTipsManager;
use betterauth\utils\SystemUtils;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use rajadordev\autoupdater\api\CheckUpdateScheduler;
use rajadordev\autoupdater\api\plugin\defaults\github\GitHubPluginUpdaterAPI;
use rajadordev\autoupdater\api\PluginUpdaterChecker;
use SmartCommand\command\SmartCommand;
use SmartCommand\message\DefaultMessages;

class Loader extends PluginBase
{

    use SingletonTrait;

    /** @var SystemMessages */
    protected $messages;

    /** @var Settings */
    protected $settings;

    /** @var AccountProvider */
    protected $provider;

    /** @var LoggedOutRoom|null */
    protected $loggedOutRoom = null;

    /** @var array<string,SmartCommand> */
    protected $allowedNotLoggedInCommands = [];
 
    public function onLoad()
    {
        self::setInstance($this);
    }

    public function onEnable()
    {
        if (!file_exists($dir = $this->getDataFolder())) {
            mkdir($dir);
        }

        $this->saveResource('config.yml');

        $this->saveResource('messages.yml');
        $messagesFilePath = $dir . 'messages.yml';

        $this->messages = SystemMessages::create($messagesFilePath);
        $this->settings = new Settings($this->getConfig());


        $accountsFolder = $dir . 'accounts' . DIRECTORY_SEPARATOR;
        $fileProvider = new FileAccountProvider($accountsFolder);
        $this->setProvider($fileProvider);

        $this->loggedOutRoom = LoggedOutRoom::createFromSettings($this->settings, $this);

        SessionController::init();

        $this->initListeners();

        $this->registerCommands();

        $this->tryAutoUpdate();

        $this->registerTimeoutTask();

        $this->registerTipsTask();
    }

    protected function registerTipsTask()
    {
        if ($this->settings->isAuthTipsEnabled()) {
            AuthTipsManager::init($this);
        }
    }

    protected function registerTimeoutTask()
    {
        $maxTime = $this->settings->getInteger(Settings::MAX_AUTH_TIMEOUT, 25, false);

        if ($maxTime > 0) {
            LoginTimeoutTask::init($maxTime * 20);
            $this->getLogger()->info("Max auth time setted as $maxTime seconds");
        } else {
            $this->getLogger()->info("Auth timout is disabled! Players can keel online even if not login");
        }
    }

    protected function tryAutoUpdate()
    {
        if ($this->settings->getBool(Settings::AUTO_UPDATE, true, false)) {
            $autoUpdaterPlugin = $this->getServer()->getPluginManager()->getPlugin('AutoPluginUpdater');

            if ($autoUpdaterPlugin instanceof Plugin) {
                $this->getLogger()->info("Searching for updates soon...");
                CheckUpdateScheduler::getInstance()->schedule(
                    new PluginUpdaterChecker(
                        $this,
                        GitHubPluginUpdaterAPI::createFromPlugin(
                            $this,
                            'RajadorDev',
                            'BetterAuth'
                        )
                    )
                );
            } else {
                $this->getLogger()->warning("AutoPluginUpdater is not enabled! Please install AutoPluginUpdater to update BetterAuth, SmartCommand automatically from: https://github.com/RajadorDev/AutoPluginUpdater");
            }
        } else {
            $this->getLogger()->info("Auto update is disabled, the BetterAuth will not update automatically!");
        }
    }

    public function setProvider(AccountProvider $provider)
    {
        $this->provider = $provider;
    }

    public function getMessages(): SystemMessages
    {
        return $this->messages;
    }

    public function getSettings(): Settings
    {
        return $this->settings;
    }

    public function getProvider(): AccountProvider
    {
        return $this->provider;
    }

    public function getLoggedOutRoom()
    {
        return $this->loggedOutRoom;
    }

    public function registerListener(Listener $listener)
    {
        Server::getInstance()->getPluginManager()->registerEvents($listener, $this);
    }

    protected function registerCommands() {

        $messages = DefaultMessages::PORTUGUESE();

        $messages->add(
            $this->messages->getFile()->getAll()
        );

        $commandMap = $this->getServer()->getCommandMap();
        
        foreach (
            [
                new LoginCommand($messages),
                new RegisterCommand($messages),
                new LogoutCommand($messages),
                new ChangePasswordCommand($messages)
            ] as $authCommand
        ) {
            $this->allowedNotLoggedInCommands[$authCommand->getName()] = $authCommand;
            $commandMap->register('betterauth', $authCommand);
        }
    }

    public function isAuthCommand(string $commandLine) : bool
    {
        $splitedCommand = explode(' ', $commandLine);

        $commandName = array_shift($splitedCommand);

        $commandName = substr($commandName, 1);

        $commandFound = Server::getInstance()->getCommandMap()->getCommand($commandName);


        return (
            $commandFound instanceof Command
            && 
            isset($this->allowedNotLoggedInCommands[$commandFound->getName()])
        );
    }

    public function allowNotLoggedInPlayerMove() : bool 
    {
        if ($this->loggedOutRoom) {
            return $this->loggedOutRoom->canMove();
        }
        return false;
    }

    public function onPlayerJoin(Player $player)
    {
        if (!$this->allowNotLoggedInPlayerMove()) {
            SystemUtils::freezePlayer($player, true);
        }
        
        if (!$this->getSettings()->isAutoLoginEnabled()) {
            $this->teleportWhenJoin($player);
        }

        $message = $this->messages->get(
            'join-message',
            [
                '{username}',
                '{server_port}'
            ],
            [
                $player->getName(),
                $player->getServer()->getPort()
            ],
            '',
            false
        );

        $player->sendMessage($message);
    }

    public function teleportWhenJoin(Player $player)
    {
        //TODO
    }

    protected function initListeners() 
    {
        foreach ([
            new LoginListener($this->settings, $this->messages),
            new AuthListener(SessionController::getInstance())
        ] as $listener) {
            $this->registerListener($listener);
        }
    }

}