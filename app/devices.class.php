<?php
namespace app;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */

class devices
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
     * 
     * @param $parent \HMS           
     * @param $template \Template           
     */
    function __construct (\HMS $parent,\Template $template)
    {
        $this->parent = &$parent;
        $this->template = &$template;
        $array = array(
                    BASE_URL . 'home/rooms' => array(
                                                    'icon' => 'grid', 
                                                    'text' => 'R&auml;ume'), 
                    BASE_URL . 'home/devices' => array(
                                                    'icon' => 'grid', 
                                                    'text' => 'Ger&auml;te')
                );
        $this->template->set_header_menu($array);
    }
    /**
     * 
     * @param unknown_type $module
     * @param unknown_type $id
     */
    public function load ($module, $id)
    {
        try
        {
            // Modulprüfen
            if(!in_array($module, \Modules::getModules()) && \Modules::isActive($module))
            {
                throw new \Exception('Module not found');
            }
            // Geräte laden
            $home = new home($this->parent, $this->template);
            $home->devices();
            // Classe initalisieren
            $module = '\modules\system\\'.$module.'\\'.$module;
            $class = $module::init();
            // Gerät laden
            $device = $class->getDeviceByID($id);
            // Variablen zuordnen
            $this->template->assign('device', $device);
            // set Template parameter
            $this->template->set_title($device->getName());
            $this->template->set_header($device->getName());

        }
        catch (\Exception $e)
        {
            die($e->getMessage());
        }
    }

}
?>