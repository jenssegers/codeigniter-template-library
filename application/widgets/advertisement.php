<?php

class w_advertisement extends Widget {

	public function display($args) {
		
		/* Load stuff from Codeigniter */
		// $this->load->model("m_advertisement");
		// $text = $this->m_advertisement->get($args["position"]);
		
		$text = "This codeigniter template library is cool!";
		$this->view("widgets/advertisement",array("text"=>$text, "id"=>"advertisement_".$args["size"]));
	}
	
}