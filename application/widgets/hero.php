<?php

class hero_widget extends Widget {

	public function display($args) {
		
		$data = array("title"=>$args["title"], 
					  "text"=>"Vestibulum id ligula porta felis euismod semper. Integer posuere erat a ante venenatis dapibus posuere velit aliquet. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit.");
		
		$this->load->view("widgets/hero", $data);
	}
	
}