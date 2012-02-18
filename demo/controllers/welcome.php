<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
		$this->template->title = "Welcome!";
	
		// dynamically add a css stylesheet
		$this->template->stylesheet->add("http://twitter.github.com/bootstrap/1.3.0/bootstrap.min.css");
		
		$news = array(); // load from model (but using a dummy array here)
		$this->template->content->view("news", $news);
		
		// set a partial's content
		$this->template->copyright = "&copy; Special Company 2011";
		
		// publish the template
		$this->template->publish();
	}
}