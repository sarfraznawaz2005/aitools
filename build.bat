rem now build the app

php artisan optimize:clear
npm run build
php artisan native:build win

pause
