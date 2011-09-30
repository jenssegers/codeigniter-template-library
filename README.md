Codeigniter template library
============================

This template library for Codeigniter lets you build complex templates using layouts, partial views and widgets. It's built with the same method chaining support that we are seeing so often in Codeigniter so it feels familiar. This library loads a layout file that uses partial views. These partial view sections are internally represented by Partial Objects managed by the template library. These objects let you modify their content in a user friendly way through method chaining.

Installation
------------

Copy the files to the corresponding folder in your application folder.


Configuration
-------------

In your template.php config file you can change following configuration parameters (optional):

	/*
	| -------------------------------------------------------------------
	| Template configuration
	| -------------------------------------------------------------------
	| This file will contain the settings for the template library.
	|
	| 'parser'	= if you want your main layout file to be parsed, set to TRUE
	| 'layout'	= the filename of the default layout file
	| 'ttl'		= the time all partials should be cache in seconds, 0 means no global caching
	|
	| Because of class name duplicates you can auto correct your widget class names,
	| adding a prefix or suffix will allow you to load widgets with their short name.
	|
	| 'widget_prefix'
	| 'widget_suffix'
	*/

	$config["parser"] = FALSE;
	$config["layout"] = "layout";
	$config["ttl"]	  = 0;

	$config["widget_prefix"] = "";
	$config["widget_suffix"] = "_widget";

If you prefer, you can autoload the library by adjusting your autoload.php file and add 'template' to the $autoload['libraries'] array.
	
Layout files
------------

Layout files are loaded or parsed by Codeigniter and the partials are passed to them as data. You can easily load them like you would normally use data in your view files:

	<head>
		<title><?php echo $title; ?></title>
		<?php echo $stylesheet; ?>
	</head>
	<body>
		<?php echo $content; ?>
	</body>

Or when parsing is enabled you can use {content} etc.

However, I prefer to directly call the library's methods from inside the layout file to work around php's Undefined variable errors when you are not setting all partials. Calling these methods well replace non-existing partials with empty one's so you don't get any errors:

	<head>
		<title><?php echo $this->template->title; ?></title>
		<?php echo $this->template->stylesheet; ?>
	</head>
	<body>
		<?php echo $this->template->content; ?>
	</body>

These variables are in fact Partial Ojects, so you can still manipulate them from inside the layout view file like this:

	<?php echo $title->prepend("My Website - "); ?>

Partial manipulation methods will always return the partial object itself for further chaining or for displaying. So this is perfectly possible:

	<?php echo $sidebar->cache(500)->widget("login")->prepend("Login: "); ?>

Partial manipulation
--------------------

Partials have a few handy methods for manipulating their content such as:

    $partial->set() - overwrites the content
    $partial->append() - append something
    $partial->add() - same as append (alias)
    $partial->prepend() - prepend something
    $partial->content() - gets the content
    $partial->default() - only set content if empty

You can also load dynamic content inside partials from view files or widgets. The object named partial used in the method chaining below is the name of the partial you want to load the content into.

	$this->template->partial->view()

Append or overwrite the partial with a view file with parameters.

	$this->template->partial->view("partial-name", array(), $overwrite=FALSE);
	$this->template->partial->parse()

Append or overwrite the partial with a parsed view file with parameters.

	$this->template->partial->parse("partial-name", array(), $overwrite=FALSE);
	$this->template->partial->widget()

Append or overwrite the partial with a widget's output.

	$this->template->partial->widget("widget-name", array(), $overwrite=FALSE);

Publishing
----------

The template class only has a few methods. I chose to do this because almost everything can be managed by using the flexible Partial Object. If you want to publish the entire template with the current partials to the output you can use the publish() method.

You can pass a custom layout file and optional data if wanted:

	$this->template->publish("layout", array("title"=>"Title is overwritten!"));

Most of the time this will be empty using the layout file from the config:

	$this->template->publish();
	
Triggers
--------

Some partials have built in triggers:

    stylesheet - you only need to pass the source
    javascript - you only need to pass the source
    meta - will convert the arguments to the right meta tag
    title - converts special characters
    description - will convert special characters just like the title

This is an example of what these built in triggers do:

	$this->template->stylesheet->add("stylesheet.css");
	//<link rel="stylesheet" type="text/css" href="http://myweb.com/stylesheet.css" />
	 
	$this->template->javascript->add("script.js");
	//<script type="text/javascript" src="http://myweb.com/script.js"></script>
	 
	$this->template->meta->add("robots", "index,follow");
	//<meta name="robots" content="index,follow" />
	 
	$this->template->title->set("Dad & Son");
	//Dad &amp; Son

You can set your own triggers for functions or methods for any partial object like this:

	//function
	$this->template->partial->set_trigger("strtoupper");
	 
	//method
	$this->template->partial->set_trigger($this->typography, "auto_typography");

This will trigger the function or method whenever you manipulate the partial's content.


Widget
------

Widgets are intelligent partial objects. When their content is asked, their display() method is activated which will fill the content using codeigniter or partial object methods. Widgets classes are found inside the application/widgets folder. They extend the main Widget class which has the same methods as the Partial class. This is an example widget:

	/* File: widgets/hero.php */
	class hero_widget extends Widget {
		 public function display($args = array()) {
			 //$this->prepend("<h1>Slideshow:</h1>");
	 
			 $this->load->model("my_model");
			 $data = $this->my_model->all();
	 
			 $this->view("widgets/hero", $data);
		 }
	}

And this is loaded from a controller like this:

	$this->template->partial->widget("hero_widget", $args = array());

Widgets can have a specific prefix or suffix added to their class name to prevent class name duplicates. You can either set it manually from the config file or use the default _widget suffix. The file and widget name you call from the controllers are without the prefix and suffix.


Caching
-------

I did not want to expand the library in all different ways, therefore I implemented a basic caching function using Codeigniter's caching driver. This might slow your code down on simple websites but allows you to use caching for partials just like you would do yourself with Codeigniter's driver.

You can cache particular partials:

	$this->template->partial->cache(100);

Or you can cache all partials:

	$this->template->cache(100);

Both methods have an extra optional identification parameter that you can use to have multiple cache files for different pages:

	$this->template->cache(100, "frontpage");
