<?php

/**
* Interface für Haussteuerungsgeräte
* 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
*/

interface interface_system
{
    /**
     * Initalisiert die Klasse
     */
    public static function init();
    ###################
    #    Räume        #
    ###################
    /**
     * 
     */
    public function getRooms();
    /**
     * 
     * @param string $name
     */
    public function getRoomByName($name);
    /**
     * 
     * @param int $id
     */
    public function getRoomByID($id);
    
    ################
    #    Geräte    #
    ################
    /**
     * 
     */
    public function getDeviceList();
    /**
     * 
     * @param int $room_id
     */
    public function getDeviceInRoomByID($room_id);
    /**
     * 
     * @param int $id
     */
    public function getDeviceByID($id);
    /**
     * 
     * @param string $name
     */
    public function getDeviceByName($name);
    /**
     * @param \Device $device
     */
    public function getDeviceState($device);
    /**
     * @param \Device $device
     * @param string $key
     * @param mixed $value
     */
    public function setDeviceState($device, $key=null, $value=null);    
    
        
}

?>