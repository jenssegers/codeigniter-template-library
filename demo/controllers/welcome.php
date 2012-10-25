<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends GAS_Controller {
    
    public function index() {
    	//var_dump($this->config->item('theme_path'));
        $this->template->title = 'Welcome!';
        $this->template->widget(HERO, array("title"=>"Hello, world!"), SIDEBAR, TRUE);
        $this->template->stylesheet->add('http://twitter.github.com/bootstrap/1.3.0/bootstrap.min.css');
        
        $news = array(); // load from model (but using a dummy array here)
        $this->template->content->view($this->config->item('theme_path').'news', $news);
        
        // set a partial's content
        $this->template->copyright = '&copy; Special Company 2011';
        
        // publish the template
        $this->template->publish();
    }
}