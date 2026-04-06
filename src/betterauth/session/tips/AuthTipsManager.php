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

namespace betterauth\session\tips;

use betterauth\Loader;
use betterauth\utils\Settings;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;
use SmartCommand\utils\SingletonTrait;

class AuthTipsManager extends PluginTask
{

    use SingletonTrait;

    /** @var boolean */
    private static $enabled = false;

    public static function init(Loader $plugin)
    {
        $instance = new self($plugin);
        self::setInstance($instance);
        self::$enabled = true;
        $plugin->getServer()->getScheduler()->scheduleRepeatingTask($instance, 1);
    }

    public static function isEnabled() : bool 
    {
        return self::$enabled;
    }

    /** @var array<int,AuthTipInstance> */
    protected $tipsBeingSended = [];

    public function addPlayerTip(Player $player, string $tipType)
    {
        $settings = Loader::getInstance()->getSettings();
        $popupText = $settings->getAuthTipTextValue($tipType, Settings::AUTH_TIPS_POPUP_TEXT);

        $popupText = str_replace(
            '{password_confirmation}',
            $settings->needToConfirmPassword() ? '<password_confirmation: string>' : '',
            $popupText
        );

        $tipText = $settings->getAuthTipTextValue($tipType, Settings::AUTH_TIPS_TIP_TEXT);
        $tipText = str_replace(
            '{username}',
            $player->getName(),
            $tipText
        );
        $this->tipsBeingSended[$player->getLoaderId()] = $instance = new AuthTipInstance($player, $tipText, $popupText);
        $instance->sendTips();
    }

    public function removePlayer(Player $player)
    {
        unset($this->tipsBeingSended[$player->getLoaderId()]);
    }

    public function onRun($currentTick)
    {
        foreach ($this->tipsBeingSended as $instance) {
            $instance->tick();
        }
    }

    
}