<?php
/**
* @name			CodeIgniter Template Library
* @author		Jens Segers
* @link			http://www.jenssegers.be
* @license		MIT License Copyright (c) 2011 Jens Segers
* 
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
* 
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*/

if (! defined("BASEPATH"))
	exit("No direct script access allowed");

class Template {
	
	private $_ci;
	private $_partials = array();
	
	private $_layout, $_parser = FALSE, $_widget_prefix = "w_", $_ttl = 0;
	
	/**
	 * Construct with configuration array. Codeigniter will use the config file otherwise
	 * @param array $config
	 */
	public function __construct($config = array()) {
		$this->_ci = & get_instance();
		
		if (! empty($config))
			$this->initialize($config);
		
		if (! function_exists("base_url"))
			$this->_ci->load->helper('url');
		
		log_message('debug', 'Template Library Initialized');
	}
	
	/**
	 * Initialize with configuration array
	 * @param array $config
	 * @return Template
	 */
	public function initialize($config = array()) {
		foreach ($config as $key => $val) {
			$this->{'_' . $key} = $val;
		}
		
		if ($this->_parser && ! class_exists('CI_Parser'))
			$this->_ci->load->library('parser');
	}
	
	/**
	 * Set a partial's content. This will create a new partial when not existing
	 * @param string $index
	 * @param mixed $value
	 */
	public function __set($name, $value) {
		$this->partial($name)->set($value);
	}
	
	/**
	 * Access to partials for method chaining
	 * @param string $name
	 * @return mixed
	 */
	public function __get($name) {
		return $this->partial($name);
	}
	
	/**
	 * Check if a partial exists
	 * @param string $index
	 * @return boolean
	 */
	private function exists($index) {
		return array_key_exists($index, $this->_partials);
	}
	
	/**
	 * Publish the template with the current partials
	 * You can manually pass a layout file with extra data, or use the default layout from the config file
	 * @param string $layout
	 * @param array $data
	 */
	public function publish($layout = FALSE, $data = array()) {
		if (is_array($layout))
			$data = $layout;
		else if ($layout)
			$this->_layout = $layout;
		
		if (! $this->_layout)
			show_error("There was no layout file selected for the current template");
		
		if (! empty($data))
			$this->_partials = array_merge($this->_partials, $data);
		
		unset($data);
		
		if ($this->_parser)
			$this->_ci->parser->parse($this->_layout, $this->_partials);
		else
			$this->_ci->load->view($this->_layout, $this->_partials);
	}
	
	/**
	 * Create a partial object with an optional default content
	 * Can be usefull to use straight from the template file
	 * @param string $name
	 * @param string $default
	 * @return Partial
	 */
	public function partial($name, $default = "") {
		if ($this->exists($name))
			$partial = $this->_partials[$name];
		else {
			$partial = new Partial($name);
			if ($this->_ttl)
				$partial->cache($this->_ttl);
			
		// Detect local triggers
			if (method_exists($this, "trigger_" . $name))
				$partial->set_trigger($this, "trigger_" . $name);
			
			$this->_partials[$name] = $partial;
		}
		
		if (! $partial->content() && $default)
			$partial->set($default);
		
		return $partial;
	}
	
	/**
	 * Create a widget object with optional parameters
	 * Can be usefull to use straight from the template file
	 * @param string $name
	 * @param array $data
	 * @return Widget
	 */
	public function widget($name, $data = array()) {
		$class = $this->_widget_prefix . $name;
		if (! class_exists($class))
			require_once (APPPATH . "widgets/" . $name . EXT);
		$widget = new $class($name, $data);
		
		return $widget;
	}
	
	/**
	 * Enable cache for all partials with TTL, default TTL is 60
	 * @param int $ttl
	 * @param mixed $identifier
	 */
	public function cache($ttl = 60, $identifier = "") {
		foreach ($this->_partials as $partial) {
			$partial->cache($ttl, $identifier);
		}
		
		$this->_ttl = $ttl;
	}
	
	/**
	 * Stylesheet trigger
	 * @param string $source
	 */
	public function trigger_stylesheet($source) {
		if (! stristr($source, "http"))
			$source = base_url() . $source;
		
		return "\n\t" . '<link rel="stylesheet" type="text/css" href="' . htmlspecialchars(strip_tags($source)) . '" />';
	}
	
	/**
	 * Javascript trigger
	 * @param string $source
	 */
	public function trigger_javascript($source) {
		if (! stristr($source, "http"))
			$source = base_url() . $source;
		
		return "\n\t" . '<script type="text/javascript" src="' . htmlspecialchars(strip_tags($source)) . '"></script>';
	}
	
	/**
	 * Meta trigger
	 * @param string $name
	 * @param mixed $value
	 * @param enum $type
	 */
	public function trigger_meta($name, $value, $type = "meta") {
		$name = htmlspecialchars(strip_tags($name));
		$value = htmlspecialchars(strip_tags($value));
		
		if ($name == 'keywords' and ! strpos($value, ','))
			$content = preg_replace('/[\s]+/', ', ', trim($value));
		
		switch ($type) {
			case 'meta' :
				$content = "\n\t" . '<meta name="' . $name . '" content="' . $value . '" />';
				break;
			case 'link' :
				$content = "\n\t" . '<link rel="' . $name . '" href="' . $value . '" />';
				break;
		}
		
		return $content;
	}
	
	/**
	 * Title trigger, keeps it clean
	 * @param string $name
	 * @param mixed $value
	 * @param enum $type
	 */
	public function trigger_title($title) {
		return htmlspecialchars(strip_tags($title));
	}

}

class Partial {
	
	protected $_ci, $_content, $_name, $_ttl = 0, $_cached = false, $_identifier, $_trigger;
	protected $_args = array();
	
	/**
	 * Construct with optional parameters
	 * @param array $args
	 */
	public function __construct($name, $args = array()) {
		$this->_ci = &get_instance();
		$this->_args = $args;
		$this->_name = $name;
	}
	
	/**
	 * Gives access to codeigniter's functions from this class if needed
	 * This will be handy in extending classes
	 * @param string $index
	 */
	function __get($name) {
		if (isset($this->_ci->$name))
			return $this->_ci->$name;
	}
	
	function __call($name, $args) {
		switch ($name) {
			case "default" :
				return call_user_func_array(array($this, "set_default"), $args);
				break;
		}
	}
	
	/**
	 * Returns the content when converted to a string
	 * @return string
	 */
	public function __toString() {
		return (string) $this->content();
	}
	
	/**
	 * Returns the content
	 * @return string
	 */
	public function content() {
		if ($this->_ttl && ! $this->_cached) {
			$this->cache->save($this->cache_id(), $this->_content, $this->_ttl);
		}
		
		return $this->_content;
	}
	
	/**
	 * Overwrite the content
	 * @param mixed $content
	 * @return Partial
	 */
	public function set() {
		if (! $this->_cached) {
			$this->_content = (string) $this->trigger(func_get_args());
		}
		
		return $this;
	}
	
	/**
	 * Append something to the content
	 * @param mixed $content
	 * @return Partial
	 */
	public function append() {
		if (! $this->_cached) {
			$this->_content .= (string) $this->trigger(func_get_args());
		}
		
		return $this;
	}
	
	/**
	 * Append alias method
	 * @param mixed $content
	 * @return Partial
	 */
	public function add() {
		$args = func_get_args();
		return call_user_func_array(array($this, "append"), $args);
	}
	
	/**
	 * Prepend something to the content
	 * @param mixed $content
	 * @return Partial
	 */
	public function prepend() {
		if (! $this->_cached) {
			$this->_content = (string) $this->trigger(func_get_args()) . $this->_content;
		}
		
		return $this;
	}
	
	/**
	 * Set content if partial is empty
	 * @param mixed $default
	 * @return Partial
	 */
	public function set_default($default) {
		if (! $this->_cached) {
			if (! $this->_content || $this->_content = "") {
				$this->_content = $default;
			}
		}
		
		return $this;
	}
	
	/**
	 * Load a view inside this partial, overwrite if wanted
	 * @param string $view
	 * @param array $data
	 * @param bool $overwrite
	 * @return Partial
	 */
	public function view($view, $data = array(), $overwrite = false) {
		if (! $this->_cached) {
			$content = $this->_ci->load->view($view, array_merge($this->_args, $data), true);
			
			if ($overwrite)
				$this->set($content);
			else
				$this->append($content);
		}
		return $this;
	}
	
	/**
	 * Parses a view inside this partial, overwrite if wanted
	 * @param string $view
	 * @param array $data
	 * @param bool $overwrite
	 * @return Partial
	 */
	public function parse($view, $data = array(), $overwrite = false) {
		if (! $this->_cached) {
			if (! class_exists('CI_Parser'))
				$this->_ci->load->library("parser");
			
			$content = $this->_ci->parser->parse($view, array_merge($this->_args, $data), true);
			
			if ($overwrite)
				$this->set($content);
			else
				$this->append($content);
		}
		return $this;
	}
	
	/**
	 * Loads a widget inside this partial, overwrite if wanted
	 * @param string $name
	 * @param array $data
	 * @param bool $overwrite
	 * @return Partial
	 */
	public function widget($name, $data = array(), $overwrite = false) {
		if (! $this->_cached) {
			$widget = $this->template->widget($name, $data);
			
			if ($overwrite)
				$this->set($widget->content());
			else
				$this->append($widget->content());
		}
		return $this;
	}
	
	/**
	 * Enable cache with TTL, default TTL is 60
	 * @param int $ttl
	 * @param mixed $identifier
	 */
	public function cache($ttl = 60, $identifier = "") {
		if (! class_exists("CI_Cache"))
			$this->_ci->load->driver('cache', array('adapter' => 'file'));
		
		$this->_ttl = $ttl;
		$this->_identifier = $identifier;
		
		if ($cached = $this->_ci->cache->get($this->cache_id())) {
			$this->_cached = true;
			$this->_content = $cached;
		}
		return $this;
	}
	
	/**
	 * Used for cache identification
	 * @return string
	 */
	private function cache_id() {
		if ($this->_identifier)
			return $this->_name . '_' . $this->_identifier . '_' . md5(get_class($this) . implode('', $this->_args));
		else
			return $this->_name . '_' . md5(get_class($this) . implode('', $this->_args));
	}
	
	/**
	 * Trigger returns the result if a trigger is set
	 * @param array $args
	 * @return string
	 */
	private function trigger($args) {
		if (! $this->_trigger)
			return implode('', $args);
		else
			return call_user_func_array($this->_trigger, $args);
	}
	
	/**
	 * Set a trigger function
	 * Can be used like set_trigger($this, "function"); or set_trigger("function");
	 * @param mixed $arg
	 */
	public function set_trigger() {
		if ($count = func_num_args()) {
			if ($count >= 2) {
				$args = func_get_args();
				$obj = array_shift($args);
				$func = array_pop($args);
				
				foreach ($args as $trigger)
					$obj = $obj->$trigger;
				
				$this->_trigger = array($obj, $func);
			}
			else {
				$this->_trigger = reset(func_get_args());
			}
		}
		else
			$this->_trigger = FALSE;
	}
}

class Widget extends Partial {
	
	/* (non-PHPdoc)
	 * @see Partial::content()
	 */
	public function content() {
		if (! $this->_cached) {
			if (method_exists($this, "display"))
				$this->display($this->_args);
		}
		
		return parent::content();
	}
}