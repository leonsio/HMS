<?php
namespace modules\system\Homematic\lib\api;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class Room extends Base
{
	/**
	 * Prefix für die Anfrage an der JSON API
	 * @var string
	 */
	protected $API_PREFIX = 'Room';
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
	 * Liefert Detailinfomationen zu einem Raum
	 * 
	 * @param int $id ID des Raumes
	 */
	public function get( $id)
	{
		return $this->__do_call();		
	}
	/**
	 * Liefert eine Liste aller Räume
	 * 
	 * @return mixed
	 */
	public function listAll()
	{
		return $this->__do_call();	
	}
	/**
	 * Liefert Detailinformationen zu allen Räumen
	 * 
	 * @return mixed
	 */
	public function getAll()
	{
		return $this->__do_call();		
	}
	/**
	 * Fügt einen Kanal zu einem Raum hinzu
	 * 
	 * @param int $id
	 * @param int $channelId
	 */
	public function addChannel( $id, $channelId)
	{
		return $this->__do_call();
	}
	/**
	 * Liefert die Ids aller Programme, die mindestens einen Kanal in dem Raum verwenden
	 * 
	 * @param int $id
	 */
	public function listProgramIds($id)
	{
		return $this->__do_call();		
	}
	/**
	 * Entfernt einen Kanal aus einem Raum
	 * 
	 * @param int $id
	 * @param int $channelId
	 */
	public function removeChannel( $id,  $channelId)
	{
		return $this->__do_call();		
	}
}

?>