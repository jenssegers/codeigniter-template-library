<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function index()
	{
		// You can use handy methods to manipulate partials
		$this->template->title = "Template library";
		$this->template->title->append(" | test");
		$this->template->title->prepend("Codeigniter ");
		
		// Stylesheet and javascript
		$this->template->add_css("css/stylesheet.css");
		$this->template->add_js("javasccript/custom.js");
		
		// Meta data
		$this->template->add_meta("robots", "index,follow");
		
		// Load or parse views into partials
		$this->template->content->view("dashboard", array('title'=>"Dashboard"));
		$this->template->content->parse("dashboard_bottom", array("status"=>"not ready yet"));
		
		// Cache the sidebar for 60 seconds
		// You can pass an extra identifier to get different caches for differen pages
		// It's recommended that you place this before adding content to the partial
		$this->template->sidebar->cache(60, "frontpage");
		
		// Load widgets into partials
		$this->template->sidebar->widget("advertisement", array("size"=>"small", "position"=>"sidebar"));
		$this->template->sidebar->widget("random_quote");

		// Publish the template using the default layout file from config
		$this->template->publish();
	}
}

/* End of file home.php */
/* Location: ./application/controllers/home.php */