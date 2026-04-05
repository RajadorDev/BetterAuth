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
use betterauth\provider\exception\AccountAlreadyRegisteredException;
use betterauth\provider\exception\AuthException;
use betterauth\utils\async\AsyncPromiseTask;
use betterauth\utils\DynamicObject;

class RegisterAccountProcessAsyncTask extends AsyncPromiseTask
{

    public function __construct(string $filePath, Account $account)
    {
        return parent::__construct(
            [
                'file_path' => $filePath,
                'account' => $account->jsonSerialize()
            ],
            true
        );
    }

    protected function processAndSerializeResult(array $safeVarValues)
    {
        $path = $safeVarValues['file_path'];

        if (file_exists($path)) {
            return serialize(new AccountAlreadyRegisteredException("Account {$path} does not found"));
        }

        $accountData = $safeVarValues['account'];

        $account = DynamicObject::globalUnserialize($accountData);

        return serialize(
            $account->jsonSerialize()
        );
    }

}