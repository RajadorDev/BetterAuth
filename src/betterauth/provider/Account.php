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

use betterauth\utils\DynamicObject;
use betterauth\utils\PlayerPropertyDynamicObject;

class Account extends PlayerPropertyDynamicObject
{

    const DATA_ADDRESS = 'address';

    const DATA_PASSWORD = 'password';

    const DATA_CLIENT_ID = 'clientId';

    const DATA_LAST_LOGIN = 'lastLogin';

    const DATA_CREATION = 'creation';

    /** @var string */
    protected $address;

    /** @var string */
    private $passwordEncrypted;

    /** @var float */
    protected $clientId, $lastLoginAt, $createdAt;

    /**
     * @param string $username
     * @param string $passwordRaw
     * @param string $address
     * @param float $clientId
     * @return Account
     */
    public static function create(string $username, string $passwordRaw, string $address, float $clientId) : Account
    {
        $now = microtime(true);
        return new self(
            $username,
            static::encryptPassword($passwordRaw),
            $address,
            $clientId,
            $now,
            $now
        );
    }

    /**
     * @param string $username
     * @param string $passwordEncrypted
     * @param string $address
     * @param float $clientId
     * @param float $lastLoginAt
     * @param float $createdAt
     */
    public function __construct(
        string $username,
        string $passwordEncrypted,
        string $address,
        float $clientId,
        float $lastLoginAt,
        float $createdAt
    )
    {
        $this->address = $address;
        $this->passwordEncrypted = $passwordEncrypted;
        $this->clientId = $clientId;
        $this->lastLoginAt = $lastLoginAt;
        $this->createdAt = $createdAt;
        return parent::__construct($username);
    }

    public static function encryptPassword(string $rawPassword) : string 
    {
        return hash('sha256', $rawPassword);
    }

    public function getAddress() : string 
    {
        return $this->address;
    }

    public function getClientId() : float 
    {
        return $this->clientId;
    }

    public function getLastLoginTimestamp() : float 
    {
        return $this->lastLoginAt;
    }

    public function getCreationTimestamp() : float 
    {
        return $this->createdAt;
    }

    public function matchPassword(string $passwordRaw) : bool 
    {
        return static::encryptPassword($passwordRaw) === $this->passwordEncrypted;
    }

    public function save(string $path)
    {
        file_put_contents(
            $path,
            json_encode($this)
        );
    }

    protected function serializeExtraData(): array
    {
        return [
            self::DATA_PASSWORD => $this->passwordEncrypted,
            self::DATA_ADDRESS => $this->address,
            self::DATA_CLIENT_ID => $this->clientId,
            self::DATA_CREATION => $this->createdAt,
            self::DATA_LAST_LOGIN => $this->lastLoginAt
        ];
    }

    public static function unserialize(array $data): DynamicObject
    {
        return new self(
            self::usernameFromData($data),
            $data[self::DATA_PASSWORD],
            $data[self::DATA_ADDRESS],
            $data[self::DATA_CLIENT_ID],
            $data[self::DATA_LAST_LOGIN],
            $data[self::DATA_CREATION]
        );
    }


}