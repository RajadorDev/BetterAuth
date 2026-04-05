<?php

namespace betterauth\listener;

use betterauth\Loader;
use betterauth\session\SessionController;
use betterauth\utils\Settings;
use betterauth\utils\SystemMessages;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;

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
}
