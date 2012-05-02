<?php
namespace modules\system\Homematic\lib\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class Device extends Base
{
	/**
	 * Prefix für die Anfrage an der JSON API
	 * @var string
	 */
	protected $API_PREFIX = 'Device';
	/**
	 * Konstruktor der Klasse
	 * @param Session $session
	 */
	function __construct(Session $session=null)
	{
		// Session setzen
		$this->session=$session;
		// Falls Session nicht übergeben wurde, die Instanz initalisieren
		if(!($session instanceof Session))
		{
			$this->session = Session::init();
		}
		// Base Construktor initieren
		parent::initBase();
	}

	/**
	 * Liefert Detailinformationen zu einem Gerät
	 * 
	 * @var int id
	 */
	public function get($id)
	{
		return $this->__do_call();
	} 
	/**
	 * Ermittelt, ob das Geräte in direkten Verknüpfungen oder Programmen verwendet wird
	 */
	public function hasLinksOrPrograms($id) 
	{
		return $this->__do_call();
	}
	/**
	 * Liefert die Ids aller fertig konfigurierten Geräte
	 */
	public function listAll()
	{
		return $this->__do_call();
	}
	/**
	 * Liefert Detailinformationen zu allen Geräten
	 *
	 * @return array
	 */
	public function getAll()
	{
	    $devs = array();
	    $devlist = $this->listAll();
	    foreach($devlist as $id)
	    {
	        $dev = $this->get($id);
	        // Default Adressen übergehen
	        if($dev->address=='BidCoS-RF') continue;
	        if($dev->address=='BidCoS-Wir') continue;
	        // Gerät speichern
	        $devs[] = $dev;
	    }
	    return $devs;
	}	
	/**
	 * Liefert die Ids aller Programme, die mindestens einen Kanal des Geräts verwenden
	 * @param int $id
	 */
	public function listProgramIds($id)
	{
		return $this->__do_call();
	} 
	/**
	 * Prüft, ob Ergebnisse für einen Funktionstest vorliegen
	 * 
	 * @param int $id
	 * @param int $testId
	 */
	public function pollComTest	($id, $testId)
	{
		return $this->__do_call();
	} 
	/**
	 * Legt den Namen des Geräts fest
	 * 
	 * 
	 * @param int $id
	 * @param string $name
	 */
	public function setName($id, $name)
	{
		return $this->__do_call();
	} 
	/**
	 * Startet den Funktionstest für ein Gerät
	 * 
	 * @param int $id
	 */
	public function startComTest($id)
	{
		return $this->__do_call();
	} 
	
}

?>