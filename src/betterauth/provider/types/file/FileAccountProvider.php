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

use betterauth\provider\AccountProvider;
use betterauth\utils\promise\Promise;
use betterauth\utils\promise\PromiseResolver;

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
    }

    public function getPlayerFilePath(string $username) : string 
    {
        return $this->accountsDir . strtolower($username) . '.json';
    }

    public function isRegistered(string $username): Promise
    {
        $promiseResolver = new PromiseResolver;
        $promiseResolver->resolve(
            file_exists($this->getPlayerFilePath($username))
        );
        return $promiseResolver->getPromise();
    }




}