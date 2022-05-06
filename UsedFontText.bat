cd %~dp0

RegistSubfontMk.exe
UniqueTextCharacter > utrillo_update.txt

fc utrillo.txt utrillo_update.txt
if %errorlevel%==0 goto SAME
if %errorlevel%==2 goto DIFF

:DIFF
copy /Y utrillo_update.txt utrillo.txt
del utrillo_update.txt
copy utrillo.txt C:\usr\eclipython\workspace\website\utrillo.txt
C:
cd "C:\usr\subsetfontmk\"
SubsetFontMk.exe utrillo.txt
cd %~dp0
cd C:\usr\eclipython\workspace\website\HD.version\font
ToFTPUploadHDWeb.exe

:SAME
@echo ç∑ï™Ç»Çµ;

:END
 