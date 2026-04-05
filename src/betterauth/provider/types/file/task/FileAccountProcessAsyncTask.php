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
use Exception;

abstract class FileAccountProcessAsyncTask extends AsyncPromiseTask
{

    public function __construct(array $data, string $path)
    {
        $data['file_path'] = $path;
        return parent::__construct($data, true);
    }

    /**
     * @param array{file_path:string} $safeVarValues
     * @return mixed
     */
    protected function processAndSerializeResult(array $safeVarValues)
    {
        $filePath = $safeVarValues['file_path'];

        if (!file_exists($filePath)) {
            return serialize(new AccountNotFoundException("Account $filePath does not exists"));
        }

        $fileData = file_get_contents($filePath);

        $jsonData = json_decode($fileData, true);

        $result = $this->processAccountAndResult(
            Account::unserialize(
                $jsonData
            ),
            $safeVarValues
        );

        if ($result instanceof DynamicObject) {
            return serialize($result->jsonSerialize());
        } else if ($result instanceof Exception) {
            return serialize($result);
        } else if (self::isSafeThreadValue($result)) {
            return serialize($result);
        }

        throw new Exception("Value: " . gettype($result) . ' can\'t be async result');

    }

    /**
     * It must to return the value of the promise
     * @param Account $account
     * @param array $safeVarValues
     * @return mixed
     */
    abstract protected function processAccountAndResult(Account $account, array $safeVarValues);
}