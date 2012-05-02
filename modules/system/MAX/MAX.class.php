<?php
namespace modules\system\MAX;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
use modules\system\MAX\lib\api;

/**
 *
 * @author leonsio
 *        
 *        
 */
class MAX implements \interface_system
{

    /**
     * Instanz der Klasse
     *
     * @var modules\MAX\MAX
     */
    private static $instance = null;

    /**
     * Konfigurationsparameter
     *
     * @var object
     */
    protected $config = null;
    /**
     * Cache Objekt
     *
     * @var Cache
     */
    private $cache = null;
    /**
     * Konstruktor der Klasse
     *
     * @uses \Config
     * @uses Cache
     */
    private function __construct ()
    {
        // Konfig laden
        $this->config = \Config::getModuleConfig('MAX');
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
     *
     * @see interface_system::getRooms()
     * @return \includes\api\Room[]
     *
     */
    public function getRooms ()
    {       
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
        // Ausgabe
        return $roomlist;
    }

    /**
     *
     * @see interface_system::getDeviceList()
     *
     */
    public function getDeviceList ()
    {
        // Load Devices
        $devs = new api\Device();
        // Fill Array
        $devlist = $devs->getAll();
        // Ausgabe
        return $devlist;    
    }

    /**
     *
     * @param $id int           
     *
     * @see interface_system::getDeviceByID()
     *
     */
    public function getDeviceByID ($id)
    {
        // Load Devices
        $devs = new api\Device();
        // Fill Array
        $devlist = $devs->get($id);
        // Ausgabe
        return $devlist;    
    }

    /**
     *
     * @param $name string           
     *
     * @see interface_system::getDeviceByName()
     *
     */
    public function getDeviceByName ($name)
    {
        $devlist = $this->getDeviceList();
        foreach($devlist as $device)
        {
            if($device->getName() == $name)
            {
                return $device;
            }
        }
    }

    /**
     *
     * @param $id int           
     *
     * @see interface_system::getRoomByID()
     *
     */
    public function getRoomByID ($id)
    {
        // Load Rooms
        $rooms = new api\Room();
        // Fill Array
        $room = $rooms->get($id);
        // Ausgabe
        return $room;
    }

    /**
     *
     * @param $name string           
     *
     * @see interface_system::getRoomByName()
     * @return \includes\api\Room
     *
     */
    public function getRoomByName ($name)
    {
        // Fill Array
        $rooms = $this->getRooms();
        foreach ($rooms as $room)
        {
            if ($room->getName() == $name)
            {
                return $room;
            }
        }
    }

    /**
     *
     * @param $room_id int           
     *
     * @see interface_system::getDeviceInRoomByID()
     *
     */
    public function getDeviceInRoomByID ($room_id)
    {
        // Platzhalter für die Ausgabe
        $devs = array();
        // Raum laden
        $room = $this->getRoomByID($room_id);
        // Channels im Raum laden
        $channels = $room->getChannels();
        // Device suchen
        foreach($channels as $channel)
        {
            $devs[] = $this->getDeviceByID($channel);
        }
    
        // Ausgabe
        return $devs;    
    }

    /**
     *
     * @param $device \includes\api\Device           
     *
     * @see interface_system::getDeviceState()
     */
    public function getDeviceState ($device)
    {
        $return = array();
        $base = lib\api\Base::init();
        $state = $base->getDeviceData();
        return (object) $state[$device->getID()];
    }

    /**
     *
     * @param $device \includes\api\Device           
     * @param $key string           
     * @param $value mixed           
     *
     * @see interface_system::setDeviceState()
     */
    public function setDeviceState ($device, $key = null, $value = null)
    {
    
    }

}

?>