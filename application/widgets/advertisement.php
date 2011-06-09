<?php

class w_advertisement extends Widget {

	public function display($args) {
		
		/* Load stuff from Codeigniter */
		// $this->load->model("m_advertisement");
		// $text = $this->m_advertisement->get($args["position"]);
		
		$data["title"] = "Template Library";
		$data["text"] = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris eget turpis vel augue tincidunt sollicitudin.";
		$data["id"] = "advertisement_".$args["size"];
		
		$this->view("widgets/advertisement",$data);
	}
	
}