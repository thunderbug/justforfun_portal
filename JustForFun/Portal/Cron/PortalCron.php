<?php
namespace JustForFun\Portal\Cron;

use JustForFun\Portal\ServerStatus\StatusRetriever;

class PortalCron
{
    public static function updateServerList()
    {
        $app = \XF::app();

        $finder = \XF::finder("JustForFun\Portal:Cache_Servers");
        $servers_cache = $finder->where("date", ">", time() - 240)->fetch();

        $servers = explode(";", $app->options()->server_list);

        if(count($servers_cache) != count($servers)) {
            foreach ($servers as $server) {
                $server_expl = explode(":", $server);

                unset($server_cache);
                $finder = \XF::finder("JustForFun\Portal:Cache_Servers");
                $server_cache = $finder->where("ip", "=", $server)->fetch();

                if (count($server_cache) == 0) {
                    $server_cache = \XF::em()->create("JustForFun\Portal:Cache_Servers");
                    $server_cache->set("ip", $server);
                } else {
                    $server_cache = $server_cache->first();
                }

                $status = StatusRetriever::get($server_expl[0], (int)$server_expl[1]);
                if($status == null) {
                    $json = json_decode($server_cache->get("data"), true);
                    $json["online"] = false;
                    $json = json_encode($json);
                } else {
                    $json = json_encode($status->toArray());

                }

                $server_cache->set("data", $json);

                $server_cache->set("date", time());

                $server_cache->save(false);
            }
        }
    }
}