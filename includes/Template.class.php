<?php

/** 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 * 
 */

class Template
{
    /**
     * 
     * @var HMS
     */
    private $parent =null;
    /**
     * 
     * @var array
     */
    private $variables=array();
    /**
     * 
     * @var string
     */
    private $header;
    /**
     * 
     * @var array
     */
    private $header_nav=array();
    /**
     * 
     * @var string
     */
    private $title=null;
    /**
     * 
     * @var string
     */
    private $msg;
    /**
     * 
     * @var string
     */
    private $msg_type; 
    /**
     * Template das verwendet werden soll
     */   
    private $engine='basic';
    /**
     * 
     * @param HMS $parent
     */
    function __construct (HMS $parent, $template='basic')
    {
        $this->parent = &$parent;
        $this->engine=$template;
    }
    /**
     * 
     * @param unknown_type $model
     * @param unknown_type $action
     * @param unknown_type $html
     */
	function render($model = "", $action = "", $html = false)
	{
		extract($this->variables);
		include_once(APP_ROOT."/templates/".$this->engine."/header.php");
		$file = APP_ROOT.'/templates/'.$this->engine.'/pages/';
		if($html) echo '<div class="'.$model.'">';
		if($model) $file .= $model;
		if($action) $file .= '/'.$action;
		$file .= '.php';
		if(file_exists($file)) include_once $file;
		if($html) echo '</div>';	
		include_once(APP_ROOT."/templates/".$this->engine."/footer.php");
	}
	/**
	 * 
	 */
	public function get_header()
	{
	    echo $this->header;
	}
	/**
	 * 
	 * @param unknown_type $text
	 */
    public function set_header($text)
    {
        $this->header=$text;
    }
    /**
     * 
     */	
	public function get_footer()
	{
	    
	}
	/**
	 * 
	 */
	public function set_footer()
	{
		 
	}
	/**
	 * 
	 */	
	public function page_title()
	{
	    echo $this->title;
	}
	/**
	 * 
	 * @param unknown_type $title
	 */
	function set_title($title)
	{
		$this->title = $title;
	}
	/**
	 * 
	 */	
	public function get_msg()
	{
	    if($this->msg) echo "<div class='status message {$this->msg_type}'>".$this->msg."</div>\n";	    
	}
	/**
	 * 
	 * @param unknown_type $the_msg
	 * @param unknown_type $type
	 */
	function set_msg($the_msg, $type = null)
	{
		$this->msg = $the_msg;
		$this->msg_type = $type;
	}
	/**
	 * liefert die Header Navigation zurück
	 * @return string
	 */	
	public function get_header_menu()
	{
	    if(count($this->header_nav)<1) return "";
	    // Start
	    echo '<div data-role="navbar" data-iconpos="left" data-theme="a"><ul>';
	    // Alle Elemente
	    foreach($this->header_nav as $link => $text)
	    {
	        $icon=null;
	        if(is_array($text))
	        {
	            $icon = "data-icon='{$text['icon']}'";
	            $text = $text['text'];
	        }
	        echo "<li><a href='{$link}' {$icon} data-theme='a'>{$text}</a></li>";
	    }
	    // Ende
	    echo "</ul></div>";
	}
	/**
	 * Setzt die Header Navigation
	 * @param array $array
	 */
	public function set_header_menu(array $array)
	{
		 $this->header_nav = $array;
	}
	/**
	 * 
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */	
	function assign($name, $value)
	{
		$this->variables[$name] = $value;
	}
}

?>