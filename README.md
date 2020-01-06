# justforfun_portal
Xenforo Portal for http://justforfun-gaming.com

Config.php 
```
//Prefer location outside the public web folder
$config["justforfun"]["ssh_private_key"] = "C:\\xampp\\htdocs\\jff\\key.private";
$config["justforfun"]["ssh_public_key"] = "C:\\xampp\\htdocs\\jff\\key.public";

$config["justforfun"]["servers"] = array(
    "52.58.23.143:28960" => array(
        "host" => "52.58.23.143",
        "port" => 22,
        "username" => "cod5server",
        "location" => "cod5server"
    ),
    "3.123.242.205:28960" => array(
        "host" => "3.123.242.205",
        "port" => 22,
        "username" => "cod4server",
        "location" => "cod4server-2"
    ),
    "18.196.25.40:28962" => array(
        "host" => "18.196.25.40",
        "port" => 22,
        "username" => "cod4server",
        "location" => "cod4server"
    ),
    "18.185.183.228:28960" => array(
        "host" => "18.185.183.228",
        "port" => 22,
        "username" => "cod4server",
        "location" => "cod4server"
    ),
    "18.185.183.228:28961" => array(
        "host" => "18.185.183.228",
        "port" => 22,
        "username" => "cod4server",
        "location" => "cod4server-2"
    )
);
```
