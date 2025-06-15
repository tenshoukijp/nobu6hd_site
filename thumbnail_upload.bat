cd /d %~dp0

cmd /C C:/usr/nextftp/NEXTFTP_CLI.EXE $Host17 -local="G:/repogitory_jp/nobu6hd_site" -dir="public_html/hd.xn--rssu31gj1g.jp/thumbnail" -quit -nosound -minimize -upload=%1 -nokakunin
