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

namespace betterauth\provider\types\file;

use betterauth\provider\Account;
use betterauth\provider\AccountProvider;
use betterauth\provider\exception\AccountAlreadyRegisteredException;
use betterauth\provider\exception\AccountNotFoundException;
use betterauth\provider\types\file\task\ChangePasswordAsyncTask;
use betterauth\provider\types\file\task\GetAccountFileAsyncTask;
use betterauth\provider\types\file\task\LoginAccountProcessAsyncTask;
use betterauth\provider\types\file\task\RegisterAccountProcessAsyncTask;
use betterauth\provider\types\file\task\UpdateAccountProcessAsyncTask;
use betterauth\utils\promise\Promise;
use betterauth\utils\promise\PromiseResolver;
use pocketmine\Player;

class FileAccountProvider implements AccountProvider
{

    /** @var string */
    protected $accountsDir;

    /**
     * @param string $dir
     */
    public function __construct(
        string $dir
    )
    {
        $this->accountsDir = $dir;
        if (!file_exists($dir)) {
            mkdir($dir);
        }
    }

    public function getPlayerFilePath(string $username) : string 
    {
        return $this->accountsDir . strtolower($username) . '.json';
    }

    public function isRegistered(string $username): Promise
    {
        $promiseResolver = new PromiseResolver;
        $promiseResolver->resolve(
            $this->syncIsRegistered($username)
        );
        return $promiseResolver->getPromise();
    }

    private function syncIsRegistered(string $username) : bool 
    {
        return file_exists($this->getPlayerFilePath($username));
    }

    public function tryLogin(Player $player, string $password): Promise
    {
        $task = new LoginAccountProcessAsyncTask(
            $this->getPlayerFilePath($player->getName()),
            $password
        );
        LoginAccountProcessAsyncTask::schedule($task);
        return $task->getPromise();
    }

    public function tryRegister(Player $player, string $password): Promise
    {
        $username = $player->getName();
        if ($this->syncIsRegistered($username)) {
            $resolver = new PromiseResolver;
            $resolver->resolve(new AccountAlreadyRegisteredException("Account $username is already registered"));
            return $resolver->getPromise();
        }

        $account = Account::create($username, $password, $player->getAddress(), $player->getClientSecret());
        $task = new RegisterAccountProcessAsyncTask($this->getPlayerFilePath($username), $account);
        $task::schedule($task);
        return $task->getPromise();
    }

    /**
     * @param string $username
     * @return Promise<AccountNotFoundException|Account>
     */
    public function getAccount(string $username): Promise
    {
        $path = $this->getPlayerFilePath($username);
        $task = new GetAccountFileAsyncTask([], $path);
        $task::schedule($task);
        return $task->getPromise();
    }

    public function updateAccount(Account $account): Promise
    {
        $username = $account->getUsername();
        $path = $this->getPlayerFilePath($username);

        $task = new UpdateAccountProcessAsyncTask($path, $account);
        $task::schedule($task);
        return $task->getPromise();
    }

    public function changePassword(string $playerName, string $rawCheckPassword, string $newPasswordRaw): Promise
    {
        if ($this->syncIsRegistered($playerName)) {
            $resolver = new PromiseResolver;
            $resolver->resolve(new AccountNotFoundException("Account $playerName does not found"));
            return $resolver->getPromise();
        }

        $task = new ChangePasswordAsyncTask(
            $this->getPlayerFilePath($playerName),
            $rawCheckPassword,
            $newPasswordRaw
        );
        $task::schedule($task);
        return $task->getPromise();
    }


}