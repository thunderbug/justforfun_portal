<?php


namespace JustForFun\Portal\Entity;


use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Structure;

class ServerAction extends Entity
{
    public static function getStructure(Structure $structure)
    {
        $structure->table = "xf_jff_log_restart";
        $structure->shortName = "JustForFun\Portal:ServerAction";
        $structure->primaryKey = "action_id";
        $structure->columns = [
            "action_id" => ["type" => self::UINT, "autoIncrement" => true, "nullable" => true],
            "user_id" => ["type" => self::STR, "required" => true],
            "server_id" => ["type" => self::UINT, "required" => true],
            "date" => ["type" => self::UINT, "default" => time()],
            "action" => ["type" => self::UINT, "required" => true],
        ];

        $structure->relations = array(
            "User" => array(
                "entity" => "XF:User",
                "type" => self::TO_ONE,
                "conditions" => "user_id",
                "key" => "primary"
            ),

            "Server" => array(
                "entity" => "Addon/JustForFun:Cache_Servers",
                "type" => self::TO_ONE,
                "conditions" => "server_id",
                "key" => "primary"
            )
        );

        $structure->getters = [];

        return $structure;
    }
}