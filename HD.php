<?php
header("Content-type: text/html; charset=UTF-8");

session_start();

require("LIB_pagetree.php");

$punnyAddress = "hd.xn--rssu31gj1g.jp";

$defaultHomePage = "HD_nobu_top";

$indexFileName = "HD_index.html";

$strTitle = "天翔記.jp (HD版)";

$urlParamPage = $_GET['page'];
$orgParamPage = $_GET['page'];

/*
if ($_SERVER['HTTP_HOST'] == "usr.s602.xrea.com") {
    if (str_contains($_SERVER["REQUEST_URI"], "/" . $punnyAddress)) {
        $OldRequest = $_SERVER["REQUEST_URI"];
        $NewRequest = str_replace("/".$punnyAddress, "", $OldRequest);
        $NewUrl = "https://" . $punnyAddress . $NewRequest;
        header("HTTP/1.1 301 Moved Permanently");
        header("Location: " . $NewUrl);
        exit;
    }
}
*/

// rel=canonical の設定
if ( $urlParamPage != "" ) {

    $strCanonicalKey = $urlParamPage;
    // 一番最後の2があるキーは、2が無いものとhtmlを比べて、同じhtmlを示しているならば、
    // 2が無い方が正規のページ。これはgoogle検索で「ページが重複している」といった判定になるのを避けるため。

	// "2"で終わっているか判定
	if (substr($urlParamPage, -1) === "2") {
	  // "2"を取り除いた文字列
	  $urlParamPageWithout2 = substr($urlParamPage, 0, -1);

	  $html2 = $content_hash[$urlParamPage]['html'];          // 2がある方のHTML
	  $html1 = $content_hash[$urlParamPageWithout2]['html'];  // 2が無い方のHTML

	  // 両方有効なhtmlをもっており、しかも同じならば
	  if (isset($html1) && isset($html2) && ($html1 == $html2)) {
          // html1(2というのが無い方)を採用する。
          $strCanonicalKey = $urlParamPageWithout2;
	  }
	}


    $strCanonical = "https://" . $punnyAddress . "/?page=" . $strCanonicalKey;
} else {
    $strCanonical = "https://" . $punnyAddress . "/";
}



// デフォルトのページ
if ( $urlParamPage == "" ) {
    $urlParamPage = $defaultHomePage;
}



//-------------- クローラーの判定関数。LazyLoadをしない。 --------------
require("LIB_crawler.php");



$strMenuTemplate = file_get_contents("menu.html");
// まずBOMの除去
$strMenuTemplate = preg_replace('/^\xEF\xBB\xBF/', '', $strMenuTemplate);


$strPageFileFullPath = $content_hash[$urlParamPage]['html'];

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



// widthやheightが無いイメージタグにマッチしたら、 画像の半分のサイズでwidthやheightを入れる。
$strPageTemplate = preg_replace_callback(
   "/(<img src=[\"'])([^\"']+?)([\"'])(>)/",
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
$strPageTemplate = preg_replace_callback(
   "/(cardImgSrc\s*:\s*[\"'])([^\"']+?)([\"'])/",
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



$strShCoreHeader = "";
$strShCoreFooter = "";

// このbrush:があれば、シンタックスハイライトする必要があるページ。上部と下部に必要なCSSやJSを足しこむ
if ( strpos($strPageTemplate, "brush:") != false ) {
    $strShCoreHeader = '<link rel="stylesheet" type="text/css" href="./hilight/styles/HD_shcore-3.0.83.min.css?v=%(shcorecssupdate)s">' . "\n";

    // shcoreのスタイルシート
    $timeShcoreCSSUpdate = filemtime("./hilight/styles/HD_shcore-3.0.83.min.css");
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


//-------------- シンタックスハイライター系の処理 --------------
// ソースハイライト用。"%(heilight)s"がある時だけ埋め込まれる
$strHilightJS = "";

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
   $filetime = filemtime($fileArchieve);
   $fileKeys   = array( "%(file)s", "%(year)04d", "%(mon)02d", "%(mday)02d" );
   $fileValues = array("./".$fileArchieve, date("Y", $filetime), date("m", $filetime), date("d", $filetime) );

  $strPageTemplate = str_replace($fileKeys, $fileValues, $strPageTemplate);
}
//-------------- ダウンロードファイルの日時ここまで。 --------------


//-------------- 今開いているページを、メニュー上で強調するための処理 --------------
// css系を読み込んで、置き換える置き換え
$strStyleTemplate = file_get_contents("HD_style_dynamic.css");
// 今表示しているページへの太字等
$strStyleTemplate = str_replace("%(menu_style_dynamic)s", $urlParamPage, $strStyleTemplate);
//-------------- 今開いているページを、メニュー上で強調するための処理ここまで --------------




// トップページのテンプレートの読み込み
$strIndexTemplate = file_get_contents($indexFileName );




//--------------------------------------------------------
// 以下、indexファイルから、「前のページ」と「後のページ」を作成する
function strip_html_comment($str)
{
    $str = preg_replace("/<\!--.*?-->/sm", '', $str); //comments
    return $str;
}

$explode_text = explode( "\n", strip_html_comment( $strMenuTemplate ) );

$explode_text_line_count = count( $explode_text );
// 格納用の配列
$explode_array = array();
for( $i=0; $i<$explode_text_line_count; $i++ ) {
    preg_match("/id=\"(HD_nobu_.+?)\"/", $explode_text[$i], $explode_line_match);
    if ($explode_line_match) {
        if ( array_key_exists( $explode_line_match[1], $content_hash ) ) {
            if ( count($explode_array) > 0 ) {
                // すでに要素があるなら、付けたし
                if ($explode_line_match[1] != $explode_array[0]) {
                    $content_hash[ $explode_line_match[1] ]["prev"] = $explode_array[0];
                }
            }

            array_unshift( $explode_array, $explode_line_match[1]);
        }
    }
}

$current_page_prev = "";
$current_page_next = "";

$array_content_style = array();
$array_content_template = array();

foreach ($content_hash as $content_key => $content_value) {
    array_push($array_content_style, "%(" . $content_key . ")s");
    // 今のページと同じで、"prev" がある場合、
    if ($content_key == $urlParamPage && array_key_exists( "prev", $content_value ) ) {
        $current_page_prev = $content_value["prev"];

    // 今表示しているページとは異なるキーが、prevとして今のページを指定しているということは、
    // そのキーは、現在表示しているページのnextである。 
    } else if ( array_key_exists( "prev", $content_value ) && $content_value["prev"] == $urlParamPage) {
        $current_page_next = $content_key;
    }
}


if ($current_page_prev != "") {
    $current_page_prev = '<li class="page-item"><a class="page-link" href="?page=' .$current_page_prev. '"><i class="fa fa-caret-left fa-fw"></i>前へ</a></li>';
}

if ($current_page_next != "") {
    $current_page_next = '<li class="page-item"><a class="page-link" href="?page=' .$current_page_next. '">次へ<i class="fa fa-caret-right fa-fw"></i></a></li>';
}

if ($current_page_prev != "" || $current_page_next != "") {
    $footer_control_page = "\n" . '<div class="content-box mb-3 content-lighten"><ul class="pagination justify-content-center" style="margin:0px">%(prev)s %(next)s</ul></div>' . "\n";
    $strPageTemplate = $footer_control_page . $strPageTemplate . $footer_control_page;
}


// ページ内の上下に「前へ」と「次へ」を付け加える。
$array_style    = array( 
    "%(prev)s",
    "%(next)s" );
$array_template = array( 
    $current_page_prev, 
    $current_page_next );
$strPageTemplate = str_replace($array_style, $array_template, $strPageTemplate);
// 以上、作成でした
//--------------------------------------------------------





//-------------- 左のメニューの部分。すでに開いているページに基いて、階層を開くところを決めるための処理 --------------
// javascriptの一部を書き出す感じ
$strMenuExpand = "";
if ($orgParamPage != "" and $content_hash[$urlParamPage]['dir'] != "#") {
   $strMenuExpand = "$( '#menu' ).multilevelpushmenu( 'expand', '" . $content_hash[$urlParamPage]['dir'] . "' )";
}
//-------------- 左のメニューの部分。すでに開いているページに基いて、階層を開くところを決めるための処理ここまで --------------




//-------------- ファイルの更新に合わせて、必ず再読み込みさせたいもの。XXXXXX?v=%(*****)s系 --------------

// まずは日本語フォント系
$timeFontUpdate = filemtime("./font/utrillo_sub.woff");
$strFontUpdate = date("YmdHis", $timeFontUpdate);

// 日本語フォントはサイズが大きいので、「フォントのキャッシュが存在しないようなら、非同期」で、「フォントのキャッシュが存在するようなら、普通に同期」で表示する。
$strWebFontCSSLink = "";
$strWebFontLoader  = "";

// メインのスタイルシート
$timeStyleUpdate = filemtime("./HD_style.min.css");
$strStyleUpdate = date("YmdHis", $timeStyleUpdate);

// 独自のFontAwesome
$timeFontPluginUpdate = filemtime("./font-awesome/css/font-awesome-plugin.css");
$strFontPluginUpdate = date("YmdHis", $timeFontPluginUpdate);

// メニューのカスタムCSS
$timeMLPMCSSUpdate = filemtime("./jquery/hc-offcanvas-nav.custom.css");
$strMLPMCSSUpdate = date("YmdHis", $timeMLPMCSSUpdate);

// メニューのカスタムJS
$timeMLPMCustomUpdate = filemtime("./jquery/hc-offcanvas-nav.custom.js");
$strMLPMCustomUpdate = date("YmdHis", $timeMLPMCustomUpdate);


// パンくずリストJS
$timeBreadCrumpUpdate = filemtime("jquery/multilevelpushmenu-breadcrump.js");
$strBreadCrumpUpdate = date("YmdHis", $timeBreadCrumpUpdate);


// 最終更新日時関連
$pageCTimeObj = filemtime($strPageFileFullPath);
if (isset($filetime) && $filetime > $pageCTimeObj) {
     $pageCTimeObj = $filetime;
}
$strPageDate = date('Y-m-d\TH:i:s', $pageCTimeObj);
$strCurrentYear = date("Y");
$strPageYMD = date('最終更新日 Y-m-d', $pageCTimeObj);
// デフォルトのページ
if ( $urlParamPage == $defaultHomePage ) {
    $strPageYMD = "";
}



// <h2>タグ内の文字列を取得する
preg_match('/<h2( .+?)?>(.*?)<\/h2>/is', $strPageTemplate, $matchesTitle);
if (isset($matchesTitle[2])) {
    $strH2Content = $matchesTitle[2];

    // タグと改行を削除
    $strH2ContentCleaned = strip_tags($strH2Content); // タグを削除
    $strH2ContentCleaned = preg_replace('/\s+/', ' ', $strH2ContentCleaned); // 改行を削除

    // ページ自体が属しているメニューカテゴリ名を取得
    $strParentDir = $content_hash[$urlParamPage]['dir'];

    // ページのH2要素内の文字列と、カテゴリ名の文字列が類似していれば、「ページのH2の中身」だけ
    // 全然違うようなら、「カテゴリ | ページのH2の中身」という構成にして妥当性を上げる
    $lvsDistance = levenshtein($strParentDir, $strH2ContentCleaned);
    $lvsSimilarity = 1 - $lvsDistance / max(strlen($strParentDir), strlen($strH2ContentCleaned));
    if ($lvsSimilarity > 0.7) {
        $strTitle = $strTitle . " | " . $strH2ContentCleaned;
    } else if (strlen($strParentDir)>1) {
        $strTitle = $strTitle . " | " . $strParentDir . " | " . $strH2ContentCleaned;
    } else {
        $strTitle = $strTitle . " | " . $strH2ContentCleaned;
    }
}




// index内にある、スタイル、コンテンツ、階層の開きをそれぞれ、具体的な文字列へと置き換える
$array_style    = array(
     "%(japanese_webfont_css)s",
     "%(title)s",
     "%(canonical)s",
     "%(pagedate)s",
     "%(pageymd)s",
     "%(year)s",
     "%(menu)s",
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
     "%(hover_card)s",
     "%(breadcrump)s",
     "%(content_dynamic)s" );
$array_template = array(
      $strWebFontCSSLink,
      $strTitle, 
      $strCanonical,
      $strPageDate, 
      $strPageYMD,
      $strCurrentYear,
      $strMenuTemplate,
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
      $strHoverCard,
      $strBreadCrumpUpdate,
      $strPageTemplate );
$strIndexEvaluated = str_replace($array_style, $array_template, $strIndexTemplate);

// require("./LIB_remain_days.php");

// トップページとして表示
echo($strIndexEvaluated);
?>

