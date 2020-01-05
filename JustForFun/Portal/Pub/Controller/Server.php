<?php


namespace JustForFun\Portal\Pub\Controller;

use JustForFun\Portal\ServerStatus\StatusRetriever;
use XF\Mvc\ParameterBag;
use XF\Pub\Controller\AbstractController;

class Server extends AbstractController
{

    public function actionIndex()
    {
        $viewParams = array("servers" => array());

        $finder = \XF::finder("JustForFun\Portal:Cache_Servers");
        $servers_cache = $finder->where("date", ">", time() - 3600)->fetch();

        foreach ($servers_cache as $cache) {
            $data = json_decode($cache->get("data"), true);
            $data["lastupdate"] = $cache->get("date");
            $viewParams["servers"][] = $data;
        }

        return $this->view("JustForFun\ServerRestart:View", "justforfun_server_restart_view", $viewParams);
    }

    public function actionStatus(ParameterBag $parameterBag)
    {
        $server = $parameterBag->get("server");
        $server_ip = str_replace(array("_", "-"), array(".", ":"), $server);

        return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip));
    }
}