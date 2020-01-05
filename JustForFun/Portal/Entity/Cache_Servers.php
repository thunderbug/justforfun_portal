<?php
namespace JustForFun\Portal\Entity;

use XF\Mvc\Entity\Structure;

class Cache_Servers extends \XF\Mvc\Entity\Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = "xf_jff_cache_servers";
        $structure->shortName = "JustForFun\Portal:Cache_Servers";
        $structure->primaryKey = "server_id";
        $structure->columns = [
            "server_id" => ["type" => self::UINT, "autoIncrement" => true, "nullable" => true],
            "ip" => ["type" => self::STR, "required" => true],
            "data" => ["type" => self::STR, "required" => true],
            "date" => ["type" => self::UINT, "default" => time()],
        ];

        $structure->getters = [];

        return $structure;
    }
}