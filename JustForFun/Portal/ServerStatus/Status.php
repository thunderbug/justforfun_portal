<?php
namespace JustForFun\Portal\ServerStatus;

/**
 * Class Status
 *
 * Class containing server information
 *
 * @package JustForFun\Portal\Entity
 */
class Status
{
    private $ip;
    private $port;
    private $gametype;
    private $map;
    private $maxplayers;
    private $players;
    private $hostname;
    private $gamename;

    /**
     * Status constructor.
     * @param $ip string
     * @param $port int
     * @param $gametype string
     * @param $map string
     * @param $maxplayers int
     * @param $players int
     * @param $hostname string
     */
    public function __construct($ip, $port, $gametype, $map, $maxplayers, $players, $hostname, $gamename)
    {
        $this->ip = $ip;
        $this->port = $port;
        $this->gametype = $gametype;
        $this->map = $map;
        $this->maxplayers = $maxplayers;
        $this->players = $players;
        $this->hostname = $hostname;
        $this->gamename = $gamename;
    }

    public function getID()
    {
        return str_replace(".", "_", $this->ip)."-".$this->port;
    }

    /**
     * @return string
     */
    public function getIp() : string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort() : int
    {
        return $this->port;
    }

    /**
     * @return string
     */
    public function getGametype() : string
    {
        return $this->gametype;
    }

    /**
     * @return string
     */
    public function getMap() : string
    {
        return $this->map;
    }

    /**
     * @return int
     */
    public function getMaxplayers() : int
    {
        return $this->maxplayers;
    }

    /**
     * @return int
     */
    public function getPlayers() : int
    {
        return $this->players;
    }

    /**
     * @return string
     */
    public function getHostname(): string
    {
        return $this->hostname;
    }

    public function toArray()
    {
        return array(
            "online" => true,
            "ip" => $this->ip,
            "port" => $this->port,
            "hostname" => $this->hostname,
            "map" => $this->map,
            "gametype" => $this->gametype,
            "players" => $this->players,
            "maxplayers" => $this->maxplayers,
            "gamename" => $this->gamename,
            "ID" => $this->getID()
        );
    }

    /**
     * @return string
     */
    public function getGamename()
    {
        return $this->gamename;
    }
}