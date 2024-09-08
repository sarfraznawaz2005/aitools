rem had to comment out this line for app icon to show because that lib was using depraecated code:
rem vendor/voku/portable-utf8/src/voku/helper/UTF8.php:536

rem now build the app

rem see cleanup command in routes\console.php
php artisan cleanup

php artisan native:build win x64 -v --no-interaction

pause
