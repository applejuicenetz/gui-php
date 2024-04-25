<?php
/**
 * HIER KEINE ÄNDERUNG DURCHFÜHREN!
 * Kopiere die ".env.dist" Datei zu ".env" und konfiguriere dort das GUI!
 */
if (!version_compare(PHP_VERSION, '7.4')) {
    die('PHP 7.4 required, used: -> ' . PHP_VERSION);
}

if (file_exists('.env')) {
    $ini_array = parse_ini_file(".env", true);

$_ENV = array_merge($_ENV, $ini_array);

}

$_ENV['REAL_IP'] = 'http://' . ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

$_ENV['NEWS_URL'] = $_ENV['NEWS_URL'] ?? 'https://applejuicenetz.github.io/news/%s.html';

$_ENV['SERVERLIST_URL'] = $_ENV['SERVERLIST_URL'] ?? 'http://www.applejuicenet.cc/serverlist/xmllist.php';

$_ENV['GUI_REFRESH_STATUS'] = $_ENV['GUI_REFRESH_STATUS'] ?? 10;
$_ENV['GUI_REFRESH_DOWNLOADS'] = $_ENV['GUI_REFRESH_DOWNLOADS'] ?? 30;
$_ENV['GUI_REFRESH_UPLOADS'] = $_ENV['GUI_REFRESH_UPLOADS'] ?? 30;
$_ENV['GUI_REFRESH_SEARCH'] = $_ENV['GUI_REFRESH_SEARCH'] ?? 30;

$_ENV['ALLOWED_SERVERMSG_TAGS'] = $_ENV['ALLOWED_SERVERMSG_TAGS'] ?? '<a><b><i><u><br>';
$_ENV['GUI_SHOW_NEWS'] = $_ENV['GUI_SHOW_NEWS'] ?? 1;
$_ENV['GUI_SHOW_SHARE'] = $_ENV['GUI_SHOW_SHARE'] ?? 1;
$_ENV['TOP_SHOW_PERMALINK'] = $_ENV['TOP_SHOW_PERMALINK'] ?? 1;
$_ENV['REL_INFO'] = $_ENV['REL_INFO'] ?? base64_decode('aHR0cHM6Ly93d3cuYXBwbGUtZGVsdXhlLmNvL2luZGV4LnBocD9jdD00MDMmdmE9JXM=');
 
date_default_timezone_set($_ENV['TZ'] ?? 'Europe/Berlin');

ini_set('error_reporting', $_ENV['PHP_INI_ERROR_REPORTING'] ?? '0');
ini_set('display_errors', $_ENV['PHP_INI_DISPLAY_ERRORS'] ?? 'Off');
