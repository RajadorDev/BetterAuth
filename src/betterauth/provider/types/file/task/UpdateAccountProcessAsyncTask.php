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
use betterauth\provider\exception\AccountNotFoundException;
use betterauth\utils\async\AsyncPromiseTask;
use betterauth\utils\DynamicObject;

class UpdateAccountProcessAsyncTask extends AsyncPromiseTask
{

    public function __construct(string $filePath, Account $account)
    {
        return parent::__construct([
            'file_path' => $filePath,
            'account' => $account->jsonSerialize()
        ], true);
    }

    protected function processAndResult(array $safeVarValues)
    {
        $path = $safeVarValues['file_path'];

        if (!file_exists($path)) {
            return new AccountNotFoundException("Account $path does not found");
        }
        $accountData = $safeVarValues['account'];

        /** @var Account */
        $account = DynamicObject::globalUnserialize($accountData);
        assert($account instanceof Account);
        $account->save($path);
        return true;
    }

}