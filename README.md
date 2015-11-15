# MapIgniter2

MapIgniter2 is a geocms based on Laravel 5 and OpenLayers 3.  

A demo can be found [here](http://taviroquai.com/mapigniter2/public/).  Login with  
email: admin@isp.com  
password: admin  

![MapIgniter 2 Screenshot](public/assets/images/screenshot.png?raw=true "Screenshot")

## Install

1. *Download* zip and *extract* to a web server folder
1. Copy **.env.example** to **.env**, create a database and set your local configuration on **.env**
1. Install as you would install a Laravel application
    * ./composer.phar install --prefer-dist
    * php artisan key:generate
    * php artisan migrate
    * php artisan db:seed
1. Give *write permissions to web server* to the following folders:
    * storage
    * bootstrap/cache
    * public/storage
    * resources/views/pages
1. Open browser

### Install troubleshooting
Check web server logs for errors

## Web Mapping Features

1. Multiple maps and layers allowed
1. Data Sources: Bing, OSM, WMS, WFS, GPX, KML, Postgis, GeoJSON and Shapefile...
1. Create and edit own map features
1. Default map layout with layer switcher, map navigation and search
1. Vector features styling

## CMS Features

1. Authentication (Inc. LDAP)
1. Multilanguage
1. Backoffice (based on Twitter Bootstrap)
    1. Users
    1. Roles
    1. Permissions
    1. Website Brand
    1. Pages
    1. Content
        1. SEO fields
        1. Summernote (WYSIWYG)
        1. Main content picture - Allows to upload a main picture
        1. Event - Allows to associate a time to start/end
        1. Images (Gallery) - Allows to upload and associate several images
        1. Attachments - Allows to upload attachments
        1. Location - Allows to associate a location
        1. Transfer content ownership - Useful with permissions
        1. Create a duplicate - Useful to create similar content

## Permissions

There are 2 types of permissions: application and content

1. Application - allows to restrict users to application HTTP routes
2. Content - allows to restrict content editing to users ie. only owner (same user or same role)

## Contribute

Please contribute or just fill in issues...
