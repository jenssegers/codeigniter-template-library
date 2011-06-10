<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Template configuration
| -------------------------------------------------------------------
| This file will contain the settings for the template library.
|
| ["parser"] Enables parsing of the main layout file.
| ["layout"] The name of the default layout view file used when publishing.
| ["ttl"]	 Time all the partials should be cached in seconds, 0 means no global caching
| $config["widget_prefix"] This is the widget class name prefix you will be using
*/

$config["parser"] = FALSE;
$config["layout"] = "layout";
$config["ttl"]	  = 0;
$config["widget_prefix"] = "w_";