<?php
namespace modules\system\MAX\lib\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class Device
{
	/**
	 * Gerätedata
	 * 
	 * @var array
	 */
    private $devs = null;
    /**
     * Konstruktor der Klase
     * 
     * @uses \includes\api\Device
     */
    function __construct ()
    {
    	// Konstruktor Base aufrufen
        $base = Base::init();
        // Gerätedata generieren
		$data = $base->getMetaData();
		// Alles als Raumobjekt parsen
		foreach($data as $room)
		{
		    $room['Devices'] = is_array($room['Devices']) ? $room['Devices'] : array();
		    foreach($room['Devices'] as $device)
		    {
    			// Daten generieren
    			$dev_data = new\stdClass();
    			$dev_data->name = $device['DeviceName'];
    			$dev_data->id = $device['RFID'];
    			$dev_data->module = 'modules\system\MAX\MAX';
    			$dev_data->serial = $device['SerialNumber'];
    			$dev_data->description = null;
    			// Speichern
    			$this->devs[$device['RFID']] = new \includes\api\Device($dev_data);
		    }
		}
    }
    /**
     * Liefert Detailinformationen zu einem Gerät
     * @param int $id
     * @return \includes\api\Device
     */
    public function get($id)
    {
		if(isset($this->devs[$id]))
		{
			return $this->devs[$id];
		}
		else
		{
			return false;
		}
    }
    /**
     * Liefert IDs aller Geräte zurück
     * 
     * @return array
     */
    public function listAll()
    {
        return array_keys($this->devs);
    }
    
    /**
     * Liefert Detailinformationen aller Geräte zurück
     * 
     * @return \Device[]
     */
    public function getAll()
    {
        return $this->devs;
    }
}

?>