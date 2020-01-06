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
        $server = $parameterBag->get("server");
        $server_ip = str_replace(array("_", "-"), array(".", ":"), $server);

        if(\XF::visitor()->getPermissionSet()->hasGlobalPermission("justforfunPortal", "canstatusserver")) {
            $ssh = new SSH($server_ip);
            $status = SSH::convert($ssh->execute($ssh->getGameserverLocation() . " monitor"));

            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => $status));
        } else {
            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => "Invalid Permission"));
        }
    }

    public function actionRestart(ParameterBag $parameterBag)
    {
        $server = $parameterBag->get("server");
        $server_ip = str_replace(array("_", "-"), array(".", ":"), $server);

        if(\XF::visitor()->getPermissionSet()->hasGlobalPermission("justforfunPortal", "canrestartserver")) {
            $ssh = new SSH($server_ip);
            $status = SSH::convert($ssh->execute($ssh->getGameserverLocation() . " restart"));

            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => $status));
        } else {
            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => "Invalid Permission"));
        }
    }

    public function actionStop(ParameterBag $parameterBag)
    {
        $server = $parameterBag->get("server");
        $server_ip = str_replace(array("_", "-"), array(".", ":"), $server);

        if(\XF::visitor()->getPermissionSet()->hasGlobalPermission("justforfunPortal", "canstartstopserver")) {
            $ssh = new SSH($server_ip);
            $status = SSH::convert($ssh->execute($ssh->getGameserverLocation() . " stop"));

            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => $status));
        } else {
            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => "Invalid Permission"));
        }
    }

    public function actionStart(ParameterBag $parameterBag)
    {
        $server = $parameterBag->get("server");
        $server_ip = str_replace(array("_", "-"), array(".", ":"), $server);

        if(\XF::visitor()->getPermissionSet()->hasGlobalPermission("justforfunPortal", "canstartstopserver")) {
            $ssh = new SSH($server_ip);
            $status = SSH::convert($ssh->execute($ssh->getGameserverLocation() . " start"));

            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => $status));
        } else {
            return $this->view("JustForFun\ServerRestart:Status", "justforfun_server_status", array("server" => $server_ip, "status" => "Invalid Permission"));
        }
    }
}