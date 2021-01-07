<?php

namespace Namaskar;

//use ContentTypes\Page;

/**
 * Namaskar
 * This Class retrieves data from the config file, from the mempad files and render the page
 */
class Namaskar
{
    private $config;
    private $mempad;
    private $filename;
    private $renderer;



    public function __construct($mempad)
    {
        $this->config =  new Infini(false);
        $this->mempad = new MemPad($mempad);
        $this->filename = $mempad;
    }

    public function configFile($file)
    {
        $this->config->parseString(file_get_contents($file));
    }

    public function configPage($conf)
    {
        $page = $this->mempad->getContentByPath($conf); //Path such .conf are valid path but not url.
        $this->config->parseString($page);
    }
    public function configString($config)
    {
        $this->config->parseString($config);
    }



    /**
     * renderPage
     * create the object containing all the configuration settings, page content...
     * it is quite opiniated... it handles the main menu, handle languages from url, 
     * invoke the correct page class...  
     * @param  String $url
     * @param  mixed $url
     * @return String rendered page in html
     */
    public function renderPage($url)
    {

        $conf = $this->config;
        $url = trim($url, '/');

        if ($url === '') $url = '/';

        $found = $this->urlManager($url); // is the url found in mempad
        $notFound = substr($url, strlen($found)); //is the rest, to try to find as regular files
        $elt =  $this->mempad->getElementByUrl($found);

        $elt  = $this->mempad->getElementByUrl("/");
        $frontMatters = $this->getFrontmatter($elt->id);
        $parts = explode("/", $found);

        $path = '';

        while ($slug = array_shift($parts)) {

            $path = $path . '/' . $slug;
            $elt  = $this->mempad->getElementByUrl($path);
            $fm = $this->getFrontmatter($elt->id);
            if ($fm && $site = $fm('site')) {
                $conf('site', $site);
            }
        }


        // we build the breadcrump links;
        $home =  $conf('site.modules.breadcrumb.home');
        $parts = explode("/", trim($found, '/'));
        $slug = array_shift($parts);

        $languages = [];
        $languageMenu = $conf('site.menu.language');
        foreach ($languageMenu as $value) {
            $language = $value['url'];
            if ($language) $languages[$language] = $language;
            # code...
        }

        $breadcrumb = [(object) [
            'title' => $home,
            'url' =>  $languages[$slug] ?? ''
        ]];
        $path = '';
        if ($languages[$slug] ?? false) {
            $path = $slug;
            $slug = array_shift($parts);
        }

        while ($slug !== null) {

            $path = $path . '/' . $slug;
            $elt  = $this->mempad->getElementByUrl($path);
            $fm = $this->getFrontmatter($elt->id);
            if ($fm && $site = $fm('site')) {
                $conf('site', $site);
            }
            $elt && $elt->title !== '$HOME' && $breadcrumb[] = $elt; // ?? (object)['title' => $slug, 'slug' => $slug, 'url' => $path];
            $slug = array_shift($parts);
        }


        $language = $conf('site.language');
        $menuLanguage = $conf('site.menu.language');
        if ($menuLanguage) foreach ($menuLanguage as $item) {
            $languageUrl = $item['url'];

            if ($languageUrl && strpos($url, $languageUrl) === 0) {

                $language = $languageUrl;
                break;
            }
        }

        // setting the page type from the url
        $class = $conf('site.type') ?? "Page";
        $types = $conf("site.types.$language");

        if ($types) foreach ($types as $slug => $ctype) {

            if (strpos($found, '/' . $slug . '/') !== false) {
                $class  = $ctype;
                break;
            }
        }

        $this->pagify($elt);
        $conf('page', $elt);

        $conf('page.breadcrumb',  $breadcrumb);
        $conf('page.url', $url);
        $conf('page.urlFound', $found);
        $conf('page.urlNotFound', $notFound);
        $conf('page.urlParts', explode('/', $url));

        $url = $this->config->value('page.urlFound');

        $menu = $conf("site.menu.main");

        foreach ($menu as $key => &$item) {
            $item['active'] = ($url === $item['url']);
            $item['label'] = "plop";
        }

        $folders = [];
        $folders[] = THEME_FOLDER;
        $theme = dirname($this->filename); //theme in 
        $this->renderer = new Renderer($folders, $this->config, $this->mempad);


        return $this->renderer->render()
            . "<!--"
            . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000)
            . "ms -->";
    }


    /**
     * urlManager
     * finds the longuest valid Path in the URL
     * used for meaningful 404 and to mount external files
     * (the not found part will be used in)
     * @param  String $url
     * @return String the longuest valid Path in the URL
     */
    private function urlManager($url)
    {

        if ($this->mempad->getElementByUrl($url)) return $url;

        $parts = explode('/', $url);
        $found = '';
        $pop = array_shift($parts);
        while ($this->mempad->getElementByUrl($found . '/' . $pop) !== null) {
            $found = $found . '/' . $pop;
            $pop = array_shift($parts);
        }

        return trim($found, '/'); //$found;
    }

    function getFrontmatter($id)
    {

        $content = $this->mempad->mpContent[$id];
        if (preg_match('/^[=]{3}\s*\n(.*)\n[=]{3}\s*\n(.*)$/ms', $content, $m)) {
            preg_match('/^[=]{3}\s*\n(.*)\n[=]{3}\s*\n(.*)$/ms', $content, $m);
            $fm = new \Namaskar\Infini(true);
            $fm->parseString($m[1]);
            return $fm;
        }
        return null;
    }


    private function pagify(&$elt)
    {
        $elt->rawPage = $this->mempad->mpContent[$elt->id];
        // frontmatter...
        if (preg_match('/^[\n]*[=]{3}\s*\n.*\n[=]{3}/ms', $elt->rawPage)) {

            preg_match('/^[=]{3}\s*\n(.*)\n[=]{3}\s*\n(.*)$/ms', $elt->rawPage, $m);
            $fm = new \Namaskar\Infini(true);
            $pageFm = $fm->parseString($m[1]) ? $fm->parseString($m[1])['page'] ?? null : null;

            if ($pageFm !== null) {
                $array = is_array($pageFm) ? $pageFm : get_object_vars($pageFm);
                foreach ($array as $prop => $value) {
                    $elt->$prop = $value;
                }
            }
            $elt->rawContent = $m[2];
        } else {

            $elt->rawContent = $elt->rawPage;
        }
    }
}
