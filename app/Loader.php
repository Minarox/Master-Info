<?php
declare(strict_types = 1);

namespace app;

/**
 * Load modules
 */
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../app/Database.php";
require_once __DIR__ . "/../app/oauth2/OAuth2.php";

/**
 * List of folders to browse
 */
$folders = array(
    "/exceptions/",
    "/../src/",
    "/../src/Codes/",
    "/../src/Controllers/"
);

/**
 * Load the .php files found in the folders
 */
foreach ($folders as $folder) {
    $files = scandir(__DIR__ . $folder);
    for ($i = 2; $i < count($files); $i++) {
        if (str_ends_with($files[$i], ".php")) {
            require_once __DIR__ . $folder . $files[$i];
        }
    }
}

/**
 * Load parameters from config file
 */
define("CONFIG", parse_ini_file(__DIR__ . "/../config.ini", true));