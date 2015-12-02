## Installing on Windows

If you are using Apache on Windows, you can generally follow the standard Apache instructions.  
If you are running on Windows IIS, there are some differences with how you install.

## Installing On IIS 7+
These instructions were tested on IIS 8, but should work more or less the same on lower IIS (Windows 2008+)
1. Make sure you have CGI enabled via  IIS Server / Features
  Basic instructions [Microsoft IIS 7.0 and later](http://php.net/manual/en/install.windows.iis7.php)
  		
2. Install PHP
	a) Install Visual C++ 2010 x86 redistributable (http://www.microsoft.com/en-us/download/details.aspx?id=8328 )
	and http://www.microsoft.com/en-us/download/confirmation.aspx?id=30679 (VC++ Redistributable) -- 
	the PHP that comes with webplatform is 32-bit even when running in IIS 64-bit so you need the 32-bit version if using the IIS Web Platform installer. (also the PHP Manager evidentally requires 2010, while the PHP 5.6 requires 2012 thsu the need to install both).
	
	Usually if you fail this - you'll get a 500 error during PHP run.  You can verify the issue by launching php from cmd shell and it will pop up with a missing .dll vc...dll something or other.
	
		(If you fail to do this before installing PHP via Platform manager, then PHP won't launch and PHP Manager will fail to install)
	* Follow these instructions http://php.net/manual/en/install.windows.iis7.php 
		or use the Web Platform installer (usually avaialbel in IIS 8+) ( products -> Server -> Frameworks) - go here <a href="http://www.microsoft.com/web/downloads/platform.aspx">http://www.microsoft.com/web/downloads/platform.aspx</a> if you don't have it
	* Intall PHP 5.6 or higher
	* If you installed with Web Platform Manager, you should see a PHP Manager in IIS console.
		* Click on it and enable following extensions  (if you prefer or don't have PHP Manager, you can edit the php.ini directly in C:\Program Files (x86)\PHP\v5.6\php.ini
			php_pddo_pgsql.dll, php_fileinfo.dll
