# bundesliga-dashboard
This is a dashboard that shows the results from Bundesliga, a.k.a. German Soccer Championship

## Require
Like a Laravel application, it needs npm and composer installed at server, but you already knew that, right? 

## Configuration
After clone/download and configure the server, run the following commands
```
composer install
php artisan migrate
```

To get the last updated table, it's a good ideia to configure the schecule from Laravel at your server, so every morning it will update the tables with any changes that might had happen.
```
* * * * * php /path-to-your-project/artisan schedule:run >> /dev/null 2>&1
```

Or you can run the artisan command manually from time to time, but it can be annoying doing it
```
php artisan matches:update
```