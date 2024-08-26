rem now build the app

rm -rf ./database/nativephp.sqlite
touch ./database/nativephp.sqlite

php artisan native:migrate --force
php artisan native:db:seed --force
php artisan native:build win

pause
