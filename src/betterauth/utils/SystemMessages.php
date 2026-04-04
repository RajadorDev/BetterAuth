<?php

declare (strict_types=1);
 
/***
 *   
 * Rajador Developer
 * 
 * ▒█▀▀█ ░█▀▀█ ░░░▒█ ░█▀▀█ ▒█▀▀▄ ▒█▀▀▀█ ▒█▀▀█ 
 * ▒█▄▄▀ ▒█▄▄█ ░▄░▒█ ▒█▄▄█ ▒█░▒█ ▒█░░▒█ ▒█▄▄▀ 
 * ▒█░▒█ ▒█░▒█ ▒█▄▄█ ▒█░▒█ ▒█▄▄▀ ▒█▄▄▄█ ▒█░▒█
 * 
 * GitHub: https://github.com/rajadordev
 * 
 * Discord: rajadortv
 * 
 * 
**/ 

namespace betterauth\utils;

use betterauth\Loader;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;

class SystemMessages
{

    /** @var Config */
    protected $file;

    /** @var array<string,float> */
    protected $cooldown = [];

    public function __construct(
        Config $file
    )
    {
        $this->file = $file;
    }

    public static function create(string $path) : SystemMessages
    {
        return new SystemMessages(
            new Config(
                $path 
            )
        );
    }

    public function getFile() : Config
    {
        return $this->file;
    }

    /**
     * @param string $identifier
     * @param string|string[]|null $replace
     * @param string|string[]|null $to
     * @param string $defaultMessage
     * @return string
     */
    public function get(string $identifier, $replace = null, $to = null, string $defaultMessage = 'Message not found') : string
    {
        if (is_string($message = $this->file->get($identifier, null))) {
            if ($replace !== null) {
                $message = str_replace($replace, $to, $message);
            }
        } else {
            Loader::getInstance()->getLogger()->warning("Message $identifier does not found");
        }

        return $message ?? $defaultMessage;
    }

    /**
     * @param Player|CommandSender $sender
     * @param string $messageId
     * @param string|string[]|null $replace
     * @param string|string[]|null $to
     * @param string $defaultMessage
     * @return void
     */
    public function send($sender, string $messageId, $replace = null, $to = null, string $defaultMessage = 'Message not found')
    {
        $sender->sendMessage(
            $this->get(
                $messageId,
                $replace,
                $to,
                $defaultMessage
            )
        );
    }

    /**
     * @param CommandSender $sender
     * @param string $messageId
     * @param integer $cooldownSeconds
     * @param string|string[]|null $replace
     * @param string|string[]|null $to
     * @param string $defaultMessage
     * @return void
     */
    public function sendCooldownMessage($sender, string $messageId, int $cooldownSeconds, $replace = null, $to = null, string $defaultMessage = 'Message not found')
    {
        $cooldownIdentifier = ($sender instanceof Player ? $sender->getLoaderId() : ('@' . $sender->getName())) . ':' . $messageId;
        $now = microtime(true);
        if (!isset($this->cooldown[$cooldownIdentifier]) || $now >= $this->cooldown[$cooldownIdentifier]) {
            $this->send(
                $sender,
                $messageId,
                $replace,
                $to,
                $defaultMessage
            );
            $this->cooldown[$cooldownIdentifier] = $now + $cooldownSeconds;
        }
    }

}