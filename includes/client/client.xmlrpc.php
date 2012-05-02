<?php
/**
 * Client für das XMLRPC Service
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
class client_xmlrpc
{
    /**
     * Art der Kommunikation
     * 
     * vorerst werden nur console und http unterstützt
     *
     * @var string
     */
    private $protocol = 'http';
    /**
     * Generierung des HTTP Headers
     *
     * Gibt an ob der HTTP Header versendet und ausgewertet werden soll.
     *
     * @var boolean
     */
    private $http = null;
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
     * URL des Servers.
     * 
     * Nur bei http-Protokoll relevant
     *
     * @var string
     */
    private $url = null;
    /**
     * Abfrage die an den Server geschickt wird ( in XML )
     *
     * @var string
     */
    private $request = null;
    /**
     * Antwort was von dem Server ( in XML )
     *
     * @var string
     */
    private $response = null;
    /**
     * Ausgabekodierungsoptionen
     *
     * @var array
     */
    private $request_options = array(
        'encoding' => 'utf-8'
    );
    /**
     * Konstruktor der Klasse
     * 
     * beim gestetzten URL Parameter werden $this->host und $this->port gegebenenfalls �berschrieben 
     *
     * @param array $options
     */
    public function __construct (array $options)
    {
        $this->host = isset($options['host']) ? $options['host'] : $this->host;
        $this->port = isset($options['port']) ? $options['port'] : $this->port;
        $this->http = isset($options['http_header']) ? $options['http_header'] : true; 
        // falls URL angegeben wurde, handelt es sich um http-Protokol
        if (isset($options['url']))
        {
            // URL auseinander nehmen
            $url = parse_url($options['url']);
            // Zielurl bestimmen
            $this->url = isset($url['query']) ? $url['path'] . '?' . $url['query'] : $url['path'];
            // Protokoll setzen
            $this->protocol = $url['scheme'];
            // Port setzen, default ist 80
            $this->port = isset($url['port']) ? $url['port'] : 80;
            // Host setzen
            $this->host = $url['host'];
        }
        if (isset($options['request']) && is_array($options['request']))
        {
            $this->request_options = array_merge($this->request_options, $options['request']);
        }
    }
    /**
     * Ruft eine Methode auf dem Server auf
     * 
     * Diese Methode fügt alle Aufrufe in Form von $client->Methode('param1', 'param2)
     * und ruft diese auf dem Server auf, das Ergebnis wird dabei als PHP Variablen zur�ck geliefert
     *
     * @param string $name	Name der Methode auf dem Zielserver
     * @param array $params Parameter der Methode
     * @return mixed
     */
    public function __call ($name, array $params=array())
    {
        $this->request = xmlrpc_encode_request($name, $params, $this->request_options);
       # print_r($this->request);
        return $this->_do_call();
    }
    /**
     * Hilfsfunktion für die Aufrufe auf dem Zielserver
     *
     * @return mixed
     */
    private function _do_call ()
    {
        // Fehlermeldungen definieren
        $errno = $errstr = null;
        if(!$this->http)
        {
	        // Verbindung herstellen
	        if (! $fp = @fsockopen($this->host, $this->port, $errno, $errstr))
	        {
	            throw new Exception($errstr, $errno);
	        }
	        // Daten senden
	        if (! fputs($fp, $this->request, strlen($this->request)))
	        {
	            throw new Exception("Write Error");
	        }
	        // Ergebnis lesen
	        $this->response = '';
	        while (! feof($fp))
	        {
	            $this->response .= fgets($fp);
	        }
	        // Verbindung schliessen
	        fclose($fp);
        }
        else
        {
        	// make URL
        	$url = $this->protocol."://".$this->host.":".$this->port.$this->url;
        	// init class
        	$http = new client_http(array('url'=>$url));
        	// Send request
        	$http->send_data($this->request);
        	// get response
        	$this->response=$http->get_data();
        }

        // Daten in die entsprechenden Werte umwandeln
        $data = xmlrpc_decode($this->response);
        // Auf Fehler überprüfen
        if (is_array($data) && xmlrpc_is_fault($data))
        {
            throw new Exception($data['faultString'], $data['faultCode']);
        }
        // Alles ausgeben
        return $data;
    }
    /**
     * Gibt die Liste aller Methoden die auf dem Server existieren
     *
     * @return array
     * @todo die Methode muss auf dem Server implementiert werden
     */
    public function __getFunctions ()
    {
        return $this->__call('system.listMethods');
    }
    /**
     * Gibt die letzte Abfrage aus
     *
     * @return string
     */
    public function __getLastRequest ()
    {
        return $this->request;
    }
    /**
     * Gibt das letzte Antwort aus
     *
     * @return string
     */
    public function __getLastResponse ()
    {
        return $this->response;
    }
}
?>
