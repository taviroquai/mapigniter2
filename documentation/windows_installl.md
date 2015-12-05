## Installing on Windows

If you are using Apache on Windows, you can generally follow the standard instructions.  
If you are running on Windows IIS, there are some differences with how you install.

## Installing On IIS 7+
PHP is generally not installed on IIS. Before you can use PHP, you must make sure CGI is support is installed and enabled.
via  IIS Server / Features.  

IIS also doesn't understand URL rewrite rules present in .htaccess files, so you will need the URL Rewrite module and use it to import rewrite rules from an .htaccess rules.  More on that later.

There are two ways to install PHP.  Doing a Manual install, or using the Web Platform installer.  I generally go with Web Platform Installer.

### Manual install of PHP

Basic instructions [Microsoft IIS 7.0 and later](http://php.net/manual/en/install.windows.iis7.php)


### Using Web Platform Installer to instal PHP and URLRewrite

These instructions were tested on Windows 2012 R2 IIS 8, but should work more or less the same on lower IIS (Windows 2008+)

1. Make sure you have CGI enabled via  IIS Server / Features
  
  		
2. Install PHP
	* Install Visual C++ 2010 x86 redistributable (http://www.microsoft.com/en-us/download/details.aspx?id=8328 )
	and http://www.microsoft.com/en-us/download/confirmation.aspx?id=30679 (VC++ Redistributable) -- 
	the PHP that comes with webplatform is 32-bit even when running in IIS 64-bit so you need the 32-bit version if using the IIS Web Platform installer. (also the PHP Manager evidentally requires 2010, while the PHP 5.6 requires 2012 thsu the need to install both).
	
	Usually if you do not install the VC++ runtimes - you'll get a 500 error during PHP run.  You can verify the issue by launching php from cmd shell and it will pop up with a missing .dll vc...dll something or other.
	
	If you fail to install VC++ 2010 runtime before installing PHP via Platform manager, then PHP Manager will fail to install)
	* Install Web Platform Installer usually available via IIS console or download from  <a href="http://www.microsoft.com/web/downloads/platform.aspx">http://www.microsoft.com/web/downloads/platform.aspx</a> if you don't have it

	* Using Web Platform Installer (found in IIS Manager) intall PHP 5.6 or higher
	* You should see a PHP Manager in IIS console.
		* Click on it and enable following extensions  (if you prefer or don't have PHP Manager, you can edit the php.ini directly in C:\Program Files (x86)\PHP\v5.6\php.ini
		```
			php_pddo_pgsql.dll, php_fileinfo.dll
		```
		
## Install Mapigntier2

1. *Download* zip and *extract* to a web server folder
1. Open **.env.example** in an editor such as notepad++ and saves as **.env**  as .env. (this is nneeded since win windows files start with . sometimes yield error during copy.
1. create a postgres database and set your local configuration on **.env**
   * If you are not running PostgreSQL on default postgres port (5432), you should specify the port
    by adding a port line. For example:

      ```
          DB_HOST=localhost
	  DB_PORT=9999
          
      ```
      
      Where DB_PORT is entry in env set to what you want.
      
1. Install as you would install a [Laravel](http://laravel.com/) application
by opening up command prompt, cd into folder you extracted
    * php composer.phar install --prefer-dist
    * php artisan key:generate
    * php artisan migrate
    * php artisan db:seed
1. Give *write permissions to web server* to the following folders:
    * storage
    * bootstrap/cache
    * public/storage
    * resources/views/pages
1. Configure the webiste folder in IIS either as an app or new website
1. While still in IIS, select the public folder, go to the option labeled **URL Rewrite**
1. Choose Import and select the .htaccess file found public/.htaccess
1. Exit IIS
1. Open in browser --  your_website_url/public 
