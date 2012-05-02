<?php
namespace modules\system\Homematic;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */

// keine Session starten
define("NO_SESSION", true);
chdir('../../../');
define('APP_ROOT', getcwd().'/');
require_once('./includes/init.php');

/**
 *
 * @author leonsio
 *        
 *        
 */
class Server
{ 
    private $db  = null;
    
    public function __construct()
    {
        $config = new \config_xml();
        $this->db = new \Db($config->getCfgValue('DB'));  
    }
    
    public function listDevices()
    {

    }
    /**
     * Schreibt aktuelles Event in die Datenbank
     * 
     * @param string $interface_id
     * @param string $address
     * @param string $value_key
     * @param mixed $value
     */
    public function event($interface_id, $address, $value_key, $value)
    {
        $table = date("Ym");
        $this->db->query("
                INSERT INTO HMS_DATA.`".CURRENT_TABLE."` 
                VALUES('{$interface_id}','{$address}','{$value_key}','{$value}', NOW(), '".addslashes(get_class())."')");
    }
    
    public function newDevices($interface_id,$dev_descriptions)
    {
        
    }
    
    public function deleteDevices ($interface_id, $addresses)
    {
        
    }
    
    public function updateDevice($interface_id,  $address,  $hint)
    {
        
    }
}
$server = new \server_xmlrpc(array('style'=>'hms'));
$server->setClass('modules\system\Homematic\Server');
$server->handle();

?>