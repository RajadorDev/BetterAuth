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

namespace betterauth\provider\types\file\task;

use betterauth\provider\Account;
use betterauth\provider\exception\WrongPasswordException;

class ChangePasswordAsyncTask extends FileAccountProcessAsyncTask
{

    public function __construct(
        string $filePath,
        string $rawCheckPassword,
        string $newPasswordRaw
    )
    {
        parent::__construct([
            'check_password' => $rawCheckPassword,
            'new_password' => $newPasswordRaw
        ], $filePath);
    }

    protected function processAccountAndResult(Account $account, array $safeVarValues)
    {
        $checkPassword = $safeVarValues['check_password'];
        $newPassword = $safeVarValues['new_password'];
        try {
            $account->tryChangePassword($checkPassword, $newPassword);
            $account->save($safeVarValues['file_path']);
            return $account;
        } catch (WrongPasswordException $e) {
            return $e;
        }
    }
}