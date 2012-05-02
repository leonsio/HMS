<?php
/**
 * Client für JSON Abragen
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * @subpackage    Client
 * @version       1.0
 * @id $Id$
 * 
 */
class client_json
{
    /**
     * Art der Kommunikation
     * 
     * vorerst werden nur http unterst�tzt
     *
     * @var string
     */
    private $protocol = 'http';
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
    private $port = 80;
    /**
     * API-URL der Homematic.
     * 
     *
     * @var string
     */
    protected $url = '/';
    /**
     * Abfrage die an den Server geschickt wird ( in JSON )
     *
     * @var string
     */
    private $request = null;
    /**
     * Antwort was von dem Server ( in JSON )
     *
     * @var string
     */
    private $response = null;
    /**
     * Konstruktor der Klasse
     * 
     * beim gestetzten URL Parameter werden $this->host und $this->port gegebenenfalls �berschrieben 
     *
     * @param array $options
     */
    public function __construct (array $options)
    {
        $this->host = @$options['host'];
        $this->port = isset($options['port']) ? $options['port'] : 80 ;
        // falls URL angegeben wurde, diese parsen und host/port ermitteln
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
	}
    /**
     * Ruft eine Methode auf dem Server auf
     * 
     * Diese Methode f�gt alle Aufrufe in Form von $client->Methode('param1', 'param2)
     * und ruft diese auf dem Server auf, das Ergebnis wird dabei als PHP Variablen zur�ck geliefert
     *
     * @param string $name	Name der Methode auf dem Zielserver
     * @param array $params Parameter der Methode
     * @return array
     */
    public function __call ($name, array $params=array())
    {
    	// Nachricht generieren
    	$message = array('method'=>$name, 'params'=> $params[0]);
    	// Nachricht kodieren
        $this->request = json_encode($message);
        // Nachricht senden
        return $this->_do_call();
    }
    /**
     * Hilfsfunktion f�r die Aufrufe auf dem Zielserver
     *
     * @todo Errors abfangen
     * @return array
     */
    private function _do_call ()
    {
        // Fehlermeldungen definieren
        $errno = $errstr = null;
        // make URL
        $url = $this->protocol."://".$this->host.":".$this->port.$this->url;
        // init class
        $http = new client_http(array('url'=>$url));
        // Send request
        $http->send_data($this->request);
        // get response
        $this->response=$http->get_data();
        // Daten in die entsprechenden Werte umwandeln
        $data = json_decode($this->response);
        // Auf Fehler �berpr�fen, ausgabe zur�ckgeben
        if (json_last_error() == JSON_ERROR_SYNTAX)
        {
            throw new Exception($this->response);
        }
        if(isset($data->error->name))
        {
        	throw new Exception($data->error->message, $data->error->code);
        }
        // Alles ausgeben
        return $data;
    }
    /**
     * Gibt die Liste aller Methoden die auf dem Server existieren
     * 
     * Ist bei jedem Service anders, deswegen muss die Methode in der Child Klasse �berschrieben werden
     *
     * @return array
     
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
