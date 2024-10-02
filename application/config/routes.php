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
|	https://codeigniter.com/userguide3/general/routing.html
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
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['deal/create'] = 'Tasks/create';
$route['deal/update/(:num)'] = 'deals/update/$1';
$route['deal/pdf/(:num)'] = 'tasks/deal_pdf/$1';
$route['deal/products'] = 'deals/fetch_all_products';
$route['deal/qoute/(:num)'] = 'deals/download_quote_pdf/$1';
$route['deal/zoho/update'] = 'Tasks/update_all_leads_and_files';
$route['deal/background/process'] = 'deals/background_task_processor';

$route['deal/user/(:num)'] = 'Tasks/list_tasks_per_user';

$route['payments/user/(:num)'] = 'Tasks/get_payments_by_user/$1';
$route['tasks/big-project/(:num)'] = 'Tasks/get_big_projects/$1';

// Login route
$route['register'] = 'AUTH_Controller/register';
$route['login'] = 'AUTH_Controller/login';
$route['logout'] = 'AUTH_Controller/logout';
$route['delete/user'] = 'AUTH_Controller/delete_user';


$route['templates'] = 'deals/fetch_templates';

// Protected route
$route['protected-route'] = 'AUTH_Controller/protected_route';
