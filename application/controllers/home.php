<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		// Enable cache for all partials
		// This should be your first line if you want cache
		//$this->template->cache(60, "frontpage");
		
		// This method will set a default content if there is no content set yet
		// So place this line first if you want to manipulate the default content
		$this->template->title->default("Template library");
		
		// You can use handy methods to manipulate partials
		$this->template->title->append(" | test");
		$this->template->title->prepend("Codeigniter ");
		
		// This will do nothing because there is content set
		$this->template->title->default("I forgot something");
		
		// Stylesheet and javascript
		$this->template->stylesheet->add("css/stylesheet.css");
		$this->template->javascript->add("javasccript/custom.js");
		
		// Meta data
		$this->template->meta->add("robots", "index,follow");
		
		// Load or parse views into partials
		$this->template->content->view("dashboard", array('title'=>"Dashboard"));
		$this->template->content->parse("dashboard_bottom", array("status"=>"not ready yet"));
		
		// Cache the sidebar for 60 seconds
		// You can pass an extra identifier to get different caches for different pages
		// You should place this above all other sidebar interractions
		$this->template->sidebar->cache(60, "frontpage");
		
		// Load widgets into partials
		$this->template->sidebar->widget("advertisement", array("size"=>"small", "position"=>"sidebar"));
		$this->template->sidebar->widget("random_quote");
		
		// Trigger example
		$this->template->sidebar->set_trigger("strtoupper");
		$this->template->sidebar->append("this is lowercase text");
		
		// Publish the template using the default layout file from config
		$this->template->publish();
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */