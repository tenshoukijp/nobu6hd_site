<?php
// -----------------以下は発売までの処理。発売したら削除して良い
// 発売までの残日数
$remain_today = time();
$remain_yotei = strtotime("20151112");
$remain_int = $remain_yotei - $remain_today;
$remain_day = ceil($remain_int / (24 * 60 * 60));
$str_remain_day = num2kan_decimal($remain_day);
$strIndexEvaluated = str_replace("%(remain_day)s", $str_remain_day, $strIndexEvaluated);

/*
 * 半角数字を漢数字に変換する（位取り記法）
 * @return string 漢数字
 *一二三四五六七八九十〇
 */
function num2kan_decimal($instr) {
    static $kantbl1 = array(0=>'', 1=>'一', 2=>'二', 3=>'三', 4=>'四', 5=>'五', 6=>'六', 7=>'七', 8=>'八', 9=>'九', '.'=>'．', '-'=>'－');
    static $kantbl2 = array(0=>'', 1=>'十', 2=>'百', 3=>'千');
    static $kantbl3 = array(0=>'', 1=>'万', 2=>'億', 3=>'兆', 4=>'京');

    $outstr = '';
    $len = strlen($instr);
    $m = (int)($len / 4);
    //一、万、億、兆‥‥の繰り返し
    for ($i = 0; $i <= $m; $i++) {
        $s2 = '';
        //一、十、百、千の繰り返し
        for ($j = 0; $j < 4; $j++) {
            $pos = $len - $i * 4 - $j - 1;
            if ($pos >= 0) {
                $ch  = substr($instr, $pos, 1);
                $ch1 = isset($kantbl1[$ch]) ? $kantbl1[$ch] : '';
                $ch2 = isset($kantbl2[$j])  ? $kantbl2[$j]  : '';
                //冒頭が「一」の場合の処理
                if ($ch1 != '') {
                    if ($ch1 == '一' && $ch2 != '')      $s2 = $ch2 . $s2;
                    else                                $s2 = $ch1 . $ch2 . $s2;
                }
            }
        }
        if ($s2 != '')  $outstr = $s2 . $kantbl3[$i] . $outstr;
    }

    return $outstr;
}
?>
