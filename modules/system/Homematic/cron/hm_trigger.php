#!/usr/bin/php5
<?php
namespace modules\system\Homematic;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
############ EDIT ME ############
$url_http = 'http://192.168.178.24/hms/modules/system/Homematic/Server.class.php';
$url_bin  = 'xmlrpc_bin://192.168.178.24:8701';
##################################
// HMS laden
chdir('../../../../');
define('APP_ROOT', getcwd().'/');
require_once('./includes/init.php');
// Session initalisieren
$session = lib\api\Session::init();
// Interfacemodul laden
$hm = new lib\api\Homematic($session);
// Alle Interfaces einlesen
$interfaces = $hm->listInterfaces();
// Alle Interfaces durchlaufen
foreach($interfaces as $interface)
{
    // URL setzen
    $url = ($interface->name == 'CUxD') ? $url_bin : $url_http;
    // Für Events anmelden
    $hm->init($interface->name, $url, $interface->name);
}
// Logout
$session->logout();


?>