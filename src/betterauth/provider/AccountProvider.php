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

interface AccountProvider
{

    /**
     * @param Player $player
     * @param string $password
     * @return Promise<AuthException|Account>
     */
    public function tryLogin(Player $player, string $password) : Promise;

    /**
     * @param string $username
     * @return Promise<boolean>
     */
    public function isRegistered(string $username) : Promise;

    /**
     * @param Player $player
     * @param string $password
     * @return Promise<AuthException|Account>
     */
    public function tryRegister(Player $player, string $password) : Promise;

    /**
     * @param string $username
     * @return Promise<Account|null>
     */
    public function getAccount(string $username) : Promise;


    /**
     * NOTE: This method should not be called to save unregistered accounts!
     * @param Account $account
     * @return Promise<AccountNotFoundException|null>
     */
    public function updateAccount(Account $account) : Promise;

}