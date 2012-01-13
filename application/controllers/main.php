<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index()
	{
		$this->template->stylesheet->add("http://twitter.github.com/bootstrap/1.3.0/bootstrap.min.css");
		
		$news = array(); // Load from model
		$this->template->content->view("news", $news);
		
		$this->template->copyright = "&copy; Special Company 2011";
		
		$this->template->publish();
	}
}