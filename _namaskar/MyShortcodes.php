<?php

$this->shortcodes->addShortcode('youtube', function ($attributes, $content, $tagName) {
    $id = $attributes[0];
    $width = $attributes[1] ?? false;
    $height = $attributes[2] ?? false;
    if ($width === false) {
        return <<<HTML
    <div class="video-responsive">
    <iframe width="560" height="315" src="https://www.youtube.com/embed/$id" 
        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
        allowfullscreen></iframe>
    </div>
HTML;
    }

    return <<<HTML
<iframe width="$width" height="$height" src="https://www.youtube.com/embed/$id" 
        frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
        allowfullscreen>
</iframe>
     
HTML;
});



$this->shortcodes->addShortcode('vimeo', function ($attributes, $content, $tagName) {
    $id = $attributes[0];

    $ratio = '16by9';
    $attributes['responsive'] ?? '16by9';


    return <<<HTML

        <div class="embed-responsive embed-responsive-$ratio">
                <iframe src="//player.vimeo.com/video/$id"></iframe>
            </div>
HTML;
});




$this->shortcodes->addShortcode('col', function ($attributes, $content, $tagName) {

    $content = explode("\n||", $content);
    $nb = count($content);

    $cols = '';
    foreach ($content as $key => $value) {
        $value =  $this->shortcodes->process(($value));
        $cols .= <<<COL
<div class="col-sm">
        $value
</div>
COL;
    }

    return <<<HTML
<div class="container">
  <div class="row">
  $cols
  </div>
</div>
HTML;
});

$this->shortcodes->addShortcode('if', function ($attributes, $content, $tagName) {

    $content = explode("{else}", $content);
    $var =  $this->config->value($attributes[0]);
    $test = false;
    if (count($attributes) === 1)
        $test = !!$var;
    if (count($attributes) === 2 && $attributes[0] == '!')
        $test = !$this->ini->value($attributes[1]);
    if (count($attributes) === 3 && $attributes[1] == '==')
        $test = $var ===  $attributes[2];
    if (count($attributes) === 3 && $attributes[1] == '!=')
        $test = $var !==  $attributes[2];
    if (count($attributes) === 3 && $attributes[1] == '>')
        $test = $var >  $attributes[2];
    if (count($attributes) === 3 && $attributes[1] == '<')
        $test = $var <  $attributes[2];
    if ($test) {

        return  $this->shortcodes->process($content[0]);
    } elseif (count($content) == 2) {

        return  $this->shortcodes->process($content[1]);
    }
});

$this->shortcodes->addShortcode('?', function ($attributes, $content, $tagName) {


    $var =  $this->config->value($attributes[0]);
    $test = false;

    if ($var) {
        return  $attributes[1];
    }
    return  $attributes[2] ?? '';
});

$this->shortcodes->addShortcode('meta', function ($attributes, $content, $tagName) {

    $meta = <<<META
    <meta name="description" content="{= page.description}" />
    <meta name="robots" content="index, follow" />
    <meta property="og:title" content="{= page.title}" />
    <meta property="og:description" content="{= page.description}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{= page.url}" />
    <meta property="og:site_name" content="{= site.name}" />
    <meta property="og:image" content="{= page.description}" />
    <meta property="og:locale" content="{= site.language}" />

    <meta name="twitter:card" content="{= page.summary}" />
    <meta name="twitter:title" content="{= page.title}" />
    <meta name="twitter:description" content="{= page.description}" />
    <meta name="twitter:image" content="{= page.description}" />

META;
    return  $this->shortcodes->process($meta);
});


// foreach item in LIST
// foreach file in MASK (ex:foreach file in "logo/*.svg") 
$this->shortcodes->addShortcode('foreach', function ($attributes, $content, $tagName) {

    $html = '';
    $varname =   $attributes[0];
    $op =   $attributes[1];

    if ($op !== 'in') die('foreach syntax is "foreach item/file in list/dir" ');

    $content = trim($content);

    if ($varname === "file") {
        foreach (glob('media/' . $attributes[2] . '/*') as $filename) {

            $file = pathinfo($filename);
            $file['size'] =  filesize($filename);
            $file['url'] =  $filename;
            $html .= preg_replace_callback(
                "|\{= $varname\.(.+?)\}|",
                function ($matches) use ($file) {
                    $tmp =  $file[$matches[1]];
                    return $tmp;
                },

                $content
            );
        }
        if ($html === '') {
            $html = "nothing found :media/" . $attributes[2];
        }
        //  end of folder scanning
        return  $this->shortcodes->process($html);
    }

    $list =  $this->config->value($attributes[2]);

    if (is_array($list))
        foreach ($list as $item) {
            $plop = preg_replace_callback(
                "|\{= $varname\.(.+?)\}|",
                function ($matches) use ($item) {
                    $tmp =  $item[$matches[1]] ?? '';
                    return $tmp;
                },
                $content
            );
            $html .= preg_replace_callback(
                "|\{if $varname\.(.+?)\}|",
                function ($matches) use ($item) {
                    $tmp =  $item[$matches[1]] ?? '0';

                    return "{if $tmp}";
                },

                $plop
            );
        }

    return  $this->shortcodes->process($html);
});


$this->shortcodes->addShortcode('content', function ($attributes, $content, $tagName) {
    static $rec = 0;
    $rec++;

    $ini = $this->config;
    $type = basename($ini('page.template') ?? $ini('page.type'));
    if (!$type) {
        $type = 'Page';
    }

    $template_file =  $this->templates[0] . '/' . strtolower($type) . '.html';
    if ($rec > 10) die('recurtion spotted in [content] ' . $template_file);

    if (is_file($template_file)) {
        $template = file_get_contents($template_file);
        return  $this->shortcodes->process($template);
    }

    die($template_file . ' not found');

    $rec--;
});

$this->shortcodes->addShortcode('include', function ($attributes, $content, $tagName) {
    static $rec = 0;
    $rec++;
    if ($rec > 20) die('recurtion spotted in include ' . $attributes[0]);
    $path = ($this->config)("page.path") . "/" . $attributes[0];

    $content =  $this->mempad->getContentByPath($path);
    return  $this->shortcodes->process($content);
    //return $content;
});

$this->shortcodes->addShortcode('featurette', function ($attributes, $content, $tagName) {
    static $even = true;
    $even = !$even;

    $order1 = $order2 = '';

    if ($even) {

        $order1 = ' order-md-2';
        $order2 = ' order-md-1';
    }
    $title = $attributes['title'];
    $subtitle = $attributes['subtitle'] ?? false;
    $img = $attributes['img'];

    $content = $this->shortcodes->process($content);
    $content = <<<HTML
<div class="row featurette">
    <div class="col-md-7$order1">
      <h2 class="featurette-heading">$title<span class="text-muted">$subtitle</span></h2>
      <p class="lead">$content</p>
    </div>
    <div class="col-md-5$order2">
      <img class="bd-placeholder-img bd-placeholder-img-lg featurette-image img-fluid mx-auto" width="500" height="500" src="media/img/$img" />

    </div>
  </div>
 <hr class="featurette-divider">     
HTML;

    //$content =  $this->mempad->getContentByPath($path);
    return  $this->shortcodes->process($content);
    //return $content;
});

$this->shortcodes->addShortcode('partial', function ($attributes, $content, $tagName) {
    static $rec = 0;
    $rec++;
    if (strpos($attributes[0], '/') !== false) die('partial ' . $attributes[0] . ' not allowed');

    $content = '';
    $theme = ($this->config)("site.theme");
    for ($i = 0; $i < count($this->templates); $i++) {
        $file =  $this->templates[$i] . "/$theme/_" . $attributes[0] . '.html';
        if (is_file($file)) {

            $content = file_get_contents($file);
            break;
        }
    }

    if ($rec > 20) die('recurtion spotted in partial ' . $file);

    if ($content) {

        if (count($attributes) > 1 ?? false) {
            for ($i = 1; $i < count($attributes); $i++) {
                $content = str_replace(
                    '$attributes[' . $i . ']',
                    $attributes[$i],
                    $content
                );
            }
        }
        return ($this->shortcodes->process($content));
    }

    die($file . ' not found');

    $rec--;
});


$this->shortcodes->addShortcode('render', function ($attributes, $content, $tagName) {

    // $path = ($this->config)("page.path");
    $elt = ($this->config)($attributes[0]);

    //$elt = $this->mempad->getElementByPath("$attributes[0]");
    //$elt = $this->mempad->getElementByPath("$attributes[0]");

    //debug($elt);

    return $this->renderBlock($elt);
});

$this->shortcodes->addShortcode('blllock', function ($attributes, $content, $tagName) {
    $blockId = $attributes[0];


    $block = $this->mempad->getElementByPath(".conf/blocks/$blockId");
    if ($block) {
        return  $this->shortcodes->process($block->rawPage);
    }

    die("{block  \"$blockId\"} not found");
});

$this->shortcodes->addShortcode('code', function ($attributes, $content, $tagName) {
    $class = $attributes['class'] ?? ($this->config)('site.modules.code.class'); //microlight
    $class = ($class) ? " class='$class'" : '';
    $content = str_replace('<', '&lt;', $content);

    return "\n<div>\n<code $class>" . trim($content) . "\n</code>\n</div>";
});

$this->shortcodes->addShortcode('quote', function ($attributes, $content, $tagName) {

    $plop = $this->getIdClassStyle($attributes);
    $content = explode("\n- ", trim($content));

    $footer = ($content[1] ?? false)
        ? '<footer class="blockquote-footer"> — ' . $content[1] . '</footer>'
        : '';

    $content = $this->shortcodes->process($content[0]);
    $content = $this->markdownParser->transform($content);

    return "<blockquote $plop>" . trim($content) . $footer . '</blockquote>';
});

$this->shortcodes->addShortcode('#', function ($attributes, $content, $tagName) {
    return '';
});



$this->shortcodes->addShortcode('sidemenu', function ($attributes, $content, $tagName) {
    $url = $attributes[0] ?? '.';
    $level = $attributes[1] ?? -1;

    if ($url == '') $url = '/';
    if ($url == '.') $url = $this->config->value('page.url');
    $menu = $this->mempad->getElementByUrl($url);
    $html = $this->renderSubmenu($menu->children, $level);
    return '<nav class="sidemenu">' . $html . '</nav>';
});
$this->shortcodes->addShortcode('dynmenu', function ($attributes, $content, $tagName) {
    $url = $attributes[0] ?? '.';
    $level = $attributes[1] ?? -1;
    $level = $attributes[1] ?? -1;
    $aclass = $attributes['a.class'] ?? '';
    $liclass = $attributes['li.class'] ?? '';
    $ulclass = $attributes['ul.class'] ?? '';

    if ($url == '') $url = '/';
    if ($url == '.') $url = $this->config->value('page.url');

    $menu = $this->mempad->getElementByUrl($url);
    $html = $this->renderSubmenu($menu->children, $level, true);
    return '<nav class="dynsubmenu">' . $html . '</nav>';
});

$this->shortcodes->addShortcode('breadcrumb', function ($attributes, $content, $tagName) {

    $ini = $this->config;
    $home =  $ini('site.modules.breadcrumb.home');

    $menu = ($this->config)('page.breadcrumb');
    if (!$menu) return '';
    $html = '';
    $nb = count($menu);
    if ($nb === 1) return ''; // no breadcrumb for homepages.
    for ($i = 0; $i < $nb; $i++) {
        $item = &$menu[$i];
        if ($menu[$i]->title ?? false)
            $html .= "<li class=\"breadcrumb-item\"><a href=\"$item->url\">$item->title</a></li>\n";
    }

    $html = <<<HTML
    <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
            $html 
    </ol>
  </nav>
HTML;

    return $html;
});



$this->shortcodes->addShortcode('title', function ($attributes, $content, $tagName) {

    return '<h1>' . $this->config->value('page.title') . '</h1>';
});

$this->shortcodes->addShortcode('=', function ($attributes, $content, $tagName) {
    $value = $attributes[0]; // "/" or ">"
    $ini = $this->config;
    return $ini($value);
});


$this->shortcodes->addShortcode('link', function ($attributes, $content, $tagName) {
    $href = $attributes[0];
    $text = $attributes[1] ?? $href;
    $target = '';
    if (strpos($href, 'http://') !== false  || strpos($href, 'https://') !== false) {
        $target = ' target="_blank"';
    }

    $html = '<a href="' . $href . '"' . $target . '>' . $text . '</a>';
    return $html;
});

$this->shortcodes->addShortcode('img', function ($attributes, $content, $tagName) {
    $path = $attributes[0];
    $alt = $attributes['alt'] ?? $attributes[0];
    $str = $this->uAttr($attributes);

    $title = $alt;

    $html = <<<IMG
<img src="media/img/$path" alt="$alt" title="$alt"$str>
IMG;
    return trim($html);
});

$this->shortcodes->addShortcode(
    'bg',
    function ($attributes, $content, $tagName) {
        $img = $attributes[0];
        $id = '';
        $str = 'background: url(./media/img/' . $img . ') no-repeat fixed;';
        for ($i = 1; $i < count($attributes); $i++) {
            $attr = $attributes[$i];

            if (strpos($attr, '#') === 0) {
                $id = ' id="' . substr($attr, 1) . '"';
            } else
            if (strpos($attr, ':') !== false) {
                $str .= "$attr;";
            } else
            if ($attr === 'cover') {
                $str .= 'background-size: cover;';
            }
        }

        return trim("<div$id class='background' style='$str'></div>");
    }
);


$this->shortcodes->addShortcode('mount', function ($attributes, $content, $tagName) {
    $folder =  getcwd() . '/media/' . $attributes['folder'];
    $file = $this->config->value('page.urlNotFound');
    if ($file === '') $file = $attributes['default'] ?? 'index';

    $regex = '/<body>(.*?)<\/body>/s'; //$attributes['regex'];
    $path = $folder . '/' . $file;
    $content = '';
    //$path = realpath($path . '.m');
    if (is_file($path . '.md')) {
        $content = file_get_contents($path . '.md');
        $content =  $this->shortcodes->process($content);
        $content = $this->markdownParser->transform($content);
    }
    if (is_file($path . '.html')) {
        $content = file_get_contents($path . '.html');
    }
    if (is_file($path . '.xhtml')) {
        $content = file_get_contents($path . '.xhtml');
    }
    //$content = file_get_contents($path);
    if ($content) {
        preg_match($regex, $content, $match);

        $root = $this->config->value('page.urlFound');
        $media = "media";
        //"http" . (!empty($_SERVER['HTTPS']) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . substr($_SERVER['PHP_SELF'], 0, -10);
        $str = $match[1] ?? $content;
        $str = preg_replace('# (href|action) *= *"([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#m', ' $1="' . $root . '/$2$3', $str);
        $str = preg_replace('# (src) *= *"([^:"]*)("|(?:(?:%20|\s|\+)[^"]*"))#m', ' $1="' . "/media/$attributes[folder]" . '/$2$3', $str);


        return "<div>$str</div>";
    }
    return $path . ' not found';
});


$this->shortcodes->addShortcode('mount-manifesto', function ($attributes, $content, $tagName) {
    $file =  getcwd() . '/media/' . $attributes[0] . '/manifest.json';
    $root = getcwd() . '/media/' . $attributes[0];
    $json = json_decode(file_get_contents($file), true);
    $md = $this->renderManifesto($json, $root, 1);

    // replace ```scss hl_lines="5"   => ```scss
    return preg_replace('/( hl_lines=".*?")/m', "\n", $md);
});

$this->shortcodes->addShortcode('toc', function ($attributes, $content, $tagName) {
    return "\n<div class=\"toc\"></div>";
});



$this->shortcodes->addShortcode('lorem', function ($attributes, $content, $tagName) {

    $count = 1;
    $max = 20;
    $std = true;

    $out = '';
    if ($std)
        $out = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, ' .
            'sed do eiusmod tempor incididunt ut labore et dolore magna ' .
            'aliqua.';
    $rnd = explode(
        ' ',
        'a ab ad accusamus adipisci alias aliquam amet animi aperiam ' .
            'architecto asperiores aspernatur assumenda at atque aut beatae ' .
            'blanditiis cillum commodi consequatur corporis corrupti culpa ' .
            'cum cupiditate debitis delectus deleniti deserunt dicta ' .
            'dignissimos distinctio dolor ducimus duis ea eaque earum eius ' .
            'eligendi enim eos error esse est eum eveniet ex excepteur ' .
            'exercitationem expedita explicabo facere facilis fugiat harum ' .
            'hic id illum impedit in incidunt ipsa iste itaque iure iusto ' .
            'laborum laudantium libero magnam maiores maxime minim minus ' .
            'modi molestiae mollitia nam natus necessitatibus nemo neque ' .
            'nesciunt nihil nisi nobis non nostrum nulla numquam occaecati ' .
            'odio officia omnis optio pariatur perferendis perspiciatis ' .
            'placeat porro possimus praesentium proident quae quia quibus ' .
            'quo ratione recusandae reiciendis rem repellat reprehenderit ' .
            'repudiandae rerum saepe sapiente sequi similique sint soluta ' .
            'suscipit tempora tenetur totam ut ullam unde vel veniam vero ' .
            'vitae voluptas'
    );
    $max = $max <= 3 ? 4 : $max;
    for ($i = 0, $add = $count - (int) $std; $i < $add; $i++) {
        shuffle($rnd);
        $words = array_slice($rnd, 0, mt_rand(3, $max));
        $out .= (!$std && $i == 0 ? '' : ' ') . ucfirst(implode(' ', $words)) . '.';
    }
    return $out;
});

$this->shortcodes->addShortcode('date', function ($attributes, $content, $tagName) {
    $format = $attributes[0] ?? "Y-M-d H:i:s";

    return date($format);
});

$this->shortcodes->addShortcode('load-js', function ($attributes, $content, $tagName) {
    //$handle = opendir("asset/js");
    $str = "";

    foreach ($attributes as $file) {
        $str .= '<script src="' . $file . '"></script>';
    }

    return $str;
});



$this->shortcodes->addShortcode('encode', function ($attributes, $content, $tagName) {

    $character_set = "+-.0123456789@ABCDEFGHIJKLMNOPQRSTUVWXYZ_abcdefghijklmnopqrstuvwxyz";
    //$content = ($this->config)($attributes[0]);
    //$email =  strrev($this->ini->value($attributes[0]));

    $this->shortcodes->process($content);
    $content = $attributes[0] ?? strrev($content);


    $key = str_shuffle($character_set);
    $cipher_text = '';
    $id = 'e' . rand(1, 999999999);
    for ($i = 0; $i < strlen($content); $i++) {

        if (strpos($character_set, $content[$i]) === false) {
            $cipher_text .= ($content[$i] === '"') ? '\"' : $content[$i];
        } else {
            $cipher_text .= $key[strpos($character_set, $content[$i])];
        }
    }
    $js = <<<SCR
<span id="$id">[protected by js]</span><script>
    (function (){let a="$key", b, c="$cipher_text", d="";b=a.split("").sort().join("");for(let e=c.length-1;e>-1;e--)
    if(a.indexOf(c.charAt(e) !== false)) d+=b.charAt(a.indexOf(c.charAt(e)));else d+= c.charAt(e);d = d.split("").reverse().join("");
    document.getElementById("$id").innerHTML=d;
    }());
    </script>
SCR;

    return trim($js);
});
