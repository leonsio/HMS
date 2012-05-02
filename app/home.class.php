<?php
namespace app;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */

class home
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
    	        BASE_URL.'home/devices'=>array('icon' => 'grid', 'text'=>'Ger&auml;te') 
    	        	        );
        $this->template->set_header_menu($array);    	
    }
    /**
     * 
     */
    public function rooms()
    {
        $return = array();
        // Header
        $this->template->set_title('R&auml;ume');
        $this->template->set_header('R&auml;ume');
        try
        {
            // Hauptklasse initalisieren
            $rmcl = new \Rooms($this->parent);
            // Stockwerke laden
            $floors = $rmcl->getFloors();
            // Stockwerke durchlaufen
            foreach($floors as $floor_id => $floor_name)
            {
                // Räume auf dem Stockwerk laden
                $rooms_in_floor = $rmcl->getRoomInFloor($floor_id);
                // Alle Räume bearbeiten
                foreach($rooms_in_floor as $room_id => $room_name)
                {
                    // Ausgabe vorbereiten
                    $return[$floor_name][$room_id] = $room_name;
                }
            }
            // Ausgabe
            $this->template->assign('rooms', $return);
        }
        catch (\Exception $e)
        {
            die($e->getMessage());
        }

    }
    
    /**
     * 
     */
    public function devices()
    {
        $devs = array();
        $this->template->set_title('Ger&auml;te');
        $this->template->set_header('Ger&auml;te');
        $modules = \Modules::getModules();
        foreach($modules as $module)
        {
            try
            {
                $class = sprintf('\modules\system\%s\%s', $module, $module);
                $md = $class::init();
                $devs[$module] = $md->getDeviceList();
            }
            catch( \Exception $e)
            {
                print_r($e->getMessage());
            }
        }
        // Räume an das Template übergeben
        $this->template->assign('devs', $devs);
    }
    
}

?>