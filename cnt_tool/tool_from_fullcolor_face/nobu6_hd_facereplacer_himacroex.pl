# HiMacroEx�Ƃ����L�^�c�[���ŁA�M���̖�]HD�ł�0001.bmp�`1300.bmp�������œ���ւ���B

# �ŏ��̐M���̕�
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

# �_�C�A���O�\���̑҂����� �� ****.bmp+���^�[�� �� �E�B���h�E�g�k�m�F+���^�[�� �� �����Ƃ���Ƀ}�E�X�N���b�N
# �� ���̂ЂƂɈړ����邽�߁u���v �� �E���̊�A�C�R���̂Ƃ���Ń}�E�X�N���b�N �� �_�C�A���O�\���̑҂�����(�ŏ��փ��[�v)
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
��
1000
LMouse Down (800,120)
100
LMouse Up (800,120)
EOF

	print $loopcmd;bmp
}

