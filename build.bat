rem now build the app

rem see cleanup command in routes\console.php
php artisan cleanup

npm run build && php artisan native:build win

pause
