<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
|--------------------------------------------------------------------------
| Template configuration
|--------------------------------------------------------------------------
| This file will contain the settings for the template library.
|
| 'parser'    = if you want your main template file to be parsed, set to TRUE
| 'template'  = the filename of the default template file
| 'cache_ttl' = the time all partials should be cache in seconds, 0 means no global caching
*/

$config['parser']    = FALSE;
$config['template']  = 'template';
$config['cache_ttl'] = 0;

$config['widget_path'] = APPPATH . 'widgets/';

$config['autoload_view_css'] = FALSE;
$config['autoload_view_css_path'] = FCPATH . 'assets/css/views/';

$config['autoload_view_js'] = FALSE;
$config['autoload_view_js_path'] = FCPATH . 'assets/js/views/';

