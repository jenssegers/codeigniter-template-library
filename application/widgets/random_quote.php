<?php

class w_random_quote extends Widget {

	public function display($args) {
		
		/* Load stuff from Codeigniter */
		// $this->load->model("m_quotes");
		// $quotes = $this->m_quotes->all();
		
		$this->load->helper("text");
		
		$quotes[] = array("person"=>"Eric Cartman", "quote"=>"It's a man's obligation to stick his boneration in a women's separation; this sort of penetration will increase the population of the younger generation.");
		$quotes[] = array("person"=>"Mr Garrison (on women's period)", "quote"=>"I just don't trust anything that bleeds for five days and doesn't die.");
		$quotes[] = array("person"=>"Eric Cartman", "quote"=>"Respect My Authority!");
		$quotes[] = array("person"=>"Kyle", "quote"=>"You bastards.");
		$quotes[] = array("person"=>"Eric Cartman", "quote"=>"You so much as TOUCH kitty's ass, and I'll put a firecracker in your nutsack and blow your balls all over your pants.");
		$quotes[] = array("person"=>"Eric Cartman", "quote"=>"You seem a little irritable, Kyle. You got some sand in your vagina?");
		$quotes[] = array("person"=>"Eric Cartman", "quote"=>"How 'bout we sing, 'Kyle's Mom is a stupid bitch' in D Minor.");
		
		$quote = $quotes[array_rand($quotes)];
		
		$this->view("widgets/quote", $quote);
	}
	
}