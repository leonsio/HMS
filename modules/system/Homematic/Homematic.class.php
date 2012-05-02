<?php
namespace modules\system\Homematic;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
use modules\system\Homematic\lib\api;

final class Homematic implements \interface_system
{

    /**
     * Instanz der Klasse
     *
     * @var modules\system\Homematic\Homematic
     */
    private static $instance = null;

    /**
     * Aktuelle Homematic Session
     *
     * @var api\Session
     */
    private $session = null;

    /**
     * Cache Objekt
     *
     * @var Cache
     */
    private $cache = null;

    /**
     * Temporäre Daten
     *
     * @var array
     */
    private $data = null;

    /**
     * Konfigurationsparameter
     *
     * @var object
     */
    protected $config = null;

    /**
     * Konstruktor der Klasse
     *
     * @uses \Config
     * @uses Cache
     */
    private function __construct ()
    {
        // Konfig laden
        $this->config = \Config::getModuleConfig('Homematic');
        // Cache laden
        $this->cache = new Cache($this->config->server->cache);
    }

    /**
     * Singelton Methode
     *
     * @see interface_system::init()
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
     * Liefert eine Liste mit Räumen
     *
     * @see interface_system::getRooms()
     * @uses api\Room
     *      
     * @return \includes\api\Room[]
     */
    public function getRooms ()
    {
        // Make Array of Rooms
        $hm_rooms = array();
        // Cache is Active?
        if (! $this->cache->isActive())
        {
            // Load Rooms
            $rooms = new api\Room();
            // Fill Array
            $roomlist = $rooms->getAll();
        }
        else
        {
            // Cache is actual?
            if (! $this->cache->isActual())
            {
                $this->cache->reload();
            }
            // Load Rooms
            $roomlist = $this->cache->selectAll('Room');
        }
        // Process Object
        foreach ($roomlist as $room)
        {
            $room->module = get_class();
            $hm_rooms[] = new \includes\api\Room($room);
        }
        // Return Data
        $this->data['rooms'] = $hm_rooms;
        return $hm_rooms;
    }

    /**
     *
     * @param $id int           
     *
     * @see interface_system::getRoomByID()
     * @return \includes\api\Room
     */
    public function getRoomByID ($id)
    {
        // Cache is Active?
        if (! $this->cache->isActive())
        {
            // Load Rooms
            $rooms = new api\Room();
            // Fill Array
            $room = $rooms->get($id);
        }
        else
        {
            // Cache is actual?
            if (! $this->cache->isActual())
            {
                $this->cache->reload();
            }
            // Load Rooms
            $room = $this->cache->select($id, 'Room');
        }
        // Module setzen
        $room->module = get_class();
        // Return Data
        return new \includes\api\Room($room);
    }

    /**
     *
     * @param $name string           
     *
     * @see interface_system::getRoomByName()
     * @return \includes\api\Room
     */
    public function getRoomByName ($name)
    {
        if (isset($this->data['rooms']))
        {
            $roomlist = $this->data['rooms'];
        }
        else
        {
            $roomlist = $this->getRooms();
        }
        foreach ($roomlist as $room)
        {
            if ($room->getName() == $name)
            {
                return $room;
            }
        }
    }

    /**
     * Liefert eine Liste mit Geräten
     *
     * @see interface_system::getDeviceList()
     * @uses api\Device
     * @uses api\Homematic
     * @return \includes\api\Device[]
     */
    public function getDeviceList ()
    {
        // Make Array of Rooms
        $hm_devs = array();
        // Cache is Active?
        if (! $this->cache->isActive())
        {
            // Load Rooms
            $devs = new api\Device();
            // Fill Array
            $devlist = $devs->getAll();
        }
        else
        {
            // Cache is actual?
            if (! $this->cache->isActual())
            {
                $this->cache->reload();
            }
            // Load Rooms
            $devlist = $this->cache->selectAll('Device');
        }
        // Process Object
        foreach ($devlist as $device)
        {
            $device->module = get_class();
            $hm_devs[] = new \includes\api\Device($device);
        }
        // Return Data
        $this->data['device'] = $hm_devs;
        return $hm_devs;
    }

    /**
     *
     * @param $id int           
     *
     * @see interface_system::getDeviceByID()
     * @return \includes\api\Device
     */
    public function getDeviceByID ($id)
    {
        // Cache is Active?
        if (! $this->cache->isActive())
        {
            // Load Rooms
            $devs = new api\Device();
            // Fill Array
            $dev = $devs->get($id);
        }
        else
        {
            // Cache is actual?
            if (! $this->cache->isActual())
            {
                $this->cache->reload();
            }
            // Load Rooms
            $dev = $this->cache->select($id, 'Device');
        }
        // Modul bestimmen
        $dev->module = get_class();
        // Return Data
        return new \includes\api\Device($dev);
    }

    /**
     *
     * @param $name string           
     *
     * @see interface_system::getDeviceByName()
     * @return \includes\api\Device
     */
    public function getDeviceByName ($name)
    {
        if (isset($this->data['device']))
        {
            $devlist = $this->data['device'];
        }
        else
        {
            $devlist = $this->getDeviceList();
        }
        foreach ($devlist as $dev)
        {
            if ($dev->getName() == $name)
            {
                return $dev;
            }
        }
    }

    /**
     *
     * @param $room_id int           
     *
     * @see interface_system::getDeviceInRoomByID()
     * @todo ggf. in eigenständige Funktion auslagern
     * @return \includes\api\Device[]
     */
    public function getDeviceInRoomByID ($room_id)
    {
        // Platzhalter für die Ausgabe
        $devs = array();
        // Raum laden
        $room = $this->getRoomByID($room_id);
        // Geräte laden
        if (isset($this->data['device']))
        {
            $devlist = $this->data['device'];
        }
        else
        {
            $devlist = $this->getDeviceList();
        }
        // Channels im Raum laden
        $channels = $room->getChannels();
        // Device suchen
        foreach ($devlist as $device)
        {
            foreach ($device->getChannels() as $channel)
            {
                if (in_array($channel->id, $channels))
                {
                    $devs[] = $device;
                }
            }
        }
        // Ausgabe
        return $devs;
    }

    /**
     *
     * @param $device \Device           
     *
     * @see interface_system::getDeviceState()
     */
    public function getDeviceState ($device)
    {
        $return = array();
        $channels = $device->getChannels();
        $interface = new api\Homematic();
        foreach ($channels as $channel)
        {    
            if ($channel->isReadable)
            {
                $desc = $interface->getDeviceDescription($device->getInterface(), $channel->address);
                if (in_array('VALUES', $desc->paramsets))
                {
                    $paramset = $interface->getParamset($device->getInterface(), $channel->address, "VALUES");
                    $channel->values = $paramset;
                    $return[] = $channel;
                }
            }
        }
        // Ausgabe
        return $return;
    }

    /**
     *
     * @param $device \Device           
     * @param $key string           
     * @param $value mixed           
     *
     * @see interface_system::setDeviceState()
     */
    public function setDeviceState ($device, $key = null, $value = null)
    {
    
    }

    /**
     * Destruktor der Klasse, beendet die Session
     *
     * @uses api\Session
     */
    public function __destruct ()
    {
        // Session muss beendet werden
        if (defined('HM_SESSION'))
        {
            // Session besorgen
            $this->session = api\Session::init();
            // Benutzer abmelden
            $this->session->logout();
        }
    }

    /**
     * Verbieten von Clonen
     */
    private function __clone ()
    {
    }
}

?>