@echo off

rem do not use this command as it will delete existing data of other users, but works in my case.
php artisan native:migrate:fresh

rem now build the app
php artisan native:build win

pause
