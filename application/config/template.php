<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| Template configuration
| -------------------------------------------------------------------
| This file will contain the settings for the template library.
|
| 'parser'	= if you want your main template file to be parsed, set to TRUE
| 'template'= the filename of the default template file
| 'ttl'		= the time all partials should be cache in seconds, 0 means no global caching
|
| Because of class name duplicates you can auto correct your widget class names,
| adding a prefix or suffix will allow you to load widgets with their short name.
|
| 'widget_prefix'
| 'widget_suffix'
*/

$config["parser"]	= FALSE;
$config["template"] = "template";
$config["ttl"]		= 0;

$config["widget_prefix"] = "";
$config["widget_suffix"] = "_widget";