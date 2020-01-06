<?php

namespace JustForFun\Portal\SSH;

/**
 * Class SSH
 *
 * Setup a ssh connection to a gameserver
 *
 * @package JustForFun\Portal\SSH
 */
class SSH
{
    private $ssh_connection;
    private $gameserver_location;

    /**
     * SSH constructor.
     * @param $serverip string
     * @throws \Exception
     */
    public function __construct($serverip)
    {
        $config = \XF::config();

        $this->ssh_connection = ssh2_connect(
            $config["justforfun"]["servers"][$serverip]["host"],
            $config["justforfun"]["servers"][$serverip]["port"],
            array("hostkey" => "ssh-rsa")
        );

        if($this->ssh_connection) {
            if(!ssh2_auth_pubkey_file(
                $this->ssh_connection,
                $config["justforfun"]["servers"][$serverip]["username"],
                $config["justforfun"]["ssh_public_key"],
                $config["justforfun"]["ssh_private_key"]
            )){
                throw new \Exception("Unable to authenticate SSH");
            }

        } else {
            throw new \Exception("Unable to establish SSH connection");
        }

        $this->gameserver_location = "/home/".$config["justforfun"]["servers"][$serverip]["username"]."/".$config["justforfun"]["servers"][$serverip]["location"];
    }

    /**
     * Exec a command on SSH
     *
     * @param $cmd string
     * @return string
     * @throws \Exception
     */
    public function execute($cmd): ?string
    {
        if($stream = ssh2_exec($this->ssh_connection, $cmd)) {
            stream_set_blocking($stream, true);
            $data = "";
            while ($buf = fread($stream,4096)) {
                $data .= $buf;
            }
            fclose($stream);
        } else {
            throw new \Exception("Failed to exucute command SSH");
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getGameserverLocation(): string
    {
        return $this->gameserver_location;
    }

    /**
     * Replace CLI colors with HTML colors
     * @param $text string
     * @return string|null
     */
    public static function convertCLIcolortoHTML($text): ?string
    {
        $_colorPattern = array(
            '/\\033\[1;33m(.*?)\\033\[0m/s',
            '/\\033\[0;31m(.*?)\\033\[0m/s',
            '/\\033\[0;34m(.*?)\\033\[0m/s',
            '/\\033\[0;36m(.*?)\\033\[0m/s',
            '/\\033\[0;35m(.*?)\\033\[0m/s',
            '/\\033\[0;33m(.*?)\\033\[0m/s',
            '/\\033\[1;37m(.*?)\\033\[0m/s',
            '/\\033\[0;30m(.*?)\\033\[0m/s',
            '/\\033\[0;32m(.*?)\\033\[0m/s'
        );
        $_colorReplace = array(
            '<span class="yellow">$1</span>',
            '<span class="red">$1</span>',
            '<span class="blue">$1</span>',
            '<span class="cyan">$1</span>',
            '<span class="purple">$1</span>',
            '<span class="brown">$1</span>',
            '<span class="white">$1</span>',
            '<span class="black">$1</span>',
            '<span class="green">$1</span>'
        );

        return preg_replace($_colorPattern, $_colorReplace, $text);
    }
}