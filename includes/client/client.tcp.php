<?php
/**
 * Client für TCP Verbindungen
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Client
 * 	@version 1.0
 *  @id $Id$
 *
 */
class client_tcp
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
     * Zieladresse des Servers
     *
     * @var string
     */
    private $host = null;
    /**
     * Port auf dem der Server lauscht
     *
     * @var int
     */
    private $port = null;    
	/**
	 * Konstruktor der Klasse
	 *
	 *
	 * @param array $options
	 */
	public function __construct (array $options)
	{
	    // set options
	    $this->host = isset($options['host']) ? $options['host'] : $this->host ;
	    $this->port = isset($options['port']) ? $options['port'] : $this->port ;
	    // make connect
	    if($this->host != '' && $this->port != '' )
	    {
	    	// Fehlermeldungen definieren
	    	$errno = $errstr = null;
	    	// Verbindung herstellen
	    	if (! $this->connect = @fsockopen($this->host, $this->port, $errno, $errstr, 5))
	    	{
	    		throw new Exception($errstr, $errno);
	    	}
	    }
	    else
	    {
	        throw new Exception('Host and Port must be set');
	    }
    }
    /**
     * Sendet Daten an den Server und parst den Output
     *
     * @param string $data      Daten die gesendet sollen
     * @param boolean $output   Gibt an, ob die Ausgabe geparst werden soll
     *
     * @return array
     */
    public function send_data($query=null)
    {
    	// Daten senden
    	if (!is_null($query) && ! fputs($this->connect, $query, strlen($query)))
    	{
    		throw new Exception("Write Error");
    	}
    	// Ergebnis lesen
    	$this->response = '';
    	while (! feof($this->connect))
    	{
    		$this->response .= fgets($this->connect);
    	}
    	// Verbindung schlie�en
    	fclose($this->connect);    	
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