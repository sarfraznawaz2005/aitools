rem make sure "npm rund dev" command is run before this command; strange but it works

rem had to comment out this line for app icon to show because that lib was using depraecated code:
rem vendor/voku/portable-utf8/src/voku/helper/UTF8.php:536

rem now build the app

rem see cleanup command in routes\console.php
php artisan cleanup

npm run build && php artisan native:build win -v

pause
