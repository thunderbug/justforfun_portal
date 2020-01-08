<?php


namespace JustForFun\Portal\Pub\Controller;

use JustForFun\Portal\SSH\SSH;
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
        return $this->doSSHaction($parameterBag->get("server"), "monitor", "canstatusserver");
    }

    public function actionRestart(ParameterBag $parameterBag)
    {
        return $this->doSSHaction($parameterBag->get("server"),"restart", "canrestartserver");
    }

    public function actionStop(ParameterBag $parameterBag)
    {
        return $this->doSSHaction($parameterBag->get("server"),"stop", "canstartstopserver");
    }

    public function actionStart(ParameterBag $parameterBag)
    {
        return $this->doSSHaction($parameterBag->get("server"),"start", "canstartstopserver");
    }

    /**
     * @param $server string Server IP
     * @param $action string Action
     * @param $permission string Permission ID
     * @return \XF\Mvc\Reply\View
     * @throws \XF\PrintableException
     */
    private function doSSHaction($server, $action, $permission)
    {
        $server_ip = str_replace(array("_", "-"), array(".", ":"), $server);

        $finder = \XF::finder("JustForFun\Portal:Cache_Servers");
        $server_cache = $finder->where("ip", "=", $server_ip)->fetch();
        $server_cache = $server_cache->first();

        if(\XF::visitor()->getPermissionSet()->hasGlobalPermission("justforfunPortal", $permission)) {
            $serverAction = \XF::em()->create("JustForFun\Portal:ServerAction");
            $serverAction->set("user_id", \XF::visitor()->get("user_id"));
            $serverAction->set("server_id", $server_cache->get("server_id"));
            $serverAction->set("action", $action);
            $serverAction->save();

            $ssh = new SSH($server_ip);
            $status = SSH::convert($ssh->execute($ssh->getGameserverLocation() . " ".$action));

            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => $status));
        } else {
            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => "Invalid Permission"));
        }
    }
}