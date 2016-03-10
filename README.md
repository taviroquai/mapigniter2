# MapIgniter2

MapIgniter2 is a geocms that uses [Laravel 5](http://laravel.com), [OpenLayers 3](http://openlayers.org), and [AngularJS](https://angularjs.org/).  

A demo can be found [here](http://taviroquai.com/mapigniter2/public/).  Login with  
email: admin@isp.com  
password: admin  

![MapIgniter 2 Screenshot](public/assets/images/screenshot.png?raw=true "Screenshot")

## Requirements
1. Webserver with PHP 5.5+ (recommended 5.5.9+)
2. Database server (with PDO driver) - defaults to [PostgreSQL](http://www.postgresql.org) server and has currently only been tested on PostgreSQL, but should work with MySQL, SQLite, and Microsoft SQL Server
3. PHP extensions: pdo, pdo_pgsql (if you are using PostgreSQL), fileinfo and any other extensions required by [Laravel Install](http://laravel.com/docs/5.1#installation)

## Install

For linux users running under Apache, refer to [Install Linux](documentation/install_linux.md)  
For windows users running under IIS, refer to [Install Windows IIS](documentation/install_windows.md)

## Web Mapping Features

1. Multiple maps and layers allowed
1. Data Sources: Bing, OSM, WMS, WFS, GPX, KML, Postgis, GeoJSON, Shapefile and CSV
1. Create and edit own map features
1. Default map layout (AngularJS app) featuring layer switcher, map navigation and search
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
