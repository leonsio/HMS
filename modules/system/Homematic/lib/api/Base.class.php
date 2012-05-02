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
 * Die Klasse sollte nie direkt angesprochen werden
 * 
 *
 */
abstract class Base 
{	
    /**
     * Aktuelles Session-Objekt
     * 
     * @var Session
     */
    protected $session = null;
	/**
	 * Verbindung zu Schnittstelle per JSON
	 *
	 * @var ressource
	 */
	private $connect_json = null;
	/**
	 * Verbindung zu Schnittstelle per XML_RPC
	 *
	 * @var ressource
	 */
	private $connect_xmlrpc = null;		
	/**
	 * Konfigurationsparameter
	 * 
	 * @var object
	 */
	protected $config=null;
	/**
	 * Konstruktor der Klasse
	 */
	protected function initBase() 
	{	
	    // Konfig laden
		$this->config = \Config::getModuleConfig('Homematic'); 
		// Verbindung herstellen
		$this->connect();
	}
	/**
	 * Stellt eine Verbindung zur API her
	 * 
	 * Wir verwendet beide Verbindungsarten um einfacher an die benötigten Daten zu kommen
	 * fürs Schalten der Geräte wird bis auf CUxD Interface XML-RPC verwendet
	 */
	private function connect()
	{
	    // Parameter
	    $options = array('host'=>(string) $this->config->server->host, 'port'=>(string) $this->config->server->port);
        // Verbindung per JSON
        $this->connect_json = new \modules\system\Homematic\lib\driver\client_json($options);     
	}
	/**
	 * Sendet eine Abfrage per XML-RPC
	 * 
	 * @param array $called_form
	 * @param array $parameters
	 */
	private function send_xmlrpc($called_form, $parameters)
	{
	    // Host bestimmen
	    $host = (string) $this->config->server->host;
	    // HMXMLBIN Support
	    if($called_form['args'][0] == 'CUxD')
	    {
	        $host='localhost';
	    }
        // get Function
        $method = $called_form['function'];
        // get Interface
        $interface = array_shift($called_form['args']);
        // Parameterliste bereinigen
        unset($parameters[0]);
        // Collect needed Data
        foreach($parameters as $param)
        {
            $data[$param->name]=array_shift($called_form['args']);
	    }
	    // Set Connection
        $this->connect_xmlrpc = new \client_xmlrpc(array('host'=>$host, 'port'=> (string) $this->config->server->interface->$interface));
        // Get Result
        $result = call_user_func_array(array($this->connect_xmlrpc, $method), $data);
        // Antwort sollte immer array sein
        $result = is_array($result) ? $result : (array) $result;
        // Return (ggr. @eq-3 mal soll die Ausgabe lowcase sein, mal nicht. Entscheidet euch)
        if($method != 'getParamset')
        {
            return $this->xml_array_to_json_object(array_change_key_case($result));
        }
        else
        {
            return $this->xml_array_to_json_object($result);
	    }	    
	}
	/**
	 * Senden eine Abfrage per JSON
	 * 
	 * @param array $called_form
	 * @param array $parameters
	 * @throws \Exception
	 */
	private function send_json($called_form, $parameters)
	{
	    // Dummy
	    $data=array();
	    // get Function
	    $method = $this->API_PREFIX.'.'.$called_form['function'];
	    // Füge Session_ID für alle Requests hinzu
	    if($method != "Session.login")
	    {
	        if(isset($this->session) && !is_null($this->session))
	        {
	            $data['_session_id_']=$this->session->get_id();
	    
	        }
	    }
	    if($method == 'Session.renew' || $method == 'Session.logout')
	    {
	        // Session ID Setzen
	        $data['_session_id_']=$this->session_id;
	    }
	    
	    // Collect needed Data
	    foreach($parameters as $param)
	    {
	        $data[$param->name]=array_shift($called_form['args']);
	    }
	    // Get Result
	    try
	    {
	        $result = $this->connect_json->$method($data);
	        // Return
	        return $result->result;
	    }
	    catch (\Exception $e)
	    {
	        throw new \Exception($e->getMessage());
	    }	    
	}
	/**
	 * Generiert automatisch einen AJAX Call
	 */
	protected function __do_call()
	{
		// Ab php 5.4 kann man hier ressourcen sparren 
		if(version_compare(PHP_VERSION, '5.4.0') >= 0)
		{
			$called_form=debug_backtrace(false, 1);
			$called_form=$called_form[0];
		}
		else
		{
			// load Trace 
			$trace = debug_backtrace();
			// Limit Trace
			$called_form = $trace[1];
		}
        // Default versenden alles per JSON
        $send_xmlrpc = false;
		// Analyse called funtion
		$reflector = new \ReflectionClass(get_called_class());
		// Load function data
		$parameters = $reflector->getMethod($called_form['function'])->getParameters();
		// alle Interface Abfragen können auch als XML-RPC versendet werden
		#print_r( $called_form);
		if($this->API_PREFIX=='Interface' && $this->config->server->use_rpc == 1 && isset($called_form['args'][0]) && $called_form['args'][0] != 'System')
		{
		   $send_xmlrpc = true; 
		}
		// CUxD sollte per XML-RPC versendet werden, falls ein Proxy verfügbar ist
		if($send_xmlrpc  && isset($called_form['args'][0])  && ($called_form['args'][0] == 'CUxD' && $this->config->server->use_bin_proxy== 1))
		{
		    $send_xmlrpc = true;
		}
		// JSON/XML-RPC Support für alle Abfragen außer CUxD und System (CUxD spricht nur binary, bei System gibts wohl einen Bug)
		if($send_xmlrpc)
		{
		   return $this->send_xmlrpc($called_form, $parameters);    	    		    
		}   
		else
		{
            return $this->send_json($called_form, $parameters);
		} 
	}
	/**
	 * Sicherung der Variablen zur Speicherung in der Session
	 */
	public function __sleep()
	{
		return array( 'connect_json', 'connect_xmlrpc');
	}	
	
	/**
	 * Umwandlung von XML-RPC Ausgabe Array zu JSON Object
	 * 
	 * Danke an EQ3, dass die Ausgabe von JSON/XML-RPC nicht an einander angepasst wurde
	 *
	 * @param array $arr
	 * @return object
	 */
	public function xml_array_to_json_object (&$arr)
	{
	    $arr = ! is_object($arr) ? (object) $arr : $arr;
	    foreach ($arr as $tkey => $val)
	    {
            $split=explode('_', $tkey);
            if(count($split)>1)
            {
                foreach($split  as $id => $fix_name)
                {
                    if($id > 0)
                    {
                         $split[$id] = ucfirst($fix_name);   
                    }
                }
                // Allte Variable entfernen
                unset($arr->$tkey);
                $tkey = implode('', $split);
            }

	        if (is_array($val))
	        {
                    continue;
	        }
	        else
	        {
	            if($tkey == 'linkSourceRoles')
	            {
	                $val = explode(' ', $val);
	            }	            
	            $arr->{$tkey} = $val;
	        }
	    }
	    return $arr;
	}	
}

?>