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

    public function onPreLogin(PlayerPreLoginEvent $event) 
    {
        $player = $event->getPlayer();

        $event->setKickMessage($this->message->get("screen-cant-join", "{player_name}", $player->getName()));

        if ($this->session->getSessionByUsername($player->getName()) !== null) 
        {
            $event->setCancelled(true);
        }
    }

    public function onLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();

        Loader::getInstance()->getProvider()->getAccount($player->getName())
        ->then(
            function ($result) use ($player) 
            {
                if ($result instanceof Account && $result->matchPlayerAddress($player)) 
                {
                    try {
                        SessionController::getInstance()->acceptLogin($player, $result, true);

                    } catch (SessionAlreadyLoggedInException $error) {

                        $player->close('', $this->message->get("already-logged"));
                    }
                }
            }
        );
    }

    public function onMove(PlayerMoveEvent $event) 
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    public function onInteract(PlayerInteractEvent $event) 
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    public function onBreak(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

    public function onPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $this->message->sendCooldownMessage($player, "interation-not-logged", $this->settings->getBlockEventsMessageCooldown());

            $event->setCancelled(true);
        }
    }

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
