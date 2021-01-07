<?php

namespace Namaskar;



class AssetManager
{

    public function __construct()
    {

    }

    public function dataFinder($method)
    {
        if ($method === 'realpath') {

        }
    }
    public function bundleCss($themeFolder, $site, $url)
    {

        if ($url === 'asset/css/style.css') {
            // $file = realpath($themeFolder . '/asset/css/style.scss');
            // $scssPath = realpath($themeFolder . '/asset/css/');

            $file = realpath($themeFolder . '/asset/scss/style.scss');

            $scssPath = realpath($themeFolder . '/asset/scss/');
            $path2lib = realpath($themeFolder . '/asset/lib');

            $lib2load = $site['lib2load'];
            $libsAssets = $site['libsAssets'];

            $styles = '';
            foreach ($lib2load as $lib) {
                // $stylesScss .= "@import '../lib/$lib';\n";
                $cssAsset = $libsAssets[$lib]['css'] ?? false;
                if ($cssAsset) {
                    if (!is_File("$path2lib/$cssAsset")) {
                        die("$path2lib/$cssAsset Not found");
                    }

                    $content = file_get_contents("$path2lib/$cssAsset");

                    //change relative links since we create one file styles.css files with all files in "lib"
                    $content = str_replace('../', "../lib/$lib/", $content);

                    $styles .= "\n" . $content . "\n";
                }
            }

            $toCompile = file_get_contents($file);

            $scss = new Compiler();
            $scss->setImportPaths($scssPath);
            $styles .= $scss->compile($toCompile);
            //$styles .= scssCompile($scssPath, $toCompile);
            return $styles;

        }
    }

    public function mainScss($themeFolder)
    {
 
            require_once __DIR__.'/vendor/scssphp/scss.inc.php';

            $scss = new \ScssPhp\ScssPhp\Compiler();

            $scss->setImportPaths($themeFolder);
            $scss->setFormatter("\ScssPhp\ScssPhp\Formatter\Crunched");

            header('content-type: text/css');
            // will render for 'assets/stylesheets/main.scss'
            $css =  $scss->compile('@import "main.scss";');
            file_put_contents('style.css',$css);
            echo $css;

         

         
    }    
    public function bundleJs($themeFolder, $site, $url)
    {

        if ($url === 'asset/js/main.js') {

            $file = realpath($themeFolder . '/asset/js/main.js');
            $path2lib = realpath($themeFolder . '/asset/lib');

            $lib2load = $site['lib2load'];
            $libsAssets = $site['libsAssets'];

            $js = '';
            foreach ($lib2load as $lib) {
                // $stylesScss .= "@import '../lib/$lib';\n";
                $jsAsset = $libsAssets[$lib]['js'] ?? false;
                if ($jsAsset) {
                    if (!is_File("$path2lib/$jsAsset")) {
                        die("$path2lib/$jsAsset Not found");
                    }

                    $js .= "\n" . file_get_contents("$path2lib/$jsAsset") . "\n";
                }
            }

            if ($file) {
                $js .= file_get_contents($file);
            }

            return $js;

        }
    }

    public function handleAsset($themeFolder, $site, $url)
    {

        $ini = new Infini($themeFolder);
        //$ini('plop');
        $externals = ['GET' => $_GET, 'POST' => $_POST, 'SERVER' => $_SERVER,
            'domain' => 'defe'];

        $site = $ini->parseFile('asset.ini', $externals);

        if (strpos($url, 'asset/') !== 0) {
            return '';
        }

        if ($url === 'asset/js/main.js') {

            header('Content-Type: application/javascript');
            echo $this->bundleJs($themeFolder, $site, $url);
            exit(0);
        }
        if ($url === 'asset/css/style.css') {
            header('Content-Type: text/css');
            echo $this->bundleCss($themeFolder, $site, $url);
            exit(0);

        }

        $file = realpath($themeFolder . '/' . $url);
        if ($file) {
            $mime_types = array(

                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',

                // images
                'png' => 'image/png',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'ico' => 'image/vnd.microsoft.icon',
                'svg' => 'image/svg+xml',

                //fonts
                'eot' => 'application/vnd.ms-fontobject',
                'svg' => 'application/svg+xml',
                'ttf' => 'application/x-font-truetype',
                'woff' => 'application/font-woff',
                'woff2' => 'application/font-woff2',

            );

            $mime_type = 'text/html';
            $ext = strtolower(array_pop(explode('.', $file)));
            if (array_key_exists($ext, $mime_types)) {
                $mime_type = $mime_types[$ext];
            }
            header('Content-Type: ' . $mime_type);
            //header('Content-Disposition: inline; filename="'.basename($file).'"');
            ob_clean();
            flush();
            \readfile($file);
            return;
        }

        die("FILE not found:" .
            $themeFolder . '/' . $url . " from url=$url");
        exit(0);

    }

}
