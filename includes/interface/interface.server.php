<?php
/**
 * Interface für die Server Klasse
 * 
 * Muss noch dokumentiert werden
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Server
 * 	@version 1.0
 *  @id $I$
 */
interface interface_server
{
    /**
     * Enter description here...
     *
     * @param array $options
     */
    public function __construct (array $options);
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     */
    public function addFunction ($name);
    /**
     * Enter description here...
     *
     * @param unknown_type $name
     */
    public function setClass ($name);
    /**
     * Enter description here...
     *
     */
    public function handle ();
}
?>
