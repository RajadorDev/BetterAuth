<?php

namespace betterauth\listener;

use betterauth\Loader;
use betterauth\provider\Account;
use betterauth\session\exception\SessionAlreadyLoggedInException;
use betterauth\session\Session;
use betterauth\session\SessionController;

use betterauth\utils\Settings;
use betterauth\utils\SystemMessages;

use pocketmine\Player;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
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

    
    public function __construct(SessionController $session)
    {
        $this->session = $session;
        $this->message = Loader::getInstance()->getMessages();
        $this->settings = Loader::getInstance()->getSettings();
    }

    public function onPreprocessCommand(PlayerCommandPreprocessEvent $event) 
    {

    }

    public function onPreprocessCommand(PlayerCommandPreprocessEvent $event) 
    {

    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onMove(PlayerMoveEvent $event) 
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    /**
     * @priority LOWEST
     * @ignoreCancelled TRUE
     */
    public function onInteract(PlayerInteractEvent $event) 
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

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

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

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

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

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

        if ($entity instanceof Player && !$this->session->isLoggedIn($entity)) 
        {
            $event->setCancelled(true);

            return;
        }

        if ($event instanceof EntityDamageByEntityEvent) 
        {
            $entityDamager = $event->getDamager();

            if ($entityDamager instanceof Player && !$this->session->isLoggedIn($entity)) 
            {
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

        $session = $this->session->getSessionByUsername($player->getName());
        if ($session instanceof Session) 
        {
            $session->destroy();
        }
    }
}
