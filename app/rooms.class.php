<?php
namespace app;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */

class rooms
{
    /**
     * Templateobjekt
     *
     * @var \Template
     */
    private $template = null;
    /**
     * Hauptobjekt
     *
     * @var \HMS
     */
    private $parent = null;
    /**
     * Konstruktor der Klasse
     * @param \HMS $parent
     * @param \Template $template
     */
    function __construct (\HMS $parent, \Template $template)
    {
    	$this->parent = &$parent;
    	$this->template = &$template;
    	$array=array(
    	        BASE_URL.'home/rooms'=>array('icon' => 'grid', 'text'=>'R&auml;ume'),
    	        BASE_URL.'home/devices'=>array('icon' => 'grid', 'text'=>'Ger&auml;te')    	        );
        $this->template->set_header_menu($array);    	
    }
    
    
    public function load( $id)
    {
        try
        {    
            // Raumliste laden
            $home = new home($this->parent, $this->template);
            $home->rooms();
            // Ausgabavariable
            $devlist=array();
            // Hauptklasse initalisieren
            $rmcl = new \Rooms($this->parent);
            $hms_room = $rmcl->getRoomByID($id);
            // set Template parameter
            $this->template->set_title(utf8_encode($hms_room->name));
            $this->template->set_header(utf8_encode($hms_room->name));
            // Modul Räume laden            
            $rooms = $rmcl->getModuleRoomsInRoom($id);
            foreach ($rooms as $room)
            {
                $module = $room->getModule();
                $class = $module::init();
                $devs=$class->getDeviceInRoomByID($room->getID());
            	foreach($devs as $device)
            	{
            	    $devlist[$device->getID()] = $device;
            	}
            }
            // Variablen zuordnen
            $this->template->assign('devlist', $devlist);          	
        }
        catch( \Exception $e)
        {
        	print_r($e->getMessage());
        }
    }
}

?>