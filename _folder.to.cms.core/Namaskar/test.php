<?php

use Namaskar\Infini;

$conf = $_POST["conf"] ?? "";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <form action="" method="post">
        <textarea name="conf" id="conf" cols="80" rows="30"><?= $conf ?></textarea>
        <button type="submit">GO</button>
    </form>

</body>

</html>


<?php


if ($conf === "") exit;

require_once "Infini.php";

//TINYML: Thanks It's Not Yaml Markup Language
$tinyml = new Infini(false);
//$tinyml->parseString($conf,[],'config');

debug($tinyml->parseString($conf, [], 'config'));
function debug($var, $name = '')
{

    $fileinfo = 'no_file_info';
    $backtrace = debug_backtrace();
    if (!empty($backtrace[0]) && is_array($backtrace[0])) {
        $fileinfo = $backtrace[0]['file'] . ":" . $backtrace[0]['line'];
    }


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

    $text = "<style> div.code {background-color:#eee}</style>\n<h2>DEBUG $name:(from $fileinfo)</h2><div class='code'>$text</div>";
    echo $text;
}
