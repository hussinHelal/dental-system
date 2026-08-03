@echo off
setlocal
set "PROJECT_DIR=%~dp0"
cd /d "%PROJECT_DIR%"

echo Starting Zedan Dental Clinic...
echo.

if not exist ".env" (
    if exist ".env.example" (
        copy ".env.example" ".env" >nul
        echo Created .env from .env.example.
    ) else (
        echo Missing .env and .env.example.
        pause
        exit /b 1
    )
)

where php >nul 2>nul
if errorlevel 1 (
    echo PHP was not found. Please install PHP and add it to PATH.
    pause
    exit /b 1
)

where composer >nul 2>nul
if errorlevel 1 (
    echo Composer was not found. Please install Composer and add it to PATH.
    pause
    exit /b 1
)

where npm >nul 2>nul
if errorlevel 1 (
    echo npm was not found. Please install Node.js and add it to PATH.
    pause
    exit /b 1
)

if not exist "vendor" (
    echo Installing PHP dependencies...
    call composer install
)

if not exist "node_modules" (
    echo Installing Node dependencies...
    call npm install
)

echo Starting Laravel server...
start "Laravel" cmd /k "cd /d "%PROJECT_DIR%" && php artisan serve --host=127.0.0.1 --port=8000"

echo Starting Vite dev server...
start "Vite" cmd /k "cd /d "%PROJECT_DIR%" && npm.cmd run dev -- --host 127.0.0.1"

echo Opening browser...
start "" http://127.0.0.1:8000

echo.
echo The application is starting.
echo Open http://127.0.0.1:8000 if the browser does not open automatically.
echo Close the terminal windows when you are done.
pause
