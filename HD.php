<?php
header("Content-type: text/html; charset=UTF-8");

session_start();

require("../hd.xn--rssu31gj1g.jp/LIB_pagetree.php");


$urlParamPage = $_GET['page'];
$orgParamPage = $_GET['page'];

if ($_SERVER['HTTP_HOST']=="usr.s602.xrea.com") {
    if (str_contains($_SERVER["REQUEST_URI"], "/hd.xn--rssu31gj1g.jp")) {
        $OldRequest = $_SERVER["REQUEST_URI"];
        $NewRequest = str_replace("/hd.xn--rssu31gj1g.jp", "", $OldRequest);
	    $NewUrl = "https://hd.xn--rssu31gj1g.jp".$NewRequest;
        header( "HTTP/1.1 301 Moved Permanently" );
	    header( "Location: ".$NewUrl);
        exit;
    } 
}


// デフォルトのページ
if ( $urlParamPage == "" ) {
  $urlParamPage = "HD_nobu_top";
}



//-------------- クローラーの判定関数。LazyLoadをしない。 --------------
require("../hd.xn--rssu31gj1g.jp/LIB_crawler.php");

//-------------- シンタックスハイライター系の処理 --------------
// ソースハイライト用。"%(heilight)s"がある時だけ埋め込まれる
$strHilightJS = "";


$strPageFileFullPath = "../hd.xn--rssu31gj1g.jp/".$content_hash[$urlParamPage]['html'];

// コンテンツのページテンプレート読み込み
$strPageTemplate = file_get_contents($strPageFileFullPath);

$strPageDate = date('Y-m-d\TH:i:s', filectime($strPageFileFullPath));
$strCurrentYear = date("Y");

// まずBOMの除去
$strPageTemplate = preg_replace('/^\xEF\xBB\xBF/', '', $strPageTemplate);

// eclipseのオートフォーマッタが変な所で改行するので対応
$strPageTemplate = preg_replace('/<img\s+src/ms', '<img src', $strPageTemplate);

$strPageTemplate = str_replace('%(vs2013runtime)s', 'https://support.microsoft.com/ja-jp/help/2977003/the-latest-supported-visual-c-downloads', $strPageTemplate);
$strPageTemplate = str_replace('%(vs2015runtime)s', 'https://support.microsoft.com/ja-jp/help/2977003/the-latest-supported-visual-c-downloads', $strPageTemplate);
$strPageTemplate = str_replace('%(vs2017runtime)s', 'https://support.microsoft.com/ja-jp/help/2977003/the-latest-supported-visual-c-downloads', $strPageTemplate);
$strPageTemplate = str_replace('%(vs2019runtime)s', 'https://support.microsoft.com/ja-jp/help/2977003/the-latest-supported-visual-c-downloads', $strPageTemplate);
$strPageTemplate = str_replace('%(vs2022runtime)s', 'https://support.microsoft.com/ja-jp/help/2977003/the-latest-supported-visual-c-downloads', $strPageTemplate);

/*
// hrefにタグ
$strPageTemplate = preg_replace_callback("/(<a\s+href=[\"'])([^\"']+?)([\"'])(>)(.+?)(<\/a>)/",
                                   function ($matches) {
                                       // httpが含まれている。が、http://hd. は含まれていない(=外部サイトのリンク)
                                       if ( strpos($matches[0],'http') !== false && strpos($matches[0],'https://hd.') === false ) {
                                           return $matches[1] . $matches[2] . $matches[3] . $matches[4] . $matches[5] . "%(olink)s" . $matches[6];
                                       // 「/」と同じか、「/?page」が含まれている。(=95サイトへのリンクだ)
                                       } else if ($matches[2] == '/' || strpos($matches[0],'/?page') !== false ) {
                                           return $matches[1] . $matches[2] . $matches[3] . $matches[4] . $matches[5] . "%(ilink)s" . $matches[6];

                                       // HD内のリンクだ。
                                       } else {
                                           return $matches[0];
                                       }
                                   }, $strPageTemplate);
*/
// widthやheightが無いイメージタグにマッチしたら、 画像の半分のサイズでwidthやheightを入れる。
$strPageTemplate = preg_replace_callback("/(<img src=[\"'])([^\"']+?)([\"'])(>)/",
                                   function ($matches) {
                                       // httpが含まれている。
                                       if (strpos($matches[0],'http') !== false) {
                                           return $matches[0];

                                       // サイト内の画像
                                       } else {
                                           $strTargetImageFileName = "/virtual/usr/public_html/hd.xn--rssu31gj1g.jp/" . $matches[2];
                                           $timeTargetImageUpdate = filemtime($strTargetImageFileName);
                                           $strTargetImageUpdate = date("YmdHis", $timeTargetImageUpdate);
                                           list($width, $height, $type, $attr) = getimagesize($strTargetImageFileName);
                                           // エッジが無い(=shadowするべきではない場合)
                                           if (strpos($matches[2], "noedge") != false ) {
                                               return $matches[1] . $matches[2] . "?v=". $strTargetImageUpdate . $matches[3] . " width='".(int)($width/2)."' height='".(int)($height/2)."'" . " attr='noedge' " . $matches[4];
                                           // 通常のイメージ(エッジがある)
                                           } else {
                                               return $matches[1] . $matches[2] . "?v=". $strTargetImageUpdate . $matches[3] . " width='".(int)($width/2)."' height='".(int)($height/2)."'" . $matches[4];
                                           }
                                       }
                                   }, $strPageTemplate);


// CardImageのcardImgsrcハッシュにマッチしたら、 ファイル更新時を入れる
$strPageTemplate = preg_replace_callback("/(cardImgSrc\s*:\s*[\"'])([^\"']+?)([\"'])/",
                                   function ($matches) {
                                       // httpが含まれている。
                                       if (strpos($matches[0],'http') !== false) {
                                           return $matches[0];

                                       // サイト内の画像
                                       } else {
                                           $strTargetCardImageFileName = "/virtual/usr/public_html/hd.xn--rssu31gj1g.jp/" . $matches[2];
                                           $timeTargetCardImageUpdate = filemtime($strTargetCardImageFileName);
                                           $strTargetCardImageUpdate = date("YmdHis", $timeTargetCardImageUpdate);
                                           return $matches[1] . $matches[2] . "?v=". $strTargetCardImageUpdate . $matches[3];
                                       }
                                   }, $strPageTemplate);

// cnt_は HD.verからは相対では見えないので、「./cnt_***」⇒「https://hd.xn--rssu31gj1g.jp/cnt_***」というように、hd系をつける
// $strPageTemplate = preg_replace('/"(\.\/)?cnt_/', '"https://hd.xn--rssu31gj1g.jp/cnt_', $strPageTemplate);


// 遅延イメージロード(LazyLoad)用への置き換え処理
$isLazyLoad = false;
$strLazyLoad = "";

// 「クローラー(Bot系)」以外であれば、イメージタグにマッチしたら、LazyLoad風に置き換える。
if ( !isCrawler() && preg_match("/img src=[\"']([^\"']+?)[\"']/", $strPageTemplate ) ) {
  $isLazyLoad = true;
  $strPageTemplate = preg_replace("/img src=[\"']([^\"']+?)[\"']/", "img class='lazy' src='./web_img/dummy.png' data-original='$1'", $strPageTemplate);

  $strLazyLoad = "" . // LazyLoad
                 "<script type='text/javascript' src='./jquery/HD_jquery.lazyload-1.9.7.overlay.min.js?v=%(lazycustomupdate)s'></script>\n" .
                 "<script type='text/javascript'>\n" .
                 "$(document).ready(function(){\n" .
                 "    $('img.lazy').lazyload({ threshold: 200 , effect: 'fadeIn' , effect_speed: 300 });\n" .
                 "});\n" .
                 "</script>";
}

$strShCoreHeader = "";
$strShCoreFooter = "";

// このbrush:があれば、シンタックスハイライトする必要があるページ。上部と下部に必要なCSSやJSを足しこむ
if ( strpos($strPageTemplate, "brush:") != false ) {
    $strShCoreHeader = '<link rel="stylesheet" type="text/css" href="./hilight/styles/HD_shcore-3.0.83.min.css?v=%(shcorecssupdate)s">' . "\n";

    // shcoreのスタイルシート
    $timeShcoreCSSUpdate = filemtime("../hd.xn--rssu31gj1g.jp/hilight/styles/HD_shcore-3.0.83.min.css");
    $strShcoreCSSUpdate = date("YmdHis", $timeShcoreCSSUpdate);

    $strShCoreFooter = "<script type='text/javascript' src='./hilight/scripts/shcore-3.0.83.min.js'></script>\n" .
                       "<script type='text/javascript'>\n" .
                       "SyntaxHighlighter.defaults['toolbar'] = false;\n" .
                       "SyntaxHighlighter.defaults['auto-links'] = false;\n" .
                       "SyntaxHighlighter.all();\n" .
                       "</script>\n";
}
//-------------- シンタックスハイライター系ここまで --------------


//-------------- ホバーカードここから
$strHoverCard = "";
if ( strpos($strPageTemplate, "hovercard") != false ) {
    $strHoverCard = '<script type="text/javascript" src="./jquery/HD_jquery.hovercard-2.4.0.min.js"></script>';
}



//-------------- 単純な単語の置き換え。 --------------
// １つのコンテンツページに関して、各種置き換え
$replaceNameKeys = array("%(hilight)s", "%(home)s", "%(tshd)s", "%(ts95)s", "%(olink)s", "%(ilink)s", "%(environment_os)s", "%(environment_dnet)s", "%(environment_rt)s", "%(environment_rt_warn)s" );
$replaceNameVals = array($strHilightJS, '<i class="fa fa-ts95 fa-fw"></i>サイト', "天翔記HD版", "天翔記95PK版", '<i class="fa fa-external-link fa-fw" id="link_icon"></i>', '<i class="fa fa-internal-link-95 fa-fw" id="link_icon"></i>',
                                        '<tr><td>OS</td><td>Windows10以上</td><td>それ未満のバージョンのOSでも動作する可能性はありますが、<br>対象外となります。</td></tr>',
                                        '<tr><td>.NET</td><td>.NET FrameWork 4.6以上</td><td>(※ Windows10には最初から入っています。)</td></tr>',
                                        '<tr><td>再頒布<br>パッケージ</td><td>VS2015 Runtime x86版<br>Update 3</td><td><a href="https://www.microsoft.com/ja-JP/download/details.aspx?id=48145"><b>Visual Studio 2015 Runtime Update 3</b></a> をインストールしたことがない人は、 <br>インストールしてください。<br></td></tr>',
                                        '<fieldset class="alert alert-warning"><legend>VS2015 Runtime x86版</legend>OSのビット数が64bitか32bitかに関わらずx86版をインストールしてください。<br></fieldset>' );
$strPageTemplate = str_replace($replaceNameKeys, $replaceNameVals, $strPageTemplate);
//-------------- 単純な単語の置き換えここまで。 --------------



//-------------- ダウンロードファイルの日時。 --------------
// ファイルのアーカイブがあれば、更新日時へと置き換え
$fileArchieve = $filetime_hash[$urlParamPage];
if ($fileArchieve) {
   $filetime = filemtime("../hd.xn--rssu31gj1g.jp/".$fileArchieve);
   $fileKeys   = array( "%(file)s", "%(year)04d", "%(mon)02d", "%(mday)02d" );
   $fileValues = array("./".$fileArchieve, date("Y", $filetime), date("m", $filetime), date("d", $filetime) );

  $strPageTemplate = str_replace($fileKeys, $fileValues, $strPageTemplate);
}
//-------------- ダウンロードファイルの日時ここまで。 --------------


//-------------- 今開いているページを、メニュー上で強調するための処理 --------------
// css系を読み込んで、置き換える置き換え
$strStyleTemplate = file_get_contents("../hd.xn--rssu31gj1g.jp/"."HD_style_dynamic.css");
// 今表示しているページへの太字等
$strStyleTemplate = str_replace("%(menu_style_dynamic)s", $urlParamPage, $strStyleTemplate);
//-------------- 今開いているページを、メニュー上で強調するための処理ここまで --------------




// トップページのテンプレートの読み込み
$strIndexTemplate = file_get_contents("../hd.xn--rssu31gj1g.jp/HD_index.html");



//-------------- 左のメニューの部分。すでに開いているページに基いて、階層を開くところを決めるための処理 --------------
// javascriptの一部を書き出す感じ
$strMenuExpand = "";
if ($orgParamPage != "" and $content_hash[$urlParamPage]['dir'] != "#") {
   $strMenuExpand = "$( '#menu' ).multilevelpushmenu( 'expand', '" . $content_hash[$urlParamPage]['dir'] . "' )";
}
//-------------- 左のメニューの部分。すでに開いているページに基いて、階層を開くところを決めるための処理ここまで --------------




//-------------- ファイルの更新に合わせて、必ず再読み込みさせたいもの。XXXXXX?v=%(*****)s系 --------------

// まずは日本語フォント系
$timeFontUpdate = filemtime("../hd.xn--rssu31gj1g.jp/font/utrillo_sub.woff");
$strFontUpdate = date("YmdHis", $timeFontUpdate);

// 日本語フォントはサイズが大きいので、「フォントのキャッシュが存在しないようなら、非同期」で、「フォントのキャッシュが存在するようなら、普通に同期」で表示する。
$strWebFontCSSLink = "";
$strWebFontLoader  = "";

// セッションにフォントの日時が代入されているということは、フォントのキャッシュがすでにある可能性が極めて高い。
// 最初から埋め込みCSSとしてユトリロを表示する。
if ( $_SESSION['FontUpdate'] == $strFontUpdate ) {
/*
    $strWebFontCSSLink = "" .
                         "<style type='text/css'>\n" .
                           "@font-face {\n" .
                           "    font-family: 'utrillo_sub';\n" .
                           "    src: url('./font/utrillo_sub.woff?v=%(fontupdate)s') format('woff'),\n" .
                           "         url('./font/utrillo_sub.otf?v=%(fontupdate)s') format('opentype');\n" .
                           "}\n" .
                           "body { font-family: 'utrillo_sub', serif; }\n" .
                         "</style>";

    $strWebFontLoader = "";
*/

// 多分フォントのキャッシュが無い。非同期で表示。
} else {
/*                         

    // 上部にリンクだけ。この段階では、このリンクの正体は不明
    // フォントローダー。そして、対象のフォントが読み終わったら、実態がphpである webfontloaded.js.ver を実行して、フォントが読み終わったことをPHPセッションとして書き込む
    $strWebFontCSSLink = "" .
                         "<script src='./font/webfontloader-1.6.10.min.js'></script>\n";

    $strWebFontLoader  = "" .
                         "<script type='text/javascript'>\n" .
                         "WebFont.load({\n" .
                         "    custom: { families: ['utrillo_sub'], urls:['./font/LIB_webfontstyle.php?v=%(fontupdate)s'] },\n" .
                         "    fontactive: function(fontFamily, fontDescription) {\n" .  // フォントが読み終わった際のメソッド
                         "    }\n" .
                         "});\n" .
                         "</script>\n" .
                         "<style type='text/css'>\n" .
                         ".wf-loading  body { font-family: serif; }\n" .
                         ".wf-inactive body { font-family: serif; }\n" .
                         ".wf-active   body { font-family: 'utrillo_sub', serif; }\n" .
                         "</style>";
*/
}

$_SESSION['FontUpdate'] = $strFontUpdate;

// メインのスタイルシート
$timeStyleUpdate = filemtime("../hd.xn--rssu31gj1g.jp/HD_style.min.css");
$strStyleUpdate = date("YmdHis", $timeStyleUpdate);

// 独自のFontAwesome
$timeFontPluginUpdate = filemtime("./font-awesome/css/font-awesome-plugin.css");
$strFontPluginUpdate = date("YmdHis", $timeFontPluginUpdate);

// メニューのカスタムCSS
$timeMLPMCSSUpdate = filemtime("../hd.xn--rssu31gj1g.jp/jquery/HD_jquery.multilevelpushmenu.min.css");
$strMLPMCSSUpdate = date("YmdHis", $timeMLPMCSSUpdate);

// メニューのカスタムJS
$timeMLPMCustomUpdate = filemtime("../hd.xn--rssu31gj1g.jp/jquery/HD_jquery.multilevelpushmenu.custom.min.js");
$strMLPMCustomUpdate = date("YmdHis", $timeMLPMCustomUpdate);

// メニューのカスタムJS
$timeLazyCustomUpdate = filemtime("../hd.xn--rssu31gj1g.jp/jquery/HD_jquery.lazyload-1.9.7.overlay.min.js");
$strLazyCustomUpdate = date("YmdHis", $timeLazyCustomUpdate);

// パンくずリストJS
$timeBreadCrumpUpdate = filemtime("jquery/multilevelpushmenu-breadcrump.js");
$strBreadCrumpUpdate = date("YmdHis", $timeBreadCrumpUpdate);


// index内にある、スタイル、コンテンツ、階層の開きをそれぞれ、具体的な文字列へと置き換える
$array_style    = array(
     "%(japanese_webfont_css)s",
     "%(pagedate)s",
     "%(year)s",
     "%(webfont_loader)s",
     "%(style_dynamic)s",
     "%(expand)s",
     "%(fontupdate)s",
     "%(fontpluginupdate)s",
     "%(styleupdate)s",
     "%(mlpmcustomdate)s",
     "%(mlpmcssdate)s",
     "%(shcore_head)s",
     "%(shcore_foot)s",
     "%(shcorecssupdate)s",
     "%(lazy_load)s",
     "%(lazycustomupdate)s",
     "%(hover_card)s",
     "%(breadcrump)s",
     "%(content_dynamic)s" );
$array_template = array(
      $strWebFontCSSLink,
      $strPageDate, 
      $strCurrentYear,
      $strWebFontLoader,
      $strStyleTemplate,
      $strMenuExpand,
      $strFontUpdate,
      $strFontPluginUpdate,
      $strStyleUpdate,
      $strMLPMCustomUpdate,
      $strMLPMCSSUpdate,
      $strShCoreHeader,
      $strShCoreFooter,
      $strShcoreCSSUpdate,
      $strLazyLoad,
      $strLazyCustomUpdate,
      $strHoverCard,
      $strBreadCrumpUpdate,
      $strPageTemplate );
$strIndexEvaluated = str_replace($array_style, $array_template, $strIndexTemplate);

require("../hd.xn--rssu31gj1g.jp/LIB_remain_days.php");

// トップページとして表示
echo($strIndexEvaluated);
?>

