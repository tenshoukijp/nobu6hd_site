curl -s https://hd.xn--rssu31gj1g.jp/sitemap.php
timeout /t 5

cmd /C C:/usr/nextftp/NEXTFTP_CLI.EXE $Host17 -local="G:/repogitory_jp/nobu6hd_site" -quit -nosound -minimize -download=sitemap.xml -nokakunin

