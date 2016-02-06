<?php
header('Content-Type: text/css; charaset=utf-8');

$strWebFontCSS = "" .
                 "@font-face {\n" .
                 "    font-family: 'utrillo_sub';\n" .
                 "    src: url('http://xn--rssu31gj1g.jp/font/utrillo_sub.woff?v=%(fontupdate)s') format('woff'),\n" .
                 "         url('http://xn--rssu31gj1g.jp/font/utrillo_sub.otf?v=%(fontupdate)s') format('opentype');\n" .
                 "}\n";

// まずは日本語フォント系
$timeFontUpdate = filemtime("./font/utrillo_sub.woff");
$strFontUpdate = date("YmdHi", $timeFontUpdate);

$strWebFontCSS = str_replace("%(fontupdate)s", $strFontUpdate, $strWebFontCSS);
echo($strWebFontCSS);
?>