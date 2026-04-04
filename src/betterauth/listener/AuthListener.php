<?php

namespace betterauth\listener;

use betterauth\session\SessionController;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

class AuthListener implements Listener
{

    /** @var SessionController */
    private $session;

    public function __construct()
    {
        $this->session = new SessionController();
    }

    public function onMove(PlayerMoveEvent $event) 
    {
        $player = $event->getPlayer();

        if (!$this->session->isLoggedIn($player)) 
        {
            $player->sendMessage("Need logar para walk");

            $event->setCancelled(true);
        }
    }
}
