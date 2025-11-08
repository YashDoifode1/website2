@echo off
echo ========================================
echo PHPMailer Installation Script
echo ========================================
echo.

REM Check if Composer is installed
where composer >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Composer is not installed!
    echo.
    echo Please install Composer first:
    echo 1. Download from: https://getcomposer.org/download/
    echo 2. Run the installer
    echo 3. Restart this script
    echo.
    pause
    exit /b 1
)

echo [OK] Composer is installed
echo.

REM Navigate to project directory
cd /d "%~dp0"

echo Installing PHPMailer...
echo.

REM Install dependencies
composer install

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ========================================
    echo [SUCCESS] PHPMailer installed successfully!
    echo ========================================
    echo.
    echo Next steps:
    echo 1. Configure .env file with your email settings
    echo 2. For Gmail: Generate App Password
    echo 3. Test email: http://localhost/constructioninnagpur/test-phpmailer.php
    echo.
    echo See PHPMAILER_SETUP.md for detailed instructions.
    echo.
) else (
    echo.
    echo ========================================
    echo [ERROR] Installation failed!
    echo ========================================
    echo.
    echo Please check:
    echo 1. Internet connection
    echo 2. Composer is working: composer --version
    echo 3. Try manual installation (see PHPMAILER_SETUP.md)
    echo.
)

pause
