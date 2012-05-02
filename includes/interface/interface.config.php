<?php
/**
 * Interface für die Config-Classen
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Config
 * 	@version 1.0
 *  @id $Id$
 * 	
 */
interface interface_config
{
    /**
     * Parst eine Konfig-Datei und gibt deren Inhalt zur�ck
     *
     * @param string $name
     */
    public static function doCfgParse ($name);
    /**
     * Importiert eine Konfig-Datei in die Konfiguration
     *
     * @param string $group
     * @param strng $name
     * @param string $value
     */
    public function doCfgImport ($group, $name, $value = null);
    /**
     * Exportiert Einstellungen
     *
     */
    public function doCfgExport ();
    /**
     * gibt eine Einstellung zur�ck
     *
     * @param string $var_name
     * @param string $key
     */
    public function getCfgValue ($var_name, $key = false);
}
?>