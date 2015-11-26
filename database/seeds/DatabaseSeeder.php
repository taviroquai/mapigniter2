<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Role;
use App\Permission;
use App\Brand;
use App\Content;
use App\Event;
use App\Location;
use App\Page;
use App\Projection;
use App\Layer;
use App\Map;
use App\Layeritem;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Copy distributable storage folder
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            shell_exec('xcopy /H /E /Y ' . public_path('storage.dist') . ' ' . public_path('storage'));
        } else {
            shell_exec('cp -R ' . public_path('storage.dist') . ' ' . public_path('storage'));
        }
        
        Model::unguard();

        // $this->call(UserTableSeeder::class);
        if (!DB::table('users')->count()) {
            User::create(
                [
                    'name' => 'Admin',
                    'email' => 'admin@isp.com',
                    'password' => Hash::make('admin'),
                    'twitter_id' => '',
                    'facebook_id' => '',
                    'gplus_id' => '',
                    'avatar' => ''
                ]
            );
        }
        
        if (!DB::table('roles')->count()) {
            Role::create(
                [
                    'name' => 'Admin',
                    'content_permission' => 'NONE'
                ]
            );
            Role::create(
                [
                    'name' => 'Backoffice User',
                    'content_permission' => 'ROLE'
                ]
            );
            Role::create(
                [
                    'name' => 'Registered',
                    'content_permission' => 'NONE'
                ]
            );
        }
        
        if (!DB::table('role_user')->count()) {
            DB::table('role_user')->insert([
                [
                    'role_id' => 1,
                    'user_id' => 1
                ]
            ]);
        }
        
        if (!DB::table('permissions')->count()) {
            Permission::create(
                [
                    'label' => 'Backoffice',
                    'http' => 'GET',
                    'route' => 'admin'
                ]
            );
            Permission::create(
                [
                    'label' => 'Pages List',
                    'http' => 'GET',
                    'route' => 'admin/pages/list'
                ]
            );
            Permission::create(
                [
                    'label' => 'Users List',
                    'http' => 'GET',
                    'route' => 'admin/users/list'
                ]
            );
            Permission::create(
                [
                    'label' => 'Roles List',
                    'http' => 'GET',
                    'route' => 'admin/roles/list'
                ]
            );
            Permission::create(
                [
                    'label' => 'Permissions List',
                    'http' => 'GET',
                    'route' => 'admin/permissions/list'
                ]
            );
            Permission::create(
                [
                    'label' => 'Site Brand',
                    'http' => 'GET',
                    'route' => 'admin/brands/list'
                ]
            );
            Permission::create(
                [
                    'label' => 'Delete Content',
                    'http' => 'GET',
                    'route' => 'admin/contents/delete'
                ]
            );
            Permission::create(
                [
                    'label' => 'Change Content Ownership',
                    'http' => 'GET',
                    'route' => 'admin/contents/ownership'
                ]
            );
        }
        
        if (!DB::table('permission_role')->count()) {
            DB::table('permission_role')->insert([
                [
                    'role_id' => 3,
                    'permission_id' => 1,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 2,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 3,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 4,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 5,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 6,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 7,
                    'access' => 'DENY'
                ],
                [
                    'role_id' => 2,
                    'permission_id' => 8,
                    'access' => 'DENY'
                ]
            ]);
        }
        
        if (!DB::table('brands')->count()) {
            Brand::create(
                [
                    'name' => 'Brand',
                    'slogan' => 'Brand slogan...',
                    'description' => 'Brand description',
                    'keywords' => 'keyword',
                    'author' => 'author',
                    'logo' => 'picture.png',
                    'active' => 1,
                    'css' => '',
                    'config' => ''
                ]
            );
        }
        
        if (!DB::table('contents')->count()) {
            Content::create(
                [
                    'user_id' => 1,
                    'lang' => 'en',
                    'title' => 'Demo',
                    'seo_slug' => 'demo',
                    'seo_title' => 'Demo',
                    'seo_description' => 'Demo',
                    'seo_keywords' => 'demo',
                    'seo_author' => 'admin',
                    'seo_image' => 'picture.png',
                    'content' => '<p>Content...<br></p>',
                    'publish_start' => '2015-07-01',
                    'role_permission' => 'NONE'
                ]
            );
        }
        
        if (!DB::table('events')->count()) {
            Event::create(
                [
                    'content_id' => 1,
                    'start' => '2015-07-1',
                    'end' => '2015-07-2'
                ]
            );
        }
        
        if (!DB::table('locations')->count()) {
            Location::create(
                [
                    'content_id' => 1,
                    'address' => 'Lisbon, Portugal',
                    'lat' => '38.7222524',
                    'lon' => '-9.139336599999979',
                    'zoom' => 5
                ]
            );
        }
        
        if (!DB::table('pages')->count()) {
            Page::create(
                [
                    'name' => 'demo_notfound',
                    'route' => 'page/notfound',
                    'active' => 1
                ]
            );
            Page::create(
                [
                    'name' => 'demo_home',
                    'route' => 'demo/home',
                    'active' => 1
                ]
            );
            Page::create(
                [
                    'name' => 'demo_content',
                    'route' => '{slug}',
                    'active' => 1
                ]
            );
            Page::create(
                [
                    'name' => 'demo_events',
                    'route' => 'demo/events',
                    'active' => 1
                ]
            );
            Page::create(
                [
                    'name' => 'demo_map',
                    'route' => 'demo/map',
                    'active' => 1
                ]
            );
            Page::create(
                [
                    'name' => 'demo_webgis',
                    'route' => '/',
                    'active' => 1
                ]
            );
        }
        
        if (!DB::table('projections')->count()) {
            Projection::create(
                [
                    'srid' => '3857',
                    'proj4_params' => '',
                    'extent' => '-20026376.39 -20048966.10 20026376.39 20048966.10'
                ]
            );
        }
        
        if (!DB::table('layers')->count()) {
            $content = new Content;
            $content->user_id = 1;
            $content->lang = 'en';
            $content->title = 'Open Street Map';
            $content->seo_slug = 'open-street-map';
            $content->role_permission = 'NONE';
            $content->save();
            Layer::create(
                [
                    'user_id' => 1,
                    'content_id' => $content->id,
                    'projection_id' => 3857,
                    'type' => 'osm'
                ]
            );
            
            $content = new Content;
            $content->user_id = 1;
            $content->lang = 'en';
            $content->title = 'Markers';
            $content->seo_slug = 'markers';
            $content->role_permission = 'NONE';
            $content->save();
            Layer::create(
                [
                    'user_id' => 1,
                    'content_id' => $content->id,
                    'projection_id' => 3857,
                    'type' => 'geojson',
                    'geojson_geomtype' => 'Point',
                    'geojson_attributes' => 'label',
                    'geojson_features' => '{"type":"FeatureCollection","features":[{"type":"Feature","geometry":{"type":"Point","coordinates":[-851576.57182518,4456806.642252369]},"properties":{"label":"Tavira"}}],"crs":{"type":"name","properties":{"name":"EPSG:3857"}}}',
                    'feature_info_template' => '<p>{{ item.label }}</p>',
                    'search' => 'label',
                    'ol_style_static_icon' => 'ol_style_static_icon.png',
                    'ol_style_static_fill_color' => '',
                    'ol_style_static_stroke_color' => '',
                    'ol_style_static_stroke_width' => ''
                ]
            );
            Layer::find(2)->saveGeoJSONFile();
            
        }
        
        if (!DB::table('maps')->count()) {
            $content = new Content;
            $content->user_id = 1;
            $content->lang = 'en';
            $content->title = 'Map1';
            $content->seo_slug = 'map1';
            $content->role_permission = 'NONE';
            $content->publish_start = '2015-07-01';
            $content->save();
            Map::create(
                [
                    'user_id' => 1,
                    'content_id' => $content->id,
                    'projection_id' => 3857,
                    'center' => '0 0',
                    'zoom' => 2
                ]
            );
            
            // Add OSM layer
            $mapitem = new Layeritem();
            $mapitem->map_id = 1;
            $mapitem->layer_id = 1;
            $mapitem->parent_id = 0;
            $mapitem->visible = 1;
            $mapitem->baselayer = 1;
            $mapitem->displayorder = 1;
            $mapitem->save();
            
            // Add markers layer
            $mapitem = new Layeritem();
            $mapitem->map_id = 1;
            $mapitem->layer_id = 2;
            $mapitem->parent_id = 0;
            $mapitem->visible = 1;
            $mapitem->baselayer = 0;
            $mapitem->displayorder = 1;
            $mapitem->save();
        }

        Model::reguard();
    }
}
