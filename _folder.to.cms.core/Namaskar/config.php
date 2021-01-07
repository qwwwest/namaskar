<?php

// SuperAdmin  
$SUPERADMIN =  [
    "name" => "SuperAdmin",
    "login" => "admin",
    "pwd" =>  '$2y$10$0S5.OjO1Hxe3U/RFJ78IsOZXm3VpztaSmgjEulxwy6092zaeJ1SEW',
    "admin" => "secret"
];

define("INDEX_FOLDER", getcwd() . '/');
define("MEDIA_FOLDER", INDEX_FOLDER . '/media/');
define("DATA_FOLDER",  MEDIA_FOLDER . '/_data/');
define("THEME_FOLDER", INDEX_FOLDER . '/asset/');
define("LOG_FILE",     DATA_FOLDER . $USER['domain'] . ".log");
define("MEMPAD_FILE",  DATA_FOLDER . $USER['domain'] . ".lst");

//if MEMPAD_CONF_FILE is defined, we use it as a filename for the ini file
// here, it is DATA_FOLDER/DOMAIN.ini
//define("MEMPAD_CONF_FILE",  DATA_FOLDER . $USER['domain'] . ".ini");

// if MEMPAD_CONF is defined, we fetch that config inside the mempad file
// (pages starting with a dollar are not accessible from the web 
// and are used for internal purposes)
define("MEMPAD_CONF", '$CONF');
