<?php
namespace modules\system\MAX\lib\api;
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
	 * Räumedata
	 * 
	 * @var array
	 */
    private $rooms = null;
    /**
     * Konstruktor der Klase
     * 
     * @uses \includes\api\Room
     */
    function __construct ()
    {
    	// Konstruktor Base aufrufen
        $base = Base::init();
        // Räumedata generieren
		$data = $base->getMetaData();
		// Alles als Raumobjekt parsen
		foreach($data as $room)
		{
			// Daten generieren
			$room_data = new\stdClass();
			$room_data->name = $room['RoomName'];
			$room_data->id = $room['RoomID'];
			$room_data->module = 'modules\system\MAX\MAX';
			$room_data->description = null;
			if(is_array($room['Devices']))
			{
    			foreach ($room['Devices'] as $device)
    			{
    			    $room_data->channels[] = $device['RFID'];
    			}
			}
			// Speichern
			$this->rooms[$room['RoomID']] = new \includes\api\Room($room_data);
		}
    }
    /**
     * Liefert Detailinformationen zu einem Raum
     * @param int $id
     * @return \includes\api\Room
     */
    public function get($id)
    {
		if(isset($this->rooms[$id]))
		{
			return $this->rooms[$id];
		}
		else
		{
			return false;
		}
    }
    /**
     * Liefert IDs aller Räume zurück
     * 
     * @return array
     */
    public function listAll()
    {
        return array_keys($this->rooms);
    }
    
    /**
     * Liefert Detailinformationen aller Räume zurück
     * 
     * @return \includes\api\Room[]
     */
    public function getAll()
    {
        return $this->rooms;
    }
}

?>