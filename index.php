<?php
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 */
// Definieren wo die Skripte liegen
define('APP_ROOT', getcwd().'/');
// Skript initalisieren
include APP_ROOT.'includes/init.php';
// Anwendung starten
HMS::init();