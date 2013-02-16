<?php
/**
 * @name        CodeIgniter Template Library
 * @author      Jens Segers
 * @link        http://www.jenssegers.be
 * @license     MIT License Copyright (c) 2012 Jens Segers
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

if (!defined("BASEPATH"))
    exit("No direct script access allowed");

class Template {
    
    /* default values */
    private $_template = 'template';
    private $_parser = FALSE;
    private $_cache_ttl = 0;
    private $_widget_path = '';
    
    private $_ci;
    private $_partials = array();
    
    /**
     * Construct with configuration array. Codeigniter will use the config file otherwise
     * @param array $config
     */
    public function __construct($config = array()) {
        $this->_ci = & get_instance();
        
        if (!empty($config)) {
            $this->initialize($config);
        }
        
        log_message('debug', 'Template library initialized');
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
        
        if ($this->_widget_path == '') {
            $this->_widget_path = APPPATH . 'widgets/';
        }
        
        if ($this->_parser && !class_exists('CI_Parser')) {
            $this->_ci->load->library('parser');
        }
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
    public function exists($index) {
        return array_key_exists($index, $this->_partials);
    }
    
    /**
     * Set the template file
     * @param string $template
     */
    public function set_template($template) {
        $this->_template = $template;
    }
    
    /**
     * Publish the template with the current partials
     * You can manually pass a template file with extra data, or use the default template from the config file
     * @param string $template
     * @param array $data
     */
    public function publish($template = FALSE, $data = array()) {
        if (is_array($template) || is_object($template)) {
            $data = $template;
        } else if ($template) {
            $this->_template = $template;
        }
        
        if (!$this->_template) {
            show_error('There was no template file selected for the current template');
        }
        
        if (is_array($data) || is_object($data)) {
            foreach ($data as $name => $content) {
                $this->partial($name)->set($content);
            }
        }
        
        unset($data);
		
		//autoload view assets
		if ($this->_autoload_view_css || $this->_autoload_view_js){
				
			foreach($this->_partials as $partial){
				
				if ($partial->_views){
					
					foreach($partial->_views as $view){
						
						if ($this->_autoload_view_css){
			
							$css_file = $this->_autoload_view_css_path . $view . '.css';
							
								
							//try to load css and js with same name
							if (file_exists($css_file)){

								$this->stylesheet->add(file_get_contents($css_file), FALSE, TRUE);
							}
						}
						
						if ($this->_autoload_view_js){
			
							$js_file = $this->_autoload_view_js_path . $view . '.js';
							
							if (file_exists($js_file)){
								
								$this->javascript->add(file_get_contents($js_file), TRUE);	
							}
						}
					}
				}
				
			}
		}

        
        if ($this->_parser) {
            $this->_ci->parser->parse($this->_template, $this->_partials);
        } else {
            $this->_ci->load->view($this->_template, $this->_partials);
        }
    }
    
    /**
     * Create a partial object with an optional default content
     * Can be usefull to use straight from the template file
     * @param string $name
     * @param string $default
     * @return Partial
     */
    public function partial($name, $default = FALSE) {
        if ($this->exists($name)) {
            $partial = $this->_partials[$name];
        } else {
            // create new partial
            $partial = new Partial($name);
            if ($this->_cache_ttl) {
                $partial->cache($this->_cache_ttl);
            }
            
            // detect local triggers
            if (method_exists($this, 'trigger_' . $name)) {
                $partial->bind($this, 'trigger_' . $name);
            }
            
            $this->_partials[$name] = $partial;
        }
        
        if ($partial->content() === FALSE && $default !== FALSE) {
            $partial->set($default);
        }
        
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
        $class = str_replace('.php', '', trim($name, '/'));
        
        // determine path and widget class name
        $path = $this->_widget_path;
        if (($last_slash = strrpos($class, '/')) !== FALSE) {
            $path += substr($class, 0, $last_slash);
            $class = substr($class, $last_slash + 1);
        }
        
        // new widget
        if(!class_exists($class)) {
            // try both lowercase and capitalized versions
            foreach (array(ucfirst($class), strtolower($class)) as $class) {
                if (file_exists($path . $class . '.php')) {
                    include_once ($path . $class . '.php');
                    
                    // found the file, stop looking
                    break;
                }
            }
        }
        
        if (!class_exists($class)) {
            show_error("Widget '" . $class . "' was not found.");
        }
        
        return new $class($class, $data);
    }
    
    /**
     * Enable cache for all partials with TTL, default TTL is 60
     * @param int $ttl
     * @param mixed $identifier
     */
    public function cache($ttl = 60, $identifier = '') {
        foreach ($this->_partials as $partial) {
            $partial->cache($ttl, $identifier);
        }
        
        $this->_cache_ttl = $ttl;
    }
    
    // ---- TRIGGERS -----------------------------------------------------------------

    /**
     * Stylesheet trigger
     * @param string $source
     */
    public function trigger_stylesheet($url, $attributes = FALSE, $embed = FALSE) {
        
		$attributesString = "";
		
		if ($attributes !== FALSE){
		
			// legacy support for media
	        if (is_string($attributes)) {
	            $attributesString = array('media' => $attributes);
	        }
	        
	        if (is_array($attributes)) {
	        	$attributeString = "";
	        	
	        	foreach ($attributes as $key => $value) {
		        	$attributeString .= $key . '="' . $value . '" ';
	        	}
			}
		}
			
		if ($embed){
			
			return '<style type="text/css" ' . $attributesString . '>' . $url . '</style>' . "\n\t";
		}
		else {
			
			// array support
	        if (is_array($url)) {
	            $return = '';
	            foreach ($url as $u) {
	                $return .= $this->trigger_stylesheet($u, $attributes);
	            }
	            return $return;
	        }
	        
	        if (!stristr($url, 'http://') && !stristr($url, 'https://') && substr($url, 0, 2) != '//') {
	            	
	            $url = $this->_ci->config->item('base_url') . $url;
	        }
	        

	        return '<link rel="stylesheet" href="' . htmlspecialchars(strip_tags($url)) . '" ' . $attributeString . '>' . "\n\t";
		}
    }
    
    /**
     * Javascript trigger
     * @param string $source
     */
    public function trigger_javascript($url, $embed = FALSE) {
       
		if ($embed){
			
			return '<script type="text/javascript">' . $url . '</script>' . "\n\t";
		}
		else {
			
			 // array support
	        if (is_array($url)) {
	            $return = '';
	            foreach ($url as $u) {
	                $return .= $this->trigger_javascript($u);
	            }
	            return $return;
	        }

			if (!stristr($url, 'http://') && !stristr($url, 'https://') && substr($url, 0, 2) != '//') {
	            	
	            $url = $this->_ci->config->item('base_url') . $url;
	        }
			
			return '<script src="' . htmlspecialchars(strip_tags($url)) . '"></script>' . "\n\t";
			
    	}
	}
	
    
    /**
     * Meta trigger
     * @param string $name
     * @param mixed $value
     * @param enum $type
     */
    public function trigger_meta($name, $value, $type = 'meta') {
        $name = htmlspecialchars(strip_tags($name));
        $value = htmlspecialchars(strip_tags($value));
        
        if ($name == 'keywords' and !strpos($value, ',')) {
            $content = preg_replace('/[\s]+/', ', ', trim($value));
        }
        
        switch ($type) {
            case 'meta' :
                $content = '<meta name="' . $name . '" content="' . $value . '">' . "\n\t";
                break;
            case 'link' :
                $content = '<link rel="' . $name . '" href="' . $value . '">' . "\n\t";
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
    
    /**
     * Title trigger, keeps it clean
     * @param string $name
     * @param mixed $value
     * @param enum $type
     */
    public function trigger_description($description) {
        return htmlspecialchars(strip_tags($description));
    }

}

class Partial {
    
    protected $_ci, $_content, $_name, $_cache_ttl = 0, $_cached = false, $_identifier, $_trigger;
    protected $_args = array();
	protected $_views = array();
    
    /**
     * Construct with optional parameters
     * @param array $args
     */
    public function __construct($name, $args = array()) {
        $this->_ci = &get_instance();
        $this->_args = $args;
        $this->_name = $name;
		
		$this->_content = FALSE;
    }
    
    /**
     * Gives access to codeigniter's functions from this class if needed
     * This will be handy in extending classes
     * @param string $index
     */
    function __get($name) {
        return $name == '_views' ? $this->_views : $this->_ci->$name;
    }
    
    /**
     * Alias methods
     */
    function __call($name, $args) {
        switch ($name) {
            case 'default' :
                return call_user_func_array(array($this, 'set_default'), $args);
                break;
			case 'default_view' :
                return call_user_func_array(array($this, 'set_default_view'), $args);
                break;
            case 'add' :
                return call_user_func_array(array($this, 'append'), $args);
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
        if ($this->_cache_ttl && !$this->_cached) {
            $this->cache->save($this->cache_id(), $this->_content, $this->_cache_ttl);
        }
        
        return $this->_content;
    }
    
    /**
     * Overwrite the content
     * @param mixed $content
     * @return Partial
     */
    public function set() {
        if (!$this->_cached) {
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
        if (!$this->_cached) {
            $this->_content .= (string) $this->trigger(func_get_args());
        }
        
        return $this;
    }
    
    /**
     * Prepend something to the content
     * @param mixed $content
     * @return Partial
     */
    public function prepend() {
        if (!$this->_cached) {
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
        if (!$this->_cached) {
            if ($this->_content === FALSE) {
                $this->_content = $default;
            }
        }
        
        return $this;
    }
	
	public function set_default_view($view, $data = array(), $parse = FALSE){
		
		if (!$this->_cached) {
            
			if ($this->_content === FALSE) {
				            
				if ($parse){
					
					$this->parse($view, $data, TRUE);
				}
				else {
					
					$this->view($view, $data, TRUE);
				}	          
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
		

        if (!$this->_cached) {
            
            // better object to array
            if (is_object($data)) {
                $array = array();
                foreach ($data as $k => $v) {
                    $array[$k] = $v;
                }
                $data = $array;
            }
            
            $content = $this->_ci->load->view($view, $data, true);
            
            if ($overwrite) {
            	
            	$this->_views = array($view);
                
                $this->set($content);
            } else {
                	
				$this->_views[] = $view;
					
                $this->append($content);
            }
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
        
		$this->autoload_view_assets($view);	
			
        if (!$this->_cached) {
            if (!class_exists('CI_Parser')) {
                $this->_ci->load->library('parser');
            }
            
            // better object to array
            if (is_object($data)) {
                $array = array();
                foreach ($data as $k => $v) {
                    $array[$k] = $v;
                }
                $data = $array;
            }
            
            $content = $this->_ci->parser->parse($view, $data, true);
            
            if ($overwrite) {
            	
				$this->_views = array($view);
				
                $this->set($content);
				
            } else {
            	
				$this->_views[] = $view;
				
                $this->append($content);
            }
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
        if (!$this->_cached) {
            $widget = $this->template->widget($name, $data);
            
            if ($overwrite) {
                $this->set($widget->content());
            } else {
                $this->append($widget->content());
            }
        }
        return $this;
    }
    
    /**
     * Enable cache with TTL, default TTL is 60
     * @param int $ttl
     * @param mixed $identifier
     */
    public function cache($ttl = 60, $identifier = '') {
        if (!class_exists('CI_Cache')) {
            $this->_ci->load->driver('cache', array('adapter' => 'file'));
        }
        
        $this->_cache_ttl = $ttl;
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
        if ($this->_identifier) {
            return $this->_name . '_' . $this->_identifier . '_' . md5(get_class($this) . implode('', $this->_args));
        } else {
            return $this->_name . '_' . md5(get_class($this) . implode('', $this->_args));
        }
    }
	
    
    /**
     * Trigger returns the result if a trigger is set
     * @param array $args
     * @return string
     */
    public function trigger($args) {
        if (!$this->_trigger) {
            return implode('', $args);
        } else {
        	
            return call_user_func_array($this->_trigger, $args);
        }
    }
    
    /**
     * Bind a trigger function
     * Can be used like bind($this, "function") or bind("function")
     * @param mixed $arg
     */
    public function bind() {
        if ($count = func_num_args()) {
        	
            $args = func_get_args();
				
            if ($count >= 2) {
                
                $obj = array_shift($args);
                $func = array_pop($args);
                
                foreach ($args as $trigger) {
                    $obj = $obj->$trigger;
                }
                
                $this->_trigger = array($obj, $func);
            } else {
                	
                $this->_trigger = reset($args);
            }
        } else {
            $this->_trigger = FALSE;
        }
    }
}

class Widget extends Partial {
    
    /* (non-PHPdoc)
     * @see Partial::content()
     */
    public function content() {
        if (!$this->_cached) {
            if (method_exists($this, 'display')) {
                // capture output
                ob_start();
                $this->display($this->_args);
                $buffer = ob_get_clean();
                
                // if no content is produced but there was direct ouput we set 
                // that output as content
                if (!$this->_content && $buffer) {
                    $this->set($buffer);
                }
            }
        }
        
        return parent::content();
    }
}