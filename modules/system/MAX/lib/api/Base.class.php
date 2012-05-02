<?php
namespace modules\system\MAX\lib\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */

final class Base
{
    /**
     * Instanz der Klasse
     *
     * @var modules\system\MAX\lib\api\Base
     */
    private static $instance = null;    
	/**
	 * Verbindungsidentifer zu dem Server
	 *
	 * @var ressource
	 */
	private $connect = null;
	/**	
	/**
	 * Konfigurationsparameter
	 *
	 * @var \Config
	 */
	protected $config=null;
	/**
	 * Antwort des Servers als base64
	 * 
	 * @var array
	 */
    private $data = null;
    /**
     * Konstruktor der Klasse
     * 
     * Stellt eine Verbindung zu Cube her und liest die Daten zur sp�ten verarbeitung
     * 
     * @uses \Config
     */
    private function __construct ()
    {
    	// Konfig laden
    	$this->config = \Config::getModuleConfig('MAX');
    	// Fehlermeldungen definieren
    	$errno = $errstr = null;
    	// Verbindung herstellen
    	try 
    	{
		    $this->connect();  
    	}
    	catch (\Exception $e)
    	{
    	    echo 'hier';
    	    // Verbindung war nicht möglich, ggf ist grad ein Connect vorhanden
    	    // @todo Code abfangen
    	    if($e->getCode() == '')
    	    {
    	        // Sekunde warten
    	        sleep(1);
    	        // nochmals probieren
    	        $this->connect();
    	    }
    	}
    	// Daten einlesen
    	$this->get_cube_data();	
    }
    /**
     * Singelton Methode
     *
     * @see interface_home::init()
     */
    public static function init ()
    {
        // Prüfen ob die Instanze berets initalisiert wurde
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        // Instanz zurück geben
        return self::$instance;
    }
    /**
     * Analysiert die M: Nachrichten der Cube Box
     * Liefert Informationen über die Geräte und Räume
     */
    public function getMetaData()
    {
    	// Metadaten bestimmen
        $string = $this->data['M'];        
        // Alles in ein bin-array packen
        $binarr = array_slice(unpack("C*", base64_decode($string)), 2);
        // Anzahl raume bestimmen
        $room_count = array_shift($binarr);
        $metadata=array();
        // Raume Identifizieren
        for($i = 1; $i <= $room_count ; $i++)
        {
        	// Raum Muster
        	$raum_muster =
	        	'C1RoomID/' . 						# 1 byte
	        	'C1Lenght/'.							# 2 byte
	        	'A'.$binarr[1].'RoomName/'.			# =Lengh bytes
	        	'H6RFID/'							# 3 bytes
        		;
        	// Raum einlesen
        	$metadata[$i]=unpack($raum_muster, $this->pack_array($binarr));
        	// array berichtigen 5(= RoomID+Laenge+3*RFID) + Laenge des Namens
        	$binarr = array_slice($binarr, 5+$binarr[1]);
        	// Cleanup
        	unset($metadata[$i]['Lenght']);
        }
        // Anzahl devices bestimmen
        $dev_count=array_shift($binarr);
        // Geraete identifizieren
        for($i = 1; $i <= $dev_count ; $i++)
        {
            // Dev Muster
            $dev_muster =
	            'C1DeviceType/' . 
	            'H6RFID/' .
	            'A10SerialNumber/'.
	            'C1Length/'.
	            'A'.$binarr[14].'DeviceName/'.
	            'C1RoomID'
            	;
            // device einlesen
            $dev=unpack($dev_muster, $this->pack_array($binarr));
            // array berichtigen
            $binarr = array_slice($binarr, 16+$binarr[14]);
            // Cleanup
            unset($dev['Length']);            
            // device einem Raum zuordnen
            $metadata[$dev['RoomID']]['Devices'][] = $dev;
            
        }
        // Rückgabe der Metadaten
        return $metadata;
    }
    /**
     * 
     * @param unknown_type $device
     */
    public function getConfigData($device)
    {
        // return C:
    }
    /**
     * Liefert die Daten des Cubes zurück
     */
    public function getCubeData()
    {
    	// Daten aufteilen
    	$data = explode(',', $this->data['H']);
    	// Basic data
    	$cube['SerialNumber'] 	= $data[0];
    	$cube['RFID']			= $data[1];
    	$cube['Firmware'] 		= $data[2];
    	// Date/Time vorbereiten
    	$date = array_merge(str_split(trim($data[7]), 2), str_split(trim($data[8]), 2));
    	// Datum zusammenbauen			 	
    	$cube['Timestamp'] 		= mktime(
    			hexdec($date[3]), 				# Stunde
    			hexdec($date[4]),				# Minute
    			0,								# Sekunde
    			hexdec($date[1]),				# Monat
    			hexdec($date[2]),				# Tag
    			hexdec($date[0]) 				# Jahr
    			);
    	// Ausgabe
   		return $cube;
    }
    /**
     * Gibt aktuelle Gerätedaten zurück
     * @todo Datum/Zeit bis bei Urlaub anzeigen
     */
    public function getDeviceData()
    {
    	// Geräte Platzhalter
        $devs = array();
        // Daten verarbeiten
        $data = base64_decode($this->data['L']);
        // Pro Gerät durchlaufen (ggf vorerst bin2hex aufrufen und nach 24 teilen)
        foreach(str_split($data, 12) as $device)
        {
			// Platzhalter
        	$null=null;
        	// Unpack data
        	$dev_muster =
	        	'@1/' .			# ggf. Typ des Pakets = 11 bei Stellantrieb?
	        	'H6RFID/' .
	        	'@5/'.
	        	'C1Data1/'.
	        	'C1Data2/'.
	        	'C1Position/'.
	        	'C1Temperature/'.
	        	'H4DateUntil/'.
	        	'H2TimeUntil'
        	;
        	// device einlesen
        	$dev=unpack($dev_muster, $device); 
        	// Kommunikationsstatus
        	$status 	= str_split(str_pad(decbin($dev['Data1']),8,0, STR_PAD_LEFT));
			list($null,$null,$null, $dev["Valid"], $dev["Error"], $dev["isAnswer"], $dev["initialized"] ) = $status;
			// Weitere Parameter
        	$options 	= str_pad(decbin($dev['Data2']),8,0, STR_PAD_LEFT);
      	    list($dev["LowBatt"], $dev["LinkError"],$dev["PanelLock"],$dev["GatewayOK"], $dev["DST"]) = str_split($options);  
      	    // Modus
			switch (substr($options, 6, 2))
			{
				case "00" : 
					$dev["Mode"] = "auto"; 
					break;
				case "01" : 
					$dev["Mode"] = "manu"; 
					break;
				case "10" : 
					$dev["Mode"] = "vacation"; 
					break;
				case "11" : 
					$dev["Mode"] = "boost"; 
					break;
			}	
			// Cleanup
			unset($dev['Data1'], $dev['Data2']); 
			if($dev["Mode"] != 'vacation')
			{
				unset($dev['DateUntil'], $dev['TimeUntil']);
			}   			
        	// Berichtigungen
        	$dev['Temperature'] = $dev['Temperature'] / 2;
        	// Gerät merken
        	$devs[$dev['RFID']] = $dev;
        }
        // Ausgabe
        return $devs;
    }
	/**
	 * Verpackt ein binäres array in ein binäres string
	 * 
	 * @param array $arr
	 */
    private function pack_array($arr)
    {
    	return call_user_func_array('pack',array_merge(array("C*"),$arr));
    } 
    /**
     * Destruktor der Klasse
     */
    public function __destruct()
    {
        @fclose($this->connect);
    }
	/**
	 * Stellt eine Verbindung zu MAX Cube her
	 * @throws \Exception
	 */
    private function connect()
    {
    	// Vorherige Verbindung schliessen
    	if(is_resource($this->connect))
    	{
    		@fclose($this->connect);
    		$this->connect=null;
    	}
    	// Fehlermeldungen definieren
    	$errno = $errstr = null;    	
    	// Verbindung herstellen
    	if (! $this->connect = @fsockopen((string) $this->config->server->host, (string) $this->config->server->port, $errno, $errstr, 5))
    	{
    		throw new \Exception($errstr, $errno);
    	}    	
    }
    /**
     * Liest die Daten des Cubes nach einer Verbindung
     */
    private function get_cube_data()
    {
    	if(!is_resource($this->connect))
    	{
    		throw new \Exception('Keine Verbindung zum Cube hergestellt');
    	}
    	// Timeout einbauen
    	$time = time();
    	// Ausgabe parsen
    	while (! feof($this->connect))
    	{	
    		// Daten einlesen
    		$data = fgets($this->connect);
    		// Nachrichtentyp bestimmen
    		$type=substr($data, 0, 1);
    		// Nachrichten verarbeiten
    		switch ($type)
    		{
    			case 'H':
    				$this->data['H'] = substr($data, 2);
    				break;
    			case 'M':
    				$this->data['M'] = substr($data, 8);
    				break;
    			case 'C':
    				$this->data['C'][substr($data, 2,6)] = substr($data, 9);
    				break;
    			case 'L':
    				$this->data['L'] = substr($data, 2);
    				fclose($this->connect);
    				return;
    		}
    		// Timeout
			if(time() - $time > 3)
			{
				fclose($this->connect);
				break;
			}
    	}   	
    }
}

?>