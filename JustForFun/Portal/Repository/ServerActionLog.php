<?php


namespace JustForFun\Portal\Repository;


use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Repository;

class ServerActionLog extends Repository
{
    /**
     * @return Finder
     */
    public function findLogsForList()
    {
        return $this->finder('JustForFun\Portal:ServerAction')
            ->with('User')
            ->With("Server")
            ->setDefaultOrder('date', 'DESC');
    }

    public function getUsersInLog()
    {
        return $this->db()->fetchPairs("
			SELECT user.user_id, user.username
			FROM (
				SELECT DISTINCT user_id FROM xf_jff_log_restart
			) AS log
			INNER JOIN xf_user AS user ON (log.user_id = user.user_id)
			ORDER BY user.username
		");
    }

    public function getServerInLog()
    {
        return $this->db()->fetchPairs("SELECT server.server_id, server.ip
			FROM (
				SELECT DISTINCT server_id FROM xf_jff_log_restart
			) AS log
			INNER JOIN xf_jff_cache_servers AS server ON (log.server_id = server.server_id)
			ORDER BY server.ip
			");
    }
}