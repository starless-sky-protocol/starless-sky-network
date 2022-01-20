@echo off

:: Setup PHP and PORT
set PHP_BIN="C:\laragon\bin\php\php-7.4.19-Win32-vc15-x64\php.exe"
set PHP_INI="C:\laragon\bin\php\php-7.4.19-Win32-vc15-x64\php.ini"
set HOST_HOST=localhost
set HOST_PORT=9000

:: Current path
set CURRENT_PATH=%~dp0
set CURRENT_PATH=%CURRENT_PATH:~0,-1%

if not exist %PHP_BIN% (
    echo.
    echo ERROR: %PHP_BIN% not found
    echo.
) else if not exist %PHP_INI% (
    echo.
    echo ERROR: %PHP_INI% not found
    echo.
) else (
    :: Router path
    set ROUTER="%CURRENT_PATH%\system\boot\server.php"

    :: Start built in server
    %PHP_BIN% -S "%HOST_HOST%:%HOST_PORT%" -c %PHP_INI% -t "%CURRENT_PATH%" %ROUTER%
)

:: Prevent close if PHP failed to start
pause