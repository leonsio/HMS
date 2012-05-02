<?php
namespace modules\system\Homematic\lib\driver;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class client_json extends \client_json
{

    /**
     * API-URL der Homematic.
     * 
     *
     * @var string
     */
    protected $url = '/api/homematic.cgi';

    /**
     * Konstruktor der Klasse
     * 
     * beim gestetzten URL Parameter werden $this->host und $this->port gegebenenfalls �berschrieben 
     *
     * @param array $options
     */
    public function __construct (array $options)
    {
        parent::__construct($options);
	}
    /**
     * Gibt die Liste aller Methoden die auf dem Server existieren
     *
     * @return array
     */
    public function __getFunctions ()
    {
        return $this->__call('system.listMethods');
    }

}
?>
