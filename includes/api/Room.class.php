<?php

namespace includes\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */

class Room
{
    /**
     * ID des Raumes (von dem Modul)
     * @var int
     */
    private $id;
    /**
     * Name des Raumes
     * @var string
     */
    private $name;
    /**
     * Beschreibung
     * @var string
     */
    private $description;
    /**
     * Gerüte im Raum
     * @var array
     */
    private $channels=array();
    /**
     * Modul des Raumes
     * @var string
     */
    private $module;
    /**
     * Konstruktor
     * 
     * @param mixed $room
     */
    function __construct ($room)
    {
        // konvertierung von Array to Object
        if(is_array($room))
        {
            $room = (object) $room;
        }
            // Methoden laden
        $methods=get_class_vars(get_class($this));
        // Prüfen ob die Variablen gesetzt sind
        foreach($methods as $method => $value)
        {
            if(isset($room->$method))
            {
                $this->$method = $room->$method;
            }
        }
    }
    /**
     * Gibt den Namen zurück
     */
    public function getName()
    {
        return utf8_encode($this->name);
    }
    /**
     * Gibt die ID des Raumes zurück
     */
    public function getID()
    {
        return $this->id;   
    }
    
    public function getDescription()
    {
        return utf8_encode($this->description);
    }

    public function getModule()
    {
        return $this->module;
    }
    public function getChannels()
    {
        return $this->channels;
    }    
}

?>