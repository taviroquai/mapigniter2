## Installing on Linux

This install instructions takes in consideration that the [Requirements](https://github.com/taviroquai/mapigniter2/blob/master/README.md)
 are satisfied before going any further.

1. *Download* zip and *extract* to a web server folder
1. Copy **.env.example** to **.env**
1. Create a database and set your local configuration on **.env**
1. Install as you would install a [Laravel](http://laravel.com/) application
    * php composer.phar install --prefer-dist
    * php artisan key:generate
    * php artisan migrate
    * php artisan db:seed
1. Give *write permissions to web server* to the following folders:
    * storage
    * bootstrap/cache
    * public/storage
    * resources/views/pages
1. Laravel comes with a .htaccess that allows you to take advantage of URL rewrite. To use this feature enable module 
rewrite on Apache with: a2enmod rewrite. Next, restart apache.
1. Open in browser --  your_website_url/public 

### Install troubleshooting
Check web server logs for errors
