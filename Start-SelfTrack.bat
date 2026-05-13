@echo off
setlocal

set "ROOT=%~dp0"
set "BACKEND_DIR=%ROOT%selftrack"
set "BACKEND_PORT=8000"
set "BACKEND_URL=http://127.0.0.1:%BACKEND_PORT%"

where php >nul 2>nul
if errorlevel 1 (
  echo PHP was not found. Install PHP 8.0 or newer and try again.
  pause
  exit /b 1
)

if not exist "%BACKEND_DIR%\public" (
  echo ERROR: Frontend static files not found at "%BACKEND_DIR%\public"
  echo Please run "npm run build" in the frontend folder first.
  pause
  exit /b 1
)

echo Starting SelfTrack...
start "SelfTrack Server" /B cmd /c "cd /d "%BACKEND_DIR%" && php -S 127.0.0.1:%BACKEND_PORT% -t ."

timeout /t 2 /nobreak

echo.
echo Server started at %BACKEND_URL%
echo Opening browser...
start "" "%BACKEND_URL%"

echo.
echo Close this window to stop the server.
exit /b 0