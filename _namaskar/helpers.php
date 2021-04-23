<?php





//include rendered php file as text
function fuckinc($f)
{
    if (file_exists($f)) {
        ob_start();
        include $f;
        return ob_get_clean();
    }

    return "  <!-- $f not found --> ";
}


function htmlpath($relative_path)
{
    $realpath = realpath($relative_path);
    $htmlpath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $realpath);
    return $htmlpath;
}

function debug($var, $name = '')

{

    ini_set("highlight.comment", "#008000");
    ini_set("highlight.default", "#000000");
    ini_set("highlight.html", "#808080");
    ini_set("highlight.keyword", "#0000BB; font-weight: bold");
    ini_set("highlight.string", "#AA0000");

    $text = preg_replace("|^array *\((.*),\n\)$|s", '[$1]', var_export($var, 1));

    $text = preg_replace("| =>\s*\n\s*array \(|", " = [", $text);;
    $text = preg_replace("|,(\n *)\),|", "\$1\n]", $text);

    // highlight_string() requires opening PHP tag or otherwise it will not colorize the text
    $text = highlight_string("<?php " . $text, true);
    $text = preg_replace_callback(
        '|<br />((&nbsp;)+)</span>|',
        function ($matches) {
            $tmp = '</span><br />' . preg_replace("|&nbsp;&nbsp;|", ".&nbsp;", $matches[1]);
            return $tmp;
        },
        $text
    );
    $text = preg_replace("|&lt;\?php&nbsp;|", "", $text, 1);
    $text = preg_replace('|=&gt;&nbsp;<br />&nbsp;&nbsp;array&nbsp;\(<br />|', " = [", $text, 1);

    $text = "<style> div.code {background-color:#eee}</style>\n<h2>DEBUG $name:</h2><div class='code'>$text</div>";
    echo $text;
}
