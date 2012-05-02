<?php
namespace modules\system\Homematic;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
/**
 * Klasse für die temporäre Speicherung der Homematic Räume/Channels/Geräte in
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
    private $cache_tables = array('homematic_device_channel', 'Device' => 'homematic_devices', 'homematic_room_device', 'Room'=>'homematic_rooms');
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
        $cachetime = $this->db->query("SELECT timestamp FROM hms_cache WHERE module='Homematic'",1);
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
                        "SELECT * FROM homematic_rooms WHERE id=" . $id, 1);               
                // Channel Liste
                $channellist = $this->db->query(
                        "SELECT channelid FROM homematic_room_device WHERE roomid=" .
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
                        "SELECT * FROM homematic_devices WHERE id=" . $id, 1);
                // Channel Liste
                $channellist = $this->db->query(
                        "SELECT * FROM homematic_device_channel WHERE deviceId=" .
                                 $device->id);
                // Alle Einträge durchgehen und als Objekt merken
                while ($channel = $this->db->fetch($channellist))
                {
                    $device->channels[] = $channel;
                }
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
                $result = $this->db->query("SELECT * FROM homematic_rooms");
                // Alle Einträge durchgehen und als Objekt merken
                while ($room = $this->db->fetch($result))
                {
                    $result2 = $this->db->query("SELECT channelid FROM homematic_room_device WHERE roomid=".$room->id);
                    while ($channel = $this->db->fetch($result2))
                    {
                        $room->channels[]=$channel->channelid;
                    } 
                    // Ergebniss schreiben
                    $data[] = $room;                   
                }
                break;
            case 'Device':
                $result = $this->db->query("SELECT * FROM homematic_devices");
                // Alle Einträge durchgehen und als Objekt merken
                while ($device = $this->db->fetch($result))
                {
                    // Channels sammeln
                    $result2 = $this->db->query("SELECT * FROM homematic_device_channel WHERE deviceId=".$device->id);
                    while ($channel = $this->db->fetch($result2))
                    {
                        $device->channels[] = $channel;
                    }
                    // Ergebniss schreiben
                    $data[] = $device;
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
        // Sonderbehandlung Device
        if ($type == 'homematic_devices')
        {
            foreach ($data as $device)
            {
               
                // Second step insert channels
                if(is_array($device->channels))
                {
                    $this->insertAll($device->channels, 'homematic_device_channel');
                    unset($device->channels);
                }
                // second step import device
                $this->insert($device, 'homematic_devices');
            }
            return;
        }
        // Datenbankstruktur
        $schema = $this->getStructure($type);
        // Daten verarbeiten
        foreach ($data as $value)
        {
            if(is_object($value)) $value = (array) $value;
            if($type == "homematic_rooms")
            {
                // Channels verarbeiten
                $channelIds = $value['channelIds'];
                foreach($channelIds as $id)
                {
                    $this->db->query(sprintf("INSERT INTO homematic_room_device VALUES(%d, %d)", $value['id'], $id));
                }
                // Cleanup
                unset($value->channelIds);
            }
            $entry = null;
            // Query zusammenbauen
            foreach ($value as $key => $val)
            {
                // array als string speichern
                if(is_array($val) && (isset($val[0]) && !is_object($val[0])))
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
     * Lädt alle Daten von der Homematic in den Cache
     */
    public function reload ()
    {
        // Session initalisieren
        $session = \modules\system\Homematic\lib\api\Session::init();
        // alte Daten entfernen
        foreach(array_values($this->cache_tables) as $table)
        {
            $this->db->query("TRUNCATE {$table}");
        }
        // Alle typen durchgehen
        foreach ($this->types as $class => $method)
        {
            // Name der Klasse bestimmen
            $classname = '\modules\system\Homematic\lib\api\\' . $class;
            // Klasse initalisieren
            $instance = new $classname($session);
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
        $this->db->query("UPDATE hms_cache SET timestamp=" . time()." WHERE module='Homematic'");
    }
}

?>