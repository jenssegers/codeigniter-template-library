<?php
if (! defined("BASEPATH"))
	exit("No direct script access allowed");

class Template {
	
	private $_ci;
	private $_partials = array();
	
	private $_layout, $_parser, $_ttl = 0;
	
	/**
	 * Construct with configuration array. Codeigniter will use the config file otherwise
	 * @param array $config
	 */
	public function __construct($config = array()) {
		$this->_ci = & get_instance();
		
		if (! empty($config))
			$this->initialize($config);
		
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
		
		if ($this->_parser)
			$this->_ci->load->library("parser");
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
	private function partial_exists($index) {
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
		if ($this->partial_exists($name))
			$partial = $this->_partials[$name];
		else {
			$partial = new Partial($name);
			if ($this->_ttl)
				$partial->cache($this->_ttl);
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
		$class = "w_" . $name;
		if (! class_exists($class))
			require_once (APPPATH . "widgets/" . $name . EXT);
		$widget = new $class($name, $data);
		
		return $widget;
	}
	
	/**
	 * Add a css source
	 * @param string $source
	 */
	public function add_css($source) {
		if (! stristr($source, "http"))
			$source = base_url() . $source;
		
		$this->partial("stylesheet")->append("\n\t" . '<link rel="stylesheet" type="text/css" href="' . $source . '" />');
	}
	
	/**
	 * Add a javascript source
	 * @param string $source
	 */
	public function add_js($source) {
		if (! stristr($source, "http"))
			$source = base_url() . $source;
		
		$this->partial("javascript")->append("\n\t" . '<script type="text/javascript" src="' . $source . '"></script>');
	}
	
	/**
	 * Add meta data
	 * @param string $name
	 * @param mixed $value
	 * @param enum $type
	 */
	public function add_meta($name, $value, $type = "meta") {
		$name = htmlspecialchars(strip_tags($name));
		$value = htmlspecialchars(strip_tags($value));
		
		if ($name == 'keywords' and ! strpos($value, ','))
			$content = preg_replace('/[\s]+/', ', ', trim($value));
		
		switch ($type) {
			case 'meta' :
				$this->partial("meta")->append("\n\t" . '<meta name="' . $name . '" content="' . $value . '" />');
				break;
			case 'link' :
				$this->partial("meta")->append("\n\t" . '<link rel="' . $name . '" href="' . $value . '" />');
				break;
		}
	}
}

class Partial {
	
	protected $_ci, $_content, $_name, $_ttl = 0, $_cached = false, $_identifier;
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
	function __get($index) {
		if (isset($this->_ci->$index))
			return $this->_ci->$index;
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
	public function set($content) {
		if (! $this->_cached)
			$this->_content = (string) $content;
		
		return $this;
	}
	
	/**
	 * Append something to the content
	 * @param mixed $content
	 * @return Partial
	 */
	public function append($content) {
		if (! $this->_cached)
			$this->_content .= (string) $content;
		
		return $this;
	}
	
	/**
	 * Prepend something to the content
	 * @param mixed $content
	 * @return Partial
	 */
	public function prepend($content) {
		if (! $this->_cached)
			$this->_content = (string) $content . $this->_content;
		
		return $this;
	}
	
	/**
	 * Set content if partial is empty
	 * @param mixed $default
	 * @return Partial
	 */
	public function if_empty($default) {
		if (! $this->_cached)
			if (! $this->_content)
				$this->_content = $default;
		
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
	 */
	public function cache($ttl = 60, $identifier = "") {
		$this->_ci->load->driver('cache', array('adapter' => 'file'));
		$this->_ttl = $ttl;
		$this->_identifier = $identifier;
		
		if ($this->_content = $this->_ci->cache->get($this->cache_id()))
			$this->_cached = true;
		
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