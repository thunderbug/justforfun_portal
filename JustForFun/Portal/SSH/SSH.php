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

    private static $colors = array(
            'black', 'red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white',
            '', '',
            'brblack', 'brred', 'brgreen', 'bryellow', 'brblue', 'brmagenta', 'brcyan', 'brwhite',
        );

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
    public static function convert($text)
    {
        // remove cursor movement sequences
        $text = preg_replace('#\e\[(K|s|u|2J|2K|\d+(A|B|C|D|E|F|G|J|K|S|T)|\d+;\d+(H|f))#', '', $text);
        // remove character set sequences
        $text = preg_replace('#\e(\(|\))(A|B|[0-2])#', '', $text);
        $text = htmlspecialchars($text, PHP_VERSION_ID >= 50400 ? ENT_QUOTES | ENT_SUBSTITUTE : ENT_QUOTES, "UTF-8");
        // carriage return
        $text = preg_replace('#^.*\r(?!\n)#m', '', $text);
        $tokens = self::tokenize($text);
        // a backspace remove the previous character but only from a text token
        foreach ($tokens as $i => $token) {
            if ('backspace' == $token[0]) {
                $j = $i;
                while (--$j >= 0) {
                    if ('text' == $tokens[$j][0] && strlen($tokens[$j][1]) > 0) {
                        $tokens[$j][1] = substr($tokens[$j][1], 0, -1);
                        break;
                    }
                }
            }
        }
        $html = '';
        foreach ($tokens as $token) {
            if ('text' == $token[0]) {
                $html .= $token[1];
            } elseif ('color' == $token[0]) {
                $html .= self::convertAnsiToColor($token[1]);
            }
        }

        $html = sprintf('<span style="background-color: %s; color: %s">%s</span>',  "black", "white", $html);

        // remove empty span
        $html = preg_replace('#<span[^>]*></span>#', '', $html);
        $html = str_replace("\n", "<br />", $html);
        return $html;
    }

    private static function tokenize($text)
    {
        $tokens = array();
        preg_match_all("/(?:\e\[(.*?)m|(\x08))/", $text, $matches, PREG_OFFSET_CAPTURE);
        $offset = 0;
        foreach ($matches[0] as $i => $match) {
            if ($match[1] - $offset > 0) {
                $tokens[] = array('text', substr($text, $offset, $match[1] - $offset));
            }
            $tokens[] = array("\x08" == $match[0] ? 'backspace' : 'color', $matches[1][$i][0]);
            $offset = $match[1] + strlen($match[0]);
        }
        if ($offset < strlen($text)) {
            $tokens[] = array('text', substr($text, $offset));
        }
        return $tokens;
    }

    private static function convertAnsiToColor($ansi)
    {
        $bg = 0;
        $fg = 7;
        $as = '';
        if ('0' != $ansi && '' != $ansi) {
            $options = explode(';', $ansi);
            foreach ($options as $option) {
                if ($option >= 30 && $option < 38) {
                    $fg = $option - 30;
                } elseif ($option >= 40 && $option < 48) {
                    $bg = $option - 40;
                } elseif (39 == $option) {
                    $fg = 7;
                } elseif (49 == $option) {
                    $bg = 0;
                }
            }
            // options: bold => 1, underscore => 4, blink => 5, reverse => 7, conceal => 8
            if (in_array(1, $options)) {
                $fg += 10;
                $bg += 10;
            }
            if (in_array(4, $options)) {
                $as = '; text-decoration: underline';
            }
            if (in_array(7, $options)) {
                $tmp = $fg;
                $fg = $bg;
                $bg = $tmp;
            }
        }

        return sprintf('</span><span style="background-color: %s; color: %s%s">', self::$colors[$bg], self::$colors[$fg], $as);
    }

}