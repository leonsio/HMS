<?php

/** 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * @info:         MVC Layout Idee http://devgrow.com/jquery-mobile-php-mvc-framework/
 */
class HMS extends Config
{
    /**
     * Benutzerobjekt
     * @var Auth
     */
    public $auth = null;
    /**
     * Instanz der Klasse
     *
     * @var HMS
     */
    private static $instance = null;    
    /**
     * 
     * @var Template
     */
    private $template = null;
    
    /**
     * Singelton Methode
     * 
     * @see interface_home::init()
     */
    public static function init()
    {
        // Prüfen ob die Instanze berets initalisiert wurde
        if (null === self::$instance) 
        {
        	self::$instance = new self;
        }
        // Instanz zurück geben
        return self::$instance;       
    }
        
    /**
     * Konstuktor der Klasse
     */
    protected function __construct ()
    {
        // Config initalisieren
        parent::__construct();
        // Default konfig laden
        $hms = $this->getCfgValue('HMS');
        // Seiten URL definieren
        if(!defined('BASE_URL'))
        {
            define('BASE_URL', 'http://'.$_SERVER['HTTP_HOST'].$hms->url);
        }  
        // Path definieren
        $uri=parse_url(str_replace(BASE_URL,"","http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']));
        // Cleanup URI
        $uri=isset($uri['path']) ? $uri['path'] : '';       
        // Benutzer initalisieren
        if(!isset($_SESSION['auth']))
        {
        	$_SESSION['auth'] = new Auth($this);
        }
        // Sesion lesen
        $this->auth = $_SESSION['auth']; 
        // Templatesystem laden
        if(!isset($_SESSION['tpl'] ))
        {
            $tpl = isset($_GET['tpl']) ? $_GET['tpl'] : 'basic';
            $_SESSION['tpl']= new Template($this, $tpl);
        }
        $this->template= $_SESSION['tpl']; 
        // Autologin wenn die IP freigegeben wurde
        if(Validate::isIPinRange($_SERVER['REMOTE_ADDR'], explode(',',$hms->local_network)))
        {
            $this->auth->setAuth();
        }
        // Login prüfen
        if(!$this->auth->isAuth())
        {
            $uri = 'user/login';
        }
        // Seite starten
        $this->route($uri);
    }
    /**
     * Loginmethode
     *
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function login ($username, $password)
    {
    	return $this->auth->login($username, $password);
    }    
    /**
     * Initalisiert die Anwendung
     */
    public function route($uri)
    { 
        // URL trennen
        $uri = $this->check_pages(explode('/',$uri));
        // Classe bestimmen
        $model = array_shift($uri);
        // Methide bestimmen, default index
        $action = isset($uri[0]) ? array_shift($uri) : 'index';
		if($model && $model != "index.php")
		{
		    // immer ein Array übergeben
		    $uri = is_array($uri) ? $uri : array();
			if(file_exists(APP_ROOT.'/app/'.$model.'.class.php'))
			{
			    $class='app\\'.$model;
				$$model = new $class($this, $this->template);
				if(method_exists($$model,$action))
				{ 
				    call_user_func_array(array($$model, $action), $uri);
                    $this->template->render($model, $action, true);
				}
				else 
				{    
				    $this->error();
				}
			}
			else 
			{
			    $this->error();			    
			}    
		}
		else 
		{
		    $this->index();		    
		}
        
    }
    /**
     * Überprüft ob die Ziel-Seite vorhanden ist
     * @param array $name
     * @return string
     */
    function check_pages($name = array())
    {
    	if(file_exists(APP_ROOT.'/templates/basic/pages/'.$name[0].'.php'))
    	{
    		$str[0] = "page";
    		$str[1] = $name[0];
    		return $str;
    	}
    	else 
    	{
    	    return $name;
    	}
    }
   
    /**
     * Initalisiert die Error Seite
     */
    private function error()
    {
		$this->template->set_title('Seite nicht gefunden');
		$this->template->render("error");
    }
    /**
     * Initalisiert die Standardseite
     */
    private function index()
    {
        $this->template->set_header('&Uuml;bersicht');
        $this->template->set_title('HMS');
        $this->template->render("main", "start", true);
        $this->template->set_header_menu(array());
    }

    /**
    * clonen ist verboten
    */
    private function __clone ()
    {
        die("klonen nicht moeglich");
    }   

    public function __sleep()
    {
        return array('template','config_type');
    }
    
    public function __wakeup()
    {
        parent::__construct();
    }
    
}

?>