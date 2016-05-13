# MapIgniter2 Quick Start

#### How do I enter the backoffice?

1. Login with default username *admin@isp.com* and password *admin*  
2. Click in **Admin** menu or open *http:/localhost/mapigniter2/public/admin*  

#### How can I change the default MapIgniter2 layout style?

There are 2 ways to customize:
1. Using the *Pages* and *Brands* features in backoffice (for non-developers)  
2. Develop your *Laravel* views and stylesheets (for developers and designers)  

#### How do I load my data?

MapIgniter2 supports several data formats:  
1. In backoffice, at Layers menu, click create Layer  
2. Choose layer type. Each layer type has it's own form fields  
    1. For CSV, choose Map Editor; data will be cached into a GeoJSON file  
    2. For Shapefile, choose Shapefile (requires MapServer); a WMS will be created  
    3. For KML choose KML  
    4. For GPX choose GPX  
    5. For GeoPackage choose GeoPackage; data will be cached into a GeoJSON file  
    6. For Postgis choose Postgis; data will be cached into a GeoJSON file  
    7. For data in GeoServer/MapServer choose WMS or WFS  

#### How do I display my data?

1. After creating your layer, for vector features, choose an icon in Static Symbology or setup colors  
2. Create a Map in the **Maps** menu  
2. After saving your new map, click on tab Layers and **Add Layer**  
3. Choose your layer and click save
4. **Important:** set your Map Projection, center coordinates and initial zoom level  
5. Click on View Site and choose your map from Maps menu
6. You should now see your data. If not check, for layer and map projection  

#### How can a user search on map?

The search feature only applies for vector features on map (also called overlayers).  
Be sure to enter on the layer form, the **Searchable Properties** (separated by comma).  

#### How can I customize map popups?

This feature is only available for vector features.  
Be sure to enter Feature Info HTML Template.  
To display the feature title attribute, enter:  
    1. <p>{{ item.title }}</p>  
To display an image, save the image path in feature attribute and enter:
    1. <p><img src="{{ item.image }}" /></p>
The HTML template must be valid and use AngularJS templates format  

#### How do I hide some feature attributes?

For *Postgis* and *GeoPackage* use database Views before create your layer  
For WFS, GPX, KML please prepare your data before loading in MapIgniter2  
