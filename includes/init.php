<?php
/**
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 */

/**
 * Autoload funktion
 * @param string $className
 */
function autoload ($className)
{
    // Init
    $found = false;
    // Step 1 Namespace Dateien durchsuchen
    $fileName = str_replace("\\", "/", $className) . ".class.php";
    if (file_exists(APP_ROOT . $fileName)) 
    {
        require_once APP_ROOT . $fileName;
        $found = true;
    }
    // Step 2 Lokale Funktionen durchsuchen
    $class_file = $className . '.class.php';
    if (file_exists(APP_ROOT . 'includes/' . $class_file) && ! $found) 
    {
        require_once APP_ROOT . 'includes/' . $class_file;
        $found = true;
    }
    // Step 3 Submodule lokaler Funktionen laden
    if (! $found) 
    {
        $path = explode('_', $className);
        $class_name = array_pop($path);
        $depth = count($path);
        $parent_class = strtolower($path[$depth - 1] . '.' . $class_name . '.php');
        if (file_exists( APP_ROOT . 'includes/' . implode('/', $path) . '/' . $parent_class)) 
        {
            require_once APP_ROOT . 'includes/' . implode('/', $path) . '/' . $parent_class;
            $found = true;
        }
    }
    if (! $found) 
    {
        #print_r(debug_backtrace());
        die("Class {$className} not found\n");
    }
}

spl_autoload_extensions(".php, .class.php");
spl_autoload_register('autoload');
define('CONF_ROOT_DIR', APP_ROOT.'config');
define('CURRENT_TABLE', date("Ym"));
// Session starten 
if(!defined("NO_SESSION"))
session_start();
header("Content-Type: text/html; charset=utf-8");
?>