<?php
namespace JustForFun\Portal\ServerStatus;

/**
 * Class StatusRetriever
 *
 * Status retriever and some other quake colors parsers
 *
 * @package JustForFun\Portal\ServerStatus
 */
class StatusRetriever
{
    /**
     * @var array Colors Array
     */
    private static $Colors = array(
        0 => "#000000",		1 => "#ff0000",		2 => "#00ff00",		3 => "#dfd200",
        4 => "#0000ff",		5 => "#00ffff",		6 => "#ff00ff",		7 => "#ffffff",
        8 => "#ff7f00",		9 => "#7f7f7f",		10 => "#bfbfbf",	11 => "#bfbfbf",
        12 => "#007f00",	13 => "#7f7f00",	14 => "#00007f",	15 => "#7f0000",
        16 => "#7f3f00",	17 => "#ff9919",	18 => "#007f7f",	19 => "#7f007f",
        20 => "#007fff",	21 => "#7f00ff",	22 => "#3399cc",	23 => "#ccffcc",
        24 => "#006633",	25 => "#ff0033",	26 => "#b21919",	27 => "#993300",
        28 => "#cc9933",	29 => "#999933",	30 => "#ffffbf",	31 => "#ffff7f");

    /**
     * Get Server Status
     * @param $ip string
     * @param $port int
     * @return Status
     */
    public static function get($ip, $port) : Status
    {
        $session = @fsockopen("udp://".$ip, $port, $errno, $errstr, 30);

        if($session) {
            socket_set_timeout($session, 1, 0);
            stream_set_blocking($session, true);
            stream_set_timeout($session, 2);

            fputs($session, "\xFF\xFF\xFF\xFFgetstatus\x00");
            $recv = fread($session, 5000);

            if (!empty($recv)) {
                do {
                    $spr = socket_get_status($session);
                    $recv = $recv . fread($session, 5000);
                    $sps = socket_get_status($session);
                } while ($spr['unread_bytes'] != $sps['unread_bytes']);

                $recv = str_replace("ÿÿÿÿstatusResponse", "", $recv);
                $recv = explode("\\", $recv);

                $i = array_search("g_gametype", $recv) + 1;
                $gametype = $recv[$i];

                $i = array_search("mapname", $recv) + 1;
                $map = $recv[$i];

                $i = array_search("sv_maxclients", $recv) + 1;
                $maxplayers = (int)

                self::removecolortags($recv[$i]);

                $i = array_search("sv_hostname", $recv) + 1;
                $hostname = $recv[$i];

                $i = array_search("gamename", $recv) + 1;
                $gamename = $recv[$i];


                $last = count($recv) - 1;
                $players = explode('"', $recv[$last]);
                $players_amount = (count($players) - 1) / 2;

                return new Status($ip, $port, $gametype, $map, $maxplayers, $players_amount, self::colorize($hostname), $gamename);
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    /**
     * Remove Color q3 tags
     * @param $Text String Text
     * @return String Text without color tags
     */
    public static function RemoveColorTags($Text) {
        $Text = str_replace("'", "", $Text);
        return preg_replace('|\^.|', "", $Text);
    }

    /**
     * Replace Color q3 tags to HTML Colors
     * @param $Text String Text
     * @return string
     */
    public static function colorize($Text){
        $CurrentColor = -1;
        $NextColor = 7;

        $Buffer = "";
        for ($x = 0; $x < strlen($Text); $x++) {
            if ($Text[$x] == '^' && $Text[$x + 1] != '^') {
                $NextColor = (ord($Text[$x + 1]) + 16) & 31;
                $x++;
                continue;
            }
            if ($CurrentColor != $NextColor) {
                if ($CurrentColor != -1)
                    $Buffer .= "</span>";
                $CurrentColor = $NextColor;
                $Buffer .= sprintf("<span style=\"color: %s; \">", self::$Colors[$CurrentColor]);

            }
            $Buffer .= htmlspecialchars($Text[$x]);
        }

        if ($CurrentColor != -1)
            $Buffer .= "</font>";

        return $Buffer;
    }
}