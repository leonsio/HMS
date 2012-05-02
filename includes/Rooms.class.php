<?php

/** 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 * 
 */

class Rooms
{
    /**
     * 
     * @var \class_db
     */
    private $db = null;
    function __construct (\HMS $parent)
    {
        // Datenbank initalisieren
        $this->db = new Db($parent->getCfgValue('DB'));
    }
    /**
     * 
     */
    public function getFloors()
    {
        $floors = array();
        $result = $this->db->query("SELECT * FROM hms_floors");
        while($floor = $this->db->fetch($result))
        {
            $floors[$floor->id] = $floor->name;
        }
        // AUsgabe
        return $floors;
        
    }
    /**
     * 
     * @param unknown_type $id
     */
    public function getRoomInFloor($id)
    {
        $rooms = array();
        $result = $this->db->query(sprintf("SELECT * FROM hms_rooms WHERE floor=%d", $id));
        while($room = $this->db->fetch($result))
        {
            $rooms[$room->id] = $room->name;
        }
        // Ausgabe
        return $rooms;
    }
    /**
     * 
     * @param int $id
     * @return \includes\api\Room[]
     */
    public function getModuleRoomsInRoom($id)
    {
        $rooms = array();
        $result = $this->db->query(sprintf("SELECT * FROM hms_rooms_map WHERE hms_room=%d", $id));
        while($room = $this->db->fetch($result))
        {
            $module = $room->module;
            $class = $module::init();
            $data = $class->getRoomByID($room->module_room);
            $rooms[] = $data;
        }
        // Ausgabe
        return $rooms;        
    }
    
    public function getRoomByID($id)
    {
        return $this->db->query(sprintf("SELECT * FROM hms_rooms WHERE id=%d", $id), 1);
    }
}

?>