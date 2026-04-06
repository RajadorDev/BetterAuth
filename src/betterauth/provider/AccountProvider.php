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

namespace betterauth\provider;

use betterauth\utils\promise\Promise;
use pocketmine\Player;
use betterauth\provider\exception\AuthException;
use betterauth\provider\exception\AccountAlreadyRegisteredException;
use betterauth\provider\exception\WrongPasswordException;
use betterauth\provider\exception\AccountNotFoundException;

/**
 * This interface will manage the account saving, updating and data collection
 */
interface AccountProvider
{

    /**
     * Try login a player using a raw password (raw password === password not encrypted)
     * @param Player $player
     * @param string $password
     * @return Promise<AuthException|AccountNotFoundException|WrongPasswordException|Account>
     */
    public function tryLogin(Player $player, string $password) : Promise;

    /**
     * Check if player is registered
     * @param string $username
     * @return Promise<boolean>
     */
    public function isRegistered(string $username) : Promise;

    /**
     * Try to register a new account
     * @param Player $player
     * @param string $password Raw password
     * @return Promise<AuthException|AccountAlreadyRegisteredException|Account>
     */
    public function tryRegister(Player $player, string $password) : Promise;

    /**
     * Try to find account by his username
     * @param string $username
     * @return Promise<AccountNotFoundException|Account>
     */
    public function getAccount(string $username) : Promise;


    /**
     * NOTE: This method should not be called to save unregistered accounts! It will return AccountNotFoundException
     * Only AccountProvider::tryRegister will save a unregistered account
     * 
     * @param Account $account
     * @return Promise<AccountNotFoundException|true>
     */
    public function updateAccount(Account $account) : Promise;

    /**
     * Change a account password. It will fail if the check password is different of the old password
     * @param string $playerName
     * @param string $rawCheckPassword Password used to check if the player can really change
     * @param string $newPasswordRaw
     * @return Promise<WrongPasswordException|AccountNotFoundException|Account>
     */
    public function changePassword(string $playerName, string $rawCheckPassword, string $newPasswordRaw) : Promise;

}