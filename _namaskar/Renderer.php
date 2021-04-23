<?php

namespace Namaskar;

use Shortcodes;

require_once __DIR__ . '/vendor/Michelf/MarkdownExtra.inc.php';

class Renderer
{

    private $templates;
    private $shortcodes;
    private $markdownParser;
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



    public function configPage($conf)
    {
        $page = $this->mempad->getContentByPath($conf); //Path such .conf are valid path but not url.
        $this->config->parseString($page);
    }



    public function render(array $folders, &$config, &$mempad)
    {

        $this->shortcodes = new Shortcodes;

        require_once 'MyShortcodes.php';

        $this->markdownParser = new \Michelf\MarkdownExtra;
        $this->markdownParser->hard_wrap = true;

        $this->contents = [];
        $this->templates = $folders;
        $this->config =  &$config;
        $this->mempad =  &$mempad;

        $conf =  $this->config;

        $content = $conf('page.rawContent');

        //render shortcodes & markdown
        if ($conf('page.urlNotFound') !== '') {
            header("HTTP/1.0 404 Not Found");

            $lang = $conf('page.language');
            $content = $conf('404.' . $lang) . $content;
        };
        $content = $this->shortcodes->process($content);
        $content = $this->markdownParser->transform($content);
        $conf('page.content', trim($content));
        $page = null;
        // $theme = $conf('site.theme') ?? 'default';
        $template =  $conf('page.template')
            ?? $conf('site.template')
            ?? 'index';

        for ($i = 0; $i < count($this->templates); $i++) {
            if (is_file($this->templates[$i] . "/$template.html")) {

                $this->themeFolder = $this->templates[$i] . "/";
                $page = file_get_contents($this->templates[$i] . "/$template.html");
                break;
            }
        }

        if (!$page) die("template not found: $template");
        $page = $this->shortcodes->process($page);


        return $this->absPath($page);
    }





    public function plop($attributes, $content, $tagName)
    {
        $partial = '_' . $tagName . '.html';
        die($partial);
        $plop = $this->getIdClassStyle($attributes, 2);
    }


    public function getIdClassStyle($attributes, $from = 0, $to = -1)
    {

        $id = $class = $style = '';

        if ($to === -1) $to = count($attributes);
        for ($i = $from; $i < $to; $i++) {
            $attr = $attributes[$i];

            if (strpos($attr, '.') === 0) {
                $class .= substr($attr, 1);
            } else
           if (strpos($attr, '#') === 0) {
                $id = substr($attr, 1);
            } else
        if (strpos($attr, ':') !== false) {
                $style .= "$attr;";
            }
        }

        if ($id) $id = ' id="' . $id . '"';
        if ($class) $class = ' class="' . $class . '"';
        if ($style) $style = ' style="' . $style . '"';


        return "$id$class$style";
    }


    public function register($attributes, $content, $tagName)
    {
        $attr = '';
        $uAttr = ['id', 'class', 'title', 'style'];
        foreach ($uAttr as $key => $value) {
            $attr .= ($attributes[$key] ?? false) ? " $key=$attributes[$key]" : '';
        }


        return "\n<div>\n<code $class>" . trim($content) . "\n</code>\n</div>";
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


        $parts = explode("/", $found);

        $path = '';

        //HOME
        if ($found === '' || $found === "/") {
            $elt  = $this->mempad->getElementByPath('$HOME');
            $this->pagify($elt);
        } else // any Page
            while ($slug = array_shift($parts)) {

                $path = $path . '/' . $slug;
                $elt  = $this->mempad->getElementByUrl($path);
                $this->pagify($elt);
            }
        // we build the breadcrump links;
        $home =  $conf('site.modules.breadcrumb.home');
        $parts = explode("/", trim($found, '/'));
        $slug = array_shift($parts);

        $languages = [];
        $languageMenu = $conf('site.menu.language');
        if ($languageMenu)  foreach ($languageMenu as $value) {
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

            // we need to check if title is changed in frontmatter
            $this->pagify($elt);
            $elt && $elt->title !== '$HOME' && $breadcrumb[] = $elt;
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
        // $class = $conf('site.type') ?? "Page";
        // $types = $conf("site.types.$language");

        // if ($types) foreach ($types as $slug => $ctype) {

        //     if (strpos($found, '/' . $slug . '/') !== false) {
        //         $class  = $ctype;
        //         break;
        //     }
        // }

        $this->pagify($elt);
        $conf('page', $elt);


        $conf('page.breadcrumb',  $breadcrumb);
        $conf('page.url', $url);
        $conf('page.urlFound', $found);
        $conf('page.urlNotFound', $notFound);
        $conf('page.urlParts', explode('/', $url));

        $url = $this->config->value('page.urlFound');

        $menu = $conf("site.menu.main");

        $url = trim($url, '/');
        foreach ($menu as $key => &$item) {

            $item['active'] = ($url === $item['url']
                ||
                ($item['url'] !== ''
                    && strpos($url, $item['url']) === 0)) ? 'active' : '';
        }
        $conf("site.menu.main", $menu);
        $folders = [];
        $folders[] = THEME_FOLDER;
        $theme = dirname($this->filename); //theme in 
        return  trim($this->render($folders, $this->config, $this->mempad)
            . "<!--"
            . round((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000)
            . "ms -->");
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



    private function pagify(&$elt)
    {
        if (!$elt) return '';
        $conf = $this->config;
        $elt->rawPage = $this->mempad->mpContent[$elt->id];
        $a = $this->mempad->getPreviousAndNextSibblings($elt->id);
        $elt->previous = $a[0];
        $elt->next = $a[1];

        // frontmatter...
        $a = $this->getFrontmatterAndContent($elt->id);

        $fm = $a[0];
        $elt->frontmatter = null;
        if ($fm && $pageFm = $fm->parsed) {

            $elt->frontmatter = $fm->parsed;

            if ($pageFm !== null) {
                $array = is_array($pageFm) ? $pageFm : get_object_vars($pageFm);
                foreach ($array as $prop => $value) {
                    $elt->$prop = $value;
                }

                if ($array['site'] ?? false) $conf('site', $array['site']);
                if ($array['pages'] ?? false) {
                    $conf('pages', $array['pages']);
                }
                $pages = $conf->value('pages');

                if ($pages)  foreach ($pages as $prop => $value) {
                    $elt->$prop = $value;
                }
                if ($array['page'] ?? false) {
                    foreach ($array['page'] as $prop => $value) {
                        $elt->$prop = $value;
                    }
                }
            }
            $elt->rawContent = $a[1];
            return;
        }

        $pages = $conf->value('pages');
        if ($pages)  foreach ($pages as $prop => $value) {
            $elt->$prop = $value;
        }
        $elt->rawContent = $elt->rawPage;
    }

    /**
     * getFrontmatterAndContent
     *
     * @param  mixed $id
     * @return array of frontMatter and raw content
     */
    function getFrontmatterAndContent($id)
    {
        $content = $this->mempad->mpContent[$id];
        if (preg_match('/^[=]{3}\s*\n(.*)[=]{3}\s*\n(.*)$/ms', $content, $m)) {
            $fm = new Infini(true);
            $fm->parseString($m[1]);
            return [$fm, $m[2]];
        }
        return [null, $content];
    }


    public function renderBlock($elt)
    {

        $content = $this->markdownParser->transform($elt);
        $content = $this->shortcodes->process($content);
        return $content;
    }

    public function renderSubmenu($menu, $level, $isDynamic = false)
    {
        if (!$menu || $level === 0) return '';
        $html = '';
        $url = $this->config->value('page.urlFound');
        foreach ($menu as $key => $item) {
            $classes = [];
            $active = '';
            if ($isDynamic) {
                if (strpos($url, $item->url) === 0) $classes[] = 'dynamic';
            }

            $url = $this->config->value('page.url');
            if ($url === $item->url) $active = 'class="active"';

            $rendered =  $this->renderSubmenu($item->children, $level - 1, $isDynamic);
            if ($rendered && $isDynamic)  $classes[] = 'hasChildren';
            $dyn = $classes ? ' class="' . implode(' ', $classes) . '"' : '';
            $html .= "<li$dyn><a href=\"$item->url\" $active>$item->title</a>"
                . $rendered  . "</li>\n";
        }

        if ($isDynamic) return "<ul class=\"dynamic\">$html</ul>";
        return "<ul>$html</ul>";
    }


    private function uAttr($attributes)
    {
        $str = '';
        if ($attributes['id'] ?? 0)    $str .=  ' id="' . $attributes['id'];
        if ($attributes['class'] ?? 0) $str .=  ' class="' . $attributes['class'] . '"';
        if ($attributes['style'] ?? 0) $str .=  ' style="' . $attributes['style'] . '"';
        if ($attributes['title'] ?? 0) $str .=  ' style="' . $attributes['style'] . '"';
        if ($attributes['lang'] ?? 0)  $str .=  ' lang="' . $attributes['lang'] . '"';
        return trim($str);
    }

    private function absPath($str)
    {

        $root = "http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'], 0, -10);
        //example: <link href="css/main.min.css" rel="stylesheet" />

        $theme = ($this->config)("site.theme");
        //$str = preg_replace('#<link href *= *"css/([^:"]*)#m', "<link href=\"$root/assets/$theme/css/$1", $str);
        //  <script src="js/jquery-3.5.1.min.js"></script>

        $str = preg_replace('# (data|href|src|action) *= *"([^:\#"]*)("|(?:(?:%20|\s|\+)[^"]*"))#m', ' $1="' . $root . '/$2$3', $str);

        return $str;
    }
}
