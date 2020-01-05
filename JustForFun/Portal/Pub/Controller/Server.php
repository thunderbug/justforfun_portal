<?php


namespace JustForFun\Portal\Pub\Controller;

use JustForFun\Portal\ServerStatus\StatusRetriever;
use XF\Pub\Controller\AbstractController;

class Server extends AbstractController
{

    public function actionIndex()
    {
        $viewParams = array("servers" => array());

        $finder = \XF::finder("JustForFun\Portal:Cache_Servers");
        $servers_cache = $finder->where("date", ">", time() - 600)->fetch();

        foreach ($servers_cache as $cache) {
            $data = json_decode($cache->get("data"), true);
            $data["lastupdate"] = $cache->get("date");
            $viewParams["servers"][] = $data;
        }

        return $this->view("JustForFun\ServerRestart:View", "justforfun_server_restart_view", $viewParams);
    }
}