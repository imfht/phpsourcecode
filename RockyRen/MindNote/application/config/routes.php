<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// 例: api/groups => api/group_api/groups/format/json
$route['api/([\w]+)s(/)?'] = 'api/$1_api/$1s/format/json';
// 例: api/group/1/notebooks => api/notebook_api/notebooks/group_id/1/format/json
$route['api/([\w]+)/([\d]+)/([\w]+)s(/)?'] = 'api/$3_api/$3s/$1_id/$2/format/json';
// 例: api/group/1/notebook/2/notes => api/note_api/notes/group_id/1/notebook_id/2/format/json
$route['api/([\w]+)/([\d]+)/([\w]+)/([\d]+)/([\w]+)s(/)?'] = 'api/$5_api/$5s/$1_id/$2/$3_id/$4/format/json';


//$route['api/(?:(?:[\w]+)/(?:[\d]+)/){0,}([\w]+)/([\d]+)'] = 'api/$1_api/$1/$2/format/json';

// 例: api/groups => api/group_api/group/format/json
$route['api/([\w]+)(/)?'] = 'api/$1_api/$1/format/json';
// 例: api/group/1/notebooks => api/notebook_api/notebook/group_id/1/format/json
$route['api/([\w]+)/([\d]+)/([\w]+)(/)?'] = 'api/$3_api/$3/$1_id/$2/format/json';
// 例: api/group/1/notebook/2/note => api/note_api/note/group_id/1/notebook_id/2/format/json
$route['api/([\w]+)/([\d]+)/([\w]+)/([\d]+)/([\w]+)(/)?'] = 'api/$5_api/$5/$1_id/$2/$3_id/$4/format/json';

// 例: api/group/1 => api/group_api/group/group_id/1/format/json
$route['api/([\w]+)/([\d]+)'] = 'api/$1_api/$1/$1_id/$2/format/json';
// 例: api/group/1/notebook/2 => 'api/notebook_api/notebook/group_id/1/notebook_id/2/format/json'
$route['api/([\w]+)/([\d]+)/([\w]+)/([\d]+)'] = 'api/$3_api/$3/$1_id/$2/$3_id/$4/format/json';
// 例: api/group/1/notebook/2/note/3 => 'api/note_api/note/group_id/1/notebook_id/2/note_id/3/format/json'
$route['api/([\w]+)/([\d]+)/([\w]+)/([\d]+)/([\w]+)/([\d]+)'] = 'api/$5_api/$5/$1_id/$2/$3_id/$4/$5_id/$6/format/json';

