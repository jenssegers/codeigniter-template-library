<?php

class w_random_quote extends Widget {

	public function display($args) {
		
		/* Load stuff from Codeigniter */
		// $this->load->model("m_quotes");
		// $text = $this->m_quotes->random();
		
		$text = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris eget turpis vel augue tincidunt sollicitudin. Aliquam semper ligula eu risus rutrum quis facilisis nulla bibendum. Aenean pretium purus ac massa adipiscing sit amet convallis odio aliquet. Aliquam erat volutpat. Proin est sem, euismod at consectetur ut, pellentesque a mi. Duis vehicula risus ac risus ultricies vel suscipit nisi consectetur. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Fusce dui felis, venenatis sit amet posuere vel, suscipit a mauris. Pellentesque ac sapien et orci pellentesque pellentesque nec ut orci. Proin eu mi dolor. Praesent vel risus dui, sit amet eleifend tortor. Sed non nibh a lectus hendrerit tempor id sit amet lacus. ";
		$this->view("widgets/quote", array("text"=>$text));
	}
	
}