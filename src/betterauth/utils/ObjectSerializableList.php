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

use JsonSerializable;
use pocketmine\utils\Config;

abstract class ObjectSerializableList implements JsonSerializable 
{

    /** @var Config */
    private $file;

    public function __construct(string $filePath, int $fileType = Config::JSON)
    {
        $this->file = new Config($filePath, $fileType);
        foreach ($this->file->getAll() as $data)
        {
            $this->load($data);
        }
    }

    protected function load(array $data)
    {
        $source = $data[DynamicObject::SOURCE_ID];
        $this->onLoad($source::unserialize($data));
    }

    abstract protected function onLoad(DynamicObject $obj);

    /** @return DynamicObject[] */
    abstract public function getObjectList() : array;

    public function save(bool $async = false)
    {
        $list = $this->jsonSerialize();
        $this->file->setAll($list);
        $this->file->save($async);
    }

    public function jsonSerialize()
    {
        return array_map(
            static function (DynamicObject $obj) : array {
                return $obj->jsonSerialize();
            }, array_values($this->getObjectList())
        );
    }

    
}