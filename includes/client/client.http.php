<?php
/**
 *  Client um beliebige Abfragen per HTTP zu versenden
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * @subpackage Client
 * @version 1.0
 * @id $Id$
 *  
 * 
 */
class client_http 
{
	/**
	 * Verbindungsidentifer zu dem Server
	 *
	 * @var ressource
	 */
	private $connect = null;
	/**
	 * Ausgabe von dem Server
	 * 
	 * @var string
	 */
	private $result = null;
	/**
	 * Konstruktor der Klasse
	 *
	 *
	 * @param array $options
	 * @todo extra SET_OPT Parameter hier definieren
	 */
	public function __construct (array $options)
	{
		if (isset($options['url']))
		{
			$this->connect = curl_init($options['url']);
		}
		else
		{
			throw new Exception('url parameter is required');
		}

		if(isset($options['methode']) && $options['methode']=='POST')
		{
			curl_setopt($this->connect, CURLOPT_POST, 1);
		}
	}

	/**
	 * Sendet Daten an den Server und parst den Output
	 * 
	 * @param string $data      Daten die gesendet sollen, bei GET var=value&var=value
	 * @param string $method    Methode, aktuell werden POST/GET unterst�tzt
	 * @param boolean $output   Gibt an, ob die Ausgabe geparst werden soll
	 * 
	 * @return array
	 */
	public function send_data($data, $method='POST', $output=true)
	{
	    #print_r($data);
		// Methodenparameter
		switch ($method)
		{
			case 'POST':
				curl_setopt($this->connect, CURLOPT_POST, 1);
				curl_setopt($this->connect, CURLOPT_POSTFIELDS, $data);	
				break;				
		}
		// Outputr�ckgabe
		if($output)
		{
			curl_setopt($this->connect, CURLOPT_RETURNTRANSFER, true);
		}
		//debuging
		#curl_setopt($this->connect, CURLOPT_VERBOSE, 1);
		// Request senden
		$this->result = curl_exec($this->connect);
		// Verbindung Schliessen
		curl_close($this->connect);
	}
	/**
	 * Gibt die Ausgabe der Anfrage zur�ck
	 * 
	 * @return array
	 */
	public function get_data()
	{	
	    if(!is_null($this->result))
	    {
		    return $this->result;	
	    }
	}
}

?>