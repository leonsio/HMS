<?php

namespace includes\api;

/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 */

class Device
{

    private $id;
    private $name;
    private $description;
    private $module;
    private $type;
    private $interface;
    private $channels=array();
    private $options;
    private $serial;
    
    
    function __construct ($device)
    {
        // konvertierung von Array to Object
        if(is_array($device))
        {
            $device = (object) $device;
        }
        // Methoden laden
        $methods=get_class_vars(get_class($this));
        // Prüfen ob die Variablen gesetzt sind
        foreach($methods as $method => $value)
        {
            if(isset($device->$method))
            {
                $this->$method = $device->$method;
            }
        }
    }
    
    public function getName()
    {
        return utf8_encode($this->name);
    }
    
    public function getID()
    {
        return $this->id;   
    }
    
    public function getDescription()
    {
        return $this->description;
    }

    public function getModule()
    {
        return $this->module;
    }
    
    public function getInterface()
    {
        return $this->interface;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function getChannels()
    {
        return $this->channels;
    }
    
    public function getSerial()
    {
        return $this->serial;
    }
    
    public function getType()
    {
        return $this->type;
    }
    
    public function getState()
    {
        $method = $this->module.'::init';
        $module = $method();
    }   

    public function setState($key=null, $value=null)
    {
        
    }
}

?>