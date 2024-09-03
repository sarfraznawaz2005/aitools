rem make sure "npm rund dev" command is run before this command; strange but it works

rem now build the app

rem see cleanup command in routes\console.php
php artisan cleanup

npm run build && php artisan native:build win

pause
