<?php
define('PHP_GUI_VERSION', 'v0.27.7');

require_once 'env.php';

//Dateigroessen die richtige einheit verpassen (groesse in bytes uebergeben)
function sizeformat($bytesize)
{
    $i = 0;
    while (abs($bytesize) >= 1024 && $i < 6) {
        $bytesize /= 1024;
        $i++;
    }
    $bezeichnung = ['Bytes', "KB", "MB", "GB", "TB", "PB", "EB"];
    $newsize = ($i > 0) ? number_format($bytesize, 2) : (int)$bytesize;
    return ("$newsize $bezeichnung[$i]");
}

function writehead($title)
{
    header('Cache-Control: no-cache');
    header('Content-Type: text/html; charset=UTF-8');

    $text = "<!DOCTYPE html>\n"
        . "<html>\n"
        . "<head>\n"
        . "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\" />\n"
        . "<meta http-equiv=\"Cache-Control\" content=\"no-cache\" />\n"
        . "<title>$title</title>\n";
    return $text;
}

function dirlisting($verzeichnis, $endung)
{
    $verzeichnis_liste = [];
    foreach (new DirectoryIterator($verzeichnis) as $fileInfo) {
        if ($endung === $fileInfo->getExtension()) {
            $verzeichnis_liste[$fileInfo->getBasename()] = $fileInfo->getBasename('.' . $endung);
        }
    }

    return $verzeichnis_liste;

}

function getnews($zeit, $version)
{
    if (empty($_SESSION['cache']['NEWS']['LASTTIMESTAMP']))
        $_SESSION['cache']['NEWS']['LASTTIMESTAMP'] = time();
    if (empty($_SESSION['cache']['NEWS']['ITEMS'])
        || ((time() - $_SESSION['cache']['NEWS']['LASTTIMESTAMP']) > ($zeit * 60))) {
        $_SESSION['cache']['NEWS']['LASTTIMESTAMP'] = time();
        $_SESSION['cache']['NEWS']['ITEMS'] = '';
        $news_file = file_get_contents(sprintf($_ENV['NEWS_URL'], $version), false, stream_context_create(['http' => ['ignore_errors' => true]]));

        $_SESSION['cache']['NEWS']['ITEMS'] = strtr($news_file, ["a href=" => "a target=\"_blank\" href=", "<br>" => "<br />"]);
        $_SESSION['cache']['NEWS']['ITEMS'] = preg_replace(
            '/&([^;]*?=)/', '&amp;$1', $_SESSION['cache']['NEWS']['ITEMS']);
        $_SESSION['cache']['NEWS']['ITEMS'] =
            utf8_encode($_SESSION['cache']['NEWS']['ITEMS']);
    }
}

//sortiert alle keys von $srcarray nach den werten von $sortkey eine ebene tiefer
function ajsort($srcarray, $sortkey, $type, $reverse)
{
    foreach (array_keys($srcarray) as $a) {
        $sortarray["$a"] =& $srcarray[$a][$sortkey];
    }
    if (empty($reverse)) {
        asort($sortarray, $type);
    } else {
        arsort($sortarray, $type);
    }
    return ($sortarray);
}

function cutstring($string, $length)
{
    $changed = 0;
    if (function_exists('mb_strlen')) {
        // utf-8 string sauber kuerzen
        if (mb_strlen($string, 'UTF-8') > ($length + 3)) {
            $string_neu = mb_substr($string, 0, $length, 'UTF-8') . "...";
            $changed = 1;
        }
    } elseif (strlen($string) > ($length + 3)) {
        /* wenn mb extension fehlt: nach bytes kuerzen und hoffen kein
        utf-8 zeichen kaputt zu machen */
        $string_neu = substr($string, 0, $length) . "...";
        $changed = 1;
    }
    return (($changed) ? $string_neu : $string);
}

function progressbar($fortschritt, $fertig, $link = "")
{
    $ausgabe = "";
    $balkentext = $fertig . " (" . number_format($fortschritt, 0) . "%)";
    $ausgabe .= "<div style=\"height:12px; width:100px; overflow:hidden;\">";

    if (!empty($link)) $ausgabe .= $link;
    $ausgabe .= "<div style=\"position:relative; top:0px; height:12px; "
        . "width:100px; text-align:center; z-index:1;\">"
        . "<span style=\"font-size:9px; color:#111111;\">"
        . "$balkentext</span></div>";
    if (!empty($link)) $ausgabe .= "</a>";
    $ausgabe .= "<div style=\"width:100px; height:12px; "
        . "background-color:" . $_SESSION['progressbar_bg_color']
        . "; position:relative; top:-12px; z-index:0;\">"
        . "<div style=\"width:" . round($fortschritt, 0)
        . "px; height:12px; background-color:"
        . $_SESSION['progressbar_fg_color'] . ";\"></div></div>";

    $ausgabe .= "</div>";
    return $ausgabe;
}

function debug($var)
{
    echo '<pre>';
    print_r($var);
    die;
}
