<?php

declare(strict_types=1);

namespace betterauth\command\rule;

use betterauth\session\SessionController;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use SmartCommand\command\rule\CommandSenderRule;

class NotLoggedInCommandRule implements CommandSenderRule
{

    public function parse(CommandSender $sender, $command, int $executionType): bool
    {
        if ($sender instanceof Player) {
            return SessionController::getInstance()->isLoggedIn($sender);
        }
        return false;
    }

    public function getExecutionType(): int
    {
        return self::RULE_PRE_EXECUTION;
    }

    public function getMessage($command, CommandSender $sender): string
    {
        //TODO: Adicionar a mensagem pelo comando pegando de uma config
        return $command->getMessages()->get('not-logged-in');
    }

}
