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

namespace betterauth\provider\types\file\task;

use betterauth\provider\Account;
use betterauth\provider\exception\WrongPasswordException;

class LoginAccountProcessAsyncTask extends FileAccountProcessAsyncTask
{

    public function __construct(string $path, string $password, string $playerAddress, float $playerClientId)
    {
        return parent::__construct([
            'password' => $password,
            'address' => $playerAddress,
            'clientId' => $playerClientId
        ], $path);
    }

    protected function processAccountAndResult(Account $account, array $safeVarValues)
    {
        if ($account->matchPassword($safeVarValues['password'])) {
            $account->updateLastLogin($safeVarValues['address'], $safeVarValues['clientId']);
            $account->save($safeVarValues['file_path']);
            return $account;
        }

        return new WrongPasswordException("Wrong {$account->getUsername()} password");
    }
}