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
|	https://codeigniter.com/user_guide/general/routing.html
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
$route['default_controller'] = 'PageController';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['backend/reports/load'] = 'BackendController/reports_load';

$route['backend/dashboard/publish'] = 'BackendController/dashboard_publish';
$route['backend/dashboard/delete'] = 'BackendController/dashboard_delete';
$route['backend/dashboard/edit'] = 'BackendController/dashboard_edit';
$route['backend/dashboard/update'] = 'BackendController/dashboard_update';

$route['PublicApi/GetGroups.ashx'] = 'linkedin/AdminAPIController/GetGroups';
$route['PublicApi/GetGroupProfiles.ashx'] = 'linkedin/AdminAPIController/GetGroupProfiles';
$route['PublicApi/GetProfileDiff.ashx/(:any)'] = 'linkedin/AdminAPIController/GetProfileDiff/$1';
$route['PublicApi/GetCompanyProfiles.ashx/(:any)/(:any)'] = 'linkedin/AdminAPIController/GetCompanyProfiles/$1/$2';

$route['biorxiv/admin_api/report_search'] = 'biorxiv/AdminAPIController/reportSearch';
$route['biorxiv/admin_api/report_get_week_list'] = 'biorxiv/AdminAPIController/reportGetWeekList';
$route['biorxiv/admin_api/rss_download'] = 'biorxiv/RSSController/rssDownload';
$route['biorxiv/admin_api/download_csv'] = 'biorxiv/RSSController/downloadListCsv';
$route['biorxiv/admin_api/popup_update'] = 'biorxiv/RSSController/rssPopup';

$route['pubmed/admin_api/report_search'] = 'pubmed/AdminAPIController/reportSearch';
$route['pubmed/admin_api/report_get_week_list'] = 'pubmed/AdminAPIController/reportGetWeekList';
$route['pubmed/admin_api/rss_download'] = 'pubmed/RSSController/rssDownload';
$route['pubmed/admin_api/download_csv'] = 'pubmed/RSSController/downloadListCsv';
$route['pubmed/admin_api/popup_update'] = 'pubmed/RSSController/rssPopup';
$route['pubmed/admin_api/download_dates_csv'] = 'pubmed/RSSController/downloadDatesListCsv';

$route['clinical/admin_api/report_search'] = 'clinical/AdminAPIController/reportSearch';
$route['clinical/admin_api/report_get_week_list'] = 'clinical/AdminAPIController/reportGetWeekList';
$route['clinical/admin_api/rss_download'] = 'clinical/RSSController/rssDownload';
$route['clinical/admin_api/download_csv'] = 'clinical/RSSController/downloadListCsv';
$route['clinical/admin_api/popup_update'] = 'clinical/RSSController/rssPopup';
$route['clinical/admin_api/download_dates_csv'] = 'clinical/RSSController/downloadDatesListCsv';

$route['test'] = 'PageController/test';

$route['(:any)'] = 'PageController/dashboard/$1';
