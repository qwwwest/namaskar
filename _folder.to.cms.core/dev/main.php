<?php
include_once __DIR__ . '/scssphp/scss.inc.php';

// example: buildCss(__DIR__ . "/scss/main.scss",  __DIR__);


$basename = basename(__FILE__, '.php');

buildCss(__DIR__ . "/scss/$basename.scss",  __DIR__, $basename);




/**
 *
 * Convert a sass folder to an expanded and a crushed css files
 *
 * @param    string  $mainScssFile  the main scss file
 * @param    string  $cssFolder  the main scss file
 * @param    string  $cssFilename (optional) the output filename (default: "style")
 *  
 *
 */
function buildCss($mainScssFile, $cssFolder, $cssFilename = null)
{

    $pathinfo = pathinfo($mainScssFile);
    $filename = $pathinfo['filename'];

    if ($cssFilename === null) {
        $cssFilename = "style";
    }

    $scss = new \ScssPhp\ScssPhp\Compiler();
    $scss->setImportPaths($pathinfo['dirname']);

    //  die(realpath($pathinfo['dirname']));
    $scss->setFormatter("\ScssPhp\ScssPhp\Formatter\Crunched");
    $css =  $scss->compile("@import \"$filename.scss\"");
    file_put_contents("$cssFolder/$cssFilename.min.css", $css);

    $len = round(strlen($css) / 1024);

    $scss->setFormatter("\ScssPhp\ScssPhp\Formatter\Expanded");
    $css =  $scss->compile("@import \"$filename.scss\"");
    file_put_contents("$cssFolder/$cssFilename.css", $css);

    header('content-type: text/css');
    echo $css;

    $time = round(1000 * (microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]));
    echo "/* Did it in $time milliseconds $len kb minified */\n";
}
