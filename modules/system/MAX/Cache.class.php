<?php
namespace modules\system\MAX;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
/**
 * Klasse für die temporäre Speicherung der max Räume/Channels/Geräte in
 * die Datenbak
 * Die Datenbank wird sowohl von XML-RPC Server (Interface.init) als auch von
 * der HMS aktualisiert
 */
class Cache
{

    /**
     * Lädt den gesamten Cache neu ein
     *
     * @var array
     */
    private $types = array('Room' => 'getAll', 'Device' => 'getAll');
    /**
     * Gecachte Tabellen
     * 
     * @var array
     */
    private $cache_tables = array( 'Device' => 'max_devices', 'max_room_device', 'Room'=>'max_rooms');
    /**
     * Verbindung zu der Datenbank
     *
     * @var \class_db
     */
    private $db = null;

    /**
     * Konfigurationsparameter
     *
     * @var \SimpleXMLElement
     */
    private $config = null;

    /**
     * Konstruktor der Klasse
     *
     * @param $config \SimpleXMLElement           
     */
    public function __construct ($config)
    {
        // Set Config
        $this->config = $config;
        // Connect to DB
        $hms_config = new \config_xml();
        $this->db = new \Db($hms_config->getCfgValue('DB'));
    }

    /**
     * Prüft ob die Datenbank aktuell ist
     */
    public function isActual ()
    {
        // Cache Timestamp laden
        $cachetime = $this->db->query("SELECT timestamp FROM hms_cache WHERE module='MAX'",1);
        // Ist Cache noch aktuell?
        if ((time() - $cachetime->timestamp) > $this->config->time)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Prüft ob Cache aktive ist
     */
    public function isActive ()
    {
        if ($this->config->time == 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * Fragt ein Element aus der Datenbank ab
     * 
     * @param int $id
     * @param string $type
     */
    public function select ($id, $type)
    {
        // Typ bestimmen
        switch ($type)
        {
            case 'Room':
                // Datenbankabfrage
                $room = $this->db->query(
                        "SELECT * FROM max_rooms WHERE id=" . $id, 1);               
                // Channel Liste
                $channellist = $this->db->query(
                        "SELECT channelid FROM max_room_device WHERE roomid=" .
                        $room->id);
                // Alle Einträge durchgehen und als Objekt merken
                while ($channel = $this->db->fetch($channellist))
                {
                    $room->channels[] = $channel->channelid;
                }   
                // Ausgabe 
                return $room;           
                break;
            case 'Device':
                // Datenbankabfrage
                $device = $this->db->query(
                        "SELECT * FROM max_devices WHERE id=" . $id, 1);
                //Result
                return $device;
                break;
        }
    }

    /**
     * Gibt einen Auszug aus der Datenbank zurück
     *
     * @param $type string
     *            Tabellenname
     * @return array Array von Objekten
     */
    public function selectAll ($type)
    {
        $data = array();
        // Typ bestimmen
        switch ($type)
        {
            // Alle Räume laden
            case 'Room':
                $data = array();
                // Datenbankabfrage
                $result = $this->db->query("SELECT * FROM max_rooms");
                // Alle Einträge durchgehen und als Objekt merken
                while ($room = $this->db->fetch($result))
                {
                    $result2 = $this->db->query("SELECT channelid FROM max_room_device WHERE roomid=".$room->id);
                    while ($channel = $this->db->fetch($result2))
                    {
                        $room->channels[]=$channel->channelid;
                    } 
                    // Ergebniss schreiben
                    $data[] = new \includes\api\Room($room);                   
                }
                break;
            case 'Device':
                $result = $this->db->query("SELECT * FROM max_devices");
                // Alle Einträge durchgehen und als Objekt merken
                while ($device = $this->db->fetch($result))
                {
                    // Ergebniss schreiben
                    $data[] = new \includes\api\Device($device);
                }
                break;
        }
        // Ausgabe
        return $data;
    }

    /**
     * Fügt einen einzigen Datensatz in die Datenbank ein
     *
     * @param $id int           
     * @param $data array           
     * @param $type string           
     */
    public function insert ($data, $type)
    {
        // Datenbankstruktur
        $schema = $this->getStructure($type);
        // Daten verarbeiten
        $entry = null;
        // in Array umwandeln
        if(is_object($data)) $data = (array) $data;
        // Query zusammenbauen
        foreach ($data as $key => $val)
        {
            // array als string speichern
            if(is_array($val) && !is_object($val[0]))
            {
                $val = implode(',', $val);
            }
            if (in_array($key, $schema))
            {
                $entry[$key] = $val;
            }
        }
        // in die Datenbank schrieben
        $this->db->query(
                sprintf("INSERT INTO {$type} (`%s`)  VALUES ('%s') ", 
                        implode("`, `", array_keys($entry)), 
                        utf8_decode(implode("','", $entry))));
        
        // Aktuelle Zeit setzen
        $this->updateCache();
    }

    /**
     * Fügt einen neuen Datensatz in die Datenbank ein
     *
     * @param $data array           
     * @param $type string           
     */
    public function insertAll ($data, $type)
    {
        // Daten verarbeiten
        foreach ($data as $value)
        {
            if($type == "max_rooms")
            {
                // Channels verarbeiten
                $channelIds = $value->getChannels();
                foreach($channelIds as $id)
                {
                    $this->db->query(sprintf("INSERT INTO max_room_device VALUES('%s', '%s')", $value->getID(), $id));
                }
                // Cleanup
                unset($value->channelIds);
            }
            // Variablen zusammen bauen
            $entry['name'] = $value->getName();
            $entry['id'] = $value->getID();
            if($value instanceof \api_Device)
            {
                $entry['serial'] = $value->getSerial();
            }
            // in die Datenbank schrieben
            $this->db->query(
                    sprintf("INSERT INTO {$type} (`%s`)  VALUES ('%s') ", 
                            implode("`, `", array_keys($entry)), 
                            utf8_decode(implode("','", $entry))));
        }
        // Aktuelle Zeit setzen
        $this->updateCache();
    }
    /**
     * 
     * @param unknown_type $id
     * @param unknown_type $data
     * @param unknown_type $type
     * @todo
     */
    public function update ($id, $data, $type)
    {
        // Aktuelle Zeit setzen
        $this->updateCache();
    }
    /**
     * 
     * @param unknown_type $data
     * @param unknown_type $type
     */
    public function updateAll ($data, $type)
    {
        // Aktuelle Zeit setzen
        $this->updateCache();
    }

    /**
     * Lädt alle Daten von der max in den Cache
     */
    public function reload ()
    {
        // alte Daten entfernen
        foreach(array_values($this->cache_tables) as $table)
        {
            $this->db->query("TRUNCATE {$table}");
        }
        // Alle typen durchgehen
        foreach ($this->types as $class => $method)
        {
            // Name der Klasse bestimmen
            $classname = '\modules\system\MAX\lib\api\\' . $class;
            // Klasse initalisieren
            $instance = new $classname();
            // Methode aufrufen
            $data = $instance->$method();
            // Daten schreiben
            $this->insertAll($data, $this->cache_tables[$class]);
        }
        // Aktuelle Zeit setzen
        $this->updateCache();
    }

    /**
     * Gibt die Beschreibung der Datenbank/Datessatzes zurück
     *
     * @param $type string           
     */
    private function getStructure ($table)
    {
        // Default
        $schema = array();
        // Schema abfragen
        $result = $this->db->query("DESC $table");
        // Array zusammenbauen
        while ($res = $this->db->fetch($result))
        {
            $schema[] = $res->Field;
        }
        // Ausgabe
        return $schema;
    }

    private function updateCache ()
    {
        // Aktuelle Zeit setzen
        $this->db->query("UPDATE hms_cache SET timestamp=" . time()." WHERE module='MAX'");
    }
}

?>