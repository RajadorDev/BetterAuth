<?php

use betterauth\Loader;
use betterauth\provider\Account;
use betterauth\provider\exception\AccountNotFoundException;
use betterauth\provider\exception\WrongPasswordException;
use betterauth\session\exception\SessionAlreadyLoggedInException;
use betterauth\session\SessionController;
use betterauth\utils\SystemUtils;
use pocketmine\Player;

/** @var Player $player */

Loader::getInstance()->getProvider()->tryLogin(
    $player,
    'natanbx01234'
)->then(
    function ($result) use ($player) {

        if (!SystemUtils::isValidPlayer($player)) {
            return;
        }

        try {
            if ($result instanceof Exception) {
                throw $result;
            }

            try {
                SessionController::getInstance()->acceptLogin($player, $result, false);
            } catch (SessionAlreadyLoggedInException $error) {
                $player->close('', 'Já tem alguém logado com seu nome');
            }

        } catch (AccountNotFoundException $error) {
            $player->sendMessage("Sua conta não existe");
        } catch (WrongPasswordException $error) {
            $player->sendMessage("Sua senha está errada");
        }
    }
)->catch(
    function () use ($player) {
        if (!SystemUtils::isValidPlayer($player)) {
            return;
        }

        $player->sendMessage('Ocorreu um erro bla bla bla');
    }
);


Loader::getInstance()->getProvider()->getAccount($player->getName())
->then(
    function ($result) use ($player) {
        if ($result instanceof Account && $result->matchPlayerAddress($player)) {
            try {
                SessionController::getInstance()->acceptLogin($player, $result, true);
            } catch (SessionAlreadyLoggedInException $error) {
                $player->close('', 'Você já está logado');
            }
        }
    }
);