rem do not use this command as it will delete existing data of other users, but works in my case.
php artisan native:migrate:fresh

php artisan native:db:seed

rem now build the app
php artisan native:build win

pause
