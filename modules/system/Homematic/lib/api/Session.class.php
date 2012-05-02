<?php
namespace modules\system\Homematic\lib\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
/**
 * Klasse für die Verwaltung des Homematic Session
 * 
 *
 */
class Session  extends Base {
    /**
     * Instanz der Klasse
     *
     * @var modules\system\Homematic\lib\api\Session
     */
    private static $instance = null;    
	/**
	 * Aktuelle Session_ID
	 * 
	 * @var string
	 */
	protected $session_id = null;
	/**
	 * Benutzerdaten für die Anmeldung
	 * 
	 * @var object
	 */
	private $auth_data = null;
	/**
	 * für die JSON API angabe des Moduls
	 * 
	 * @var string
	 */
	protected $API_PREFIX = 'Session';
	/**
	 * Konstruktor der Klasse, darf nicht instanziert werden
	 */	
	private function __construct() 
	{	
		// Basis Vorbereiten
		static::initBase();
		// Auth Config laden
		$this->auth_data = $this->config->server->auth;
		// Konstante setzen
		if(!defined('HM_SESSION'))
		{
		    define('HM_SESSION', true);
		}
		else
		{
		    throw new \Exception('Can not declare Session twice');
		}
		// Benutzer einlogen		
		if(!$this->login((string) $this->auth_data->username, (string) $this->auth_data->password))
		{
		    throw new \Exception('Wrong username/password ');
		}
	}
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
	 * Logt den Benutzer ein
	 * 
	 * @param string $username
	 * @param string $password
	 */
	private function login ($username, $password)
	{
		$this->session_id = $this->__do_call();
		if($this->session_id == '')
		{
		    throw new \Exception ('Could not get session_id');
		}
		return true;
	}
	/**
	 * Logt den Benutzer aus
	 */
	public function logout()
	{
		if(!is_null($this->session_id))
			return $this->__do_call();	
	}
	/**
	 * Aktualisiert die Session
	 */
	public function renew()
	{
		return $this->__do_call();	
	}
	/**
	 * gibt die aktuelle Session_id zurück
	 */
	public function get_id()
	{
		return $this->session_id;
	}
	/**
	 * Sicherung der Variablen zur Speicherung in der Session
	 */
	public function __sleep()
	{
	    return array('session_id','API_PREFIX');
	}
	/**
	 * Verhalten nach dem Aufwachen aus der Session
	 */
	public function __wakeup()
	{
	    if(is_null($this->session_id))
	    {
	        $this->__construct();
	    }
	    else
	    {
	        parent::__construct();
	    }
	}
	
	/**
	 *  Verbieten von Clonen
	 */
	private function __clone()
	{
	}	
}


?>