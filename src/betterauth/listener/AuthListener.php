<?php

namespace betterauth\listener;

use betterauth\event\PlayerAutomaticallyLoginFailEvent;
use betterauth\Loader;
use betterauth\provider\Account;
use betterauth\session\exception\SessionAlreadyLoggedInException;
use betterauth\session\Session;
use betterauth\session\SessionController;

use betterauth\utils\Settings;
use betterauth\utils\SystemMessages;
use betterauth\utils\SystemUtils;
use pocketmine\Player;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerPreLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;

final class AuthListener implements Listener
{

    /** @var SessionController */
    private $session;

    /** @var SystemMessages */
    private $message;

    /** @var Settings */
    private $settings;

    /** @var Loader */
    private $loader;


    public function __construct(SessionController $session)
    {
        $this->session = $session;

        $this->loader = Loader::getInstance();
        $this->message = $this->loader->getMessages();
        $this->settings = $this->loader->getSettings();

    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onPreprocessCommand(PlayerCommandPreprocessEvent $event)
    {
        $commandLine = $event->getMessage();

        if (isset($commandLine[0]) && $commandLine[0] === '/' && !SessionController::getInstance()->isLoggedIn($event->getPlayer()) && !$this->loader->isAuthCommand($commandLine)) {
            $this->message->send($event->getPlayer(), 'not-logged-in-command');

            $event->setCancelled(true);
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();

        if ($this->loader->allowNotLoggedInPlayerMove()) {
            return;
        }

        if (!$this->session->isLoggedIn($player)) {
            $this->message->sendCooldownMessage($player, 'interation-not-logged', $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }


    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onPreLogin(PlayerPreLoginEvent $event)
    {
        $playerName = $event->getPlayer()->getName();

        if ($this->session->getSessionByUsername($playerName) !== null) {
            $event->setKickMessage($this->message->get('session-already-created', '{username}', $playerName));

            $event->setCancelled(true);
        }
    }

    /**
     * @priority NORMAL
     */
    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();

        $this->loader->onPlayerJoin($player);

        if (!$this->settings->isAutoLoginEnabled()) 
        {
            SystemUtils::callEvent(new PlayerAutomaticallyLoginFailEvent($player));
            return;
        }

        Loader::getInstance()->getProvider()->getAccount($player->getName())
        ->then(
            function ($result) use ($player) 
            {
                if (!SystemUtils::isValidPlayer($player)) {
                    return;
                }

                if ($result instanceof Account && $result->matchAutoLogin($player)) 
                {
                    try {
                        $this->session->acceptLogin($player, $result, true);
                    } catch (SessionAlreadyLoggedInException $error) {
                        $player->close('', $this->message->get('session-already-created', null, null, 'Message not found', false));
                    }
                } else {
                    SystemUtils::callEvent(new PlayerAutomaticallyLoginFailEvent($player));
                }
            }
        )->catch(
            function () use ($player) {
                if (!SystemUtils::isValidPlayer($player)) {
                    return;
                }

                SystemUtils::callEvent(new PlayerAutomaticallyLoginFailEvent($player));
            }
        );
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onInteract(PlayerInteractEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) {
            $this->message->sendCooldownMessage($player, 'interation-not-logged', $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onBreak(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) {
            $this->message->sendCooldownMessage($player, 'interation-not-logged', $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) {
            $this->message->sendCooldownMessage($player, 'interation-not-logged', $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onDamage(EntityDamageEvent $event)
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player && !$this->session->isLoggedIn($entity)) {
            $event->setCancelled(true);

            return;
        }

        if ($event instanceof EntityDamageByEntityEvent) {
            $entityDamager = $event->getDamager();

            if ($entityDamager instanceof Player && !$this->session->isLoggedIn($entity)) {
                $event->setCancelled(true);
            }
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();

        $session = $this->session->getPlayerSession($player);
        if ($session instanceof Session) {
            $session->destroy(true);
        }
    }
}
