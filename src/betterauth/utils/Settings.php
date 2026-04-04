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

namespace betterauth\utils;

use betterauth\Loader;
use pocketmine\utils\Config;

class Settings 
{

    const MIN_PASSWORD_LENGTH = 'password.min-length';

    const MAX_PASSWORD_LENGTH = 'password.max-length';

    const CONFIRM_REGISTER_PASSWORD = 'password.register-confirmation';

    const MESSAGE_EVENTS_COOLDOWN = 'block-alert-cooldown';

    const LOGGOUT_ROOM_PREFIX = 'logged-out-room.';

    const LOGGED_OUT_ROOM_ENABLED = self::LOGGOUT_ROOM_PREFIX . 'enabled';

    const LOGGED_OUT_WORLD = self::LOGGED_OUT_ROOM_ENABLED . 'world';

    const LOGGED_OUT_TARGET_WORLD = self::LOGGOUT_ROOM_PREFIX . 'target-world';

    const LOGGET_OUT_ALLOW_MOVE = self::LOGGOUT_ROOM_PREFIX . 'allow-move';

    /** @var Config */
    protected $file;

    public function __construct(
        Config $file
    )
    {
        $this->file = $file;
    }

    public function getFile() : Config
    {
        return $this->file;
    }

    /**
     * @param string $identifier
     * @param boolean $nested
     * @param mixed $default
     * @param boolean $warnConsoleWhenFail
     * @return mixed
     */
    public function getValue(string $identifier, bool $nested = false, $default = null, bool $warnConsoleWhenFail = false)
    {
        $result = $nested ? $this->file->getNested($identifier, null) : $this->file->get($identifier, null);
        if (is_null($result)) {
            if ($warnConsoleWhenFail) {
                Loader::getInstance()->getLogger()->warning("Setting with id $identifier does not found");
            }
            return $default;
        }
        return $result;
    }

    public function getBool(string $identifier, bool $default, bool $nested = false) : bool 
    {
        return (bool) $this->getValue($identifier, $nested, $default);
    }

    public function getInteger(string $identifier, int $default, bool $nested = false) : int 
    {
        return (int) $this->getValue($identifier, $nested, $default);
    }

    public function getString(string $identifier, string $default = '', bool $nested = false) : string 
    {
        return $this->getValue($identifier, $nested, $default);
    }

    public function getMinPasswordLength() : int 
    {
        return $this->getInteger(self::MIN_PASSWORD_LENGTH, 4, true);
    }

    public function getMaxPasswordLength() : int 
    {
        return $this->getInteger(self::MAX_PASSWORD_LENGTH, 25, true);
    }

    public function needToConfirmPassword() : bool 
    {
        return $this->getBool(self::CONFIRM_REGISTER_PASSWORD, true, true);
    }

    public function getBlockEventsMessageCooldown() : int 
    {
        return $this->getInteger(self::MESSAGE_EVENTS_COOLDOWN, 10, false);
    }

}