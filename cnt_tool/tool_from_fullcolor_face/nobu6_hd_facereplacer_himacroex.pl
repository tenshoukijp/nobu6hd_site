# HiMacroExという記録ツールで、信長の野望HD版の0001.bmp～1300.bmpを自動で入れ替える。

# 最初の信長の分
print<<'EOF';
1000
LMouse Down (94,96)
100
LMouse Up (94,96)
1000
LMouse Down (800,120)
100
LMouse Up (800,120)
EOF

# ダイアログ表示の待ち時間 ⇒ ****.bmp+リターン ⇒ ウィンドウ拡縮確認+リターン ⇒ いいところにマウスクリック
# ⇒ 次のひとに移動するため「↓」 ⇒ 右側の顔アイコンのところでマウスクリック ⇒ ダイアログ表示の待ち時間(最初へループ)
for("0001".."1300") {
	my $numstr = $_;
	my ($a, $b, $c, $d) = $numstr =~ /(.)(.)(.)(.)/;
	my $loopcmd=<<"EOF";
=begin
2000
$a
100
$b
100
$c
100
$d
100
OEM(.)
100
B
100
M
100
P
100
Enter
1000
Enter
1000
LMouse Down (725,50)
100
LMouse Up (725,50)
1000
↓
1000
LMouse Down (800,120)
100
LMouse Up (800,120)
EOF

	print $loopcmd;bmp
}

