<?php
/**
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class Config 
{
    /**
     * Name der Config-Classe
     *
     * @var string
     */
    protected $config_type = null;
    /**
     * Konfigurationsobjekt
     *
     * @var object
     */
    private $config_obj = null;      
    /**
     * Konstruktor der Klasse
     *
     * @param string $type
     */
	protected function __construct($type='xml') 
	{    
	    $this->config_type = 'config_' . $type;
	    $this->config_obj = new $this->config_type($this);
	}
	/**
	 * Parsen der Konfigurationsdatei
	 *
	 * @param string $name
	 * @param string $type
	 * @return object
	 */
	public static function doCfgParse ($name, $type = 'xml')
	{
		// Export-Typ vorbereiten, Klasse bestimmen
		$config_class = 'config_' . $type ;
		// Ausführen und zurückgeben
		return $config_class::doCfgParse($name);
	}
	/**
	 * Importiert Einstellungen in die Konfiguration
	 *
	 * @param string $group
	 * @param string $name
	 * @param string $value
	 * @return boolean
	 */
	public function doCfgImport ($group, $name, $value = null)
	{
		return $this->config_obj->doCfgImport($group, $name, $value);
	}
	/**
	 * Exportiert die Konfigurationsdatei im jeweiligen Format
	 *
	 * @param string $type
	 * @param object $configs
	 * @return string
	 */
	public function doCfgExport ($type = 'xml', $configs = null)
	{
		// Einstellungen hollen, falls keine übergeben wurden
		if (! $configs)
		{
			$configs = $this->config_obj->doCfgExport();
		}
		// Export-Typ vorbereiten, Klasse bestimmen
		$config_class = 'config_' . $type . '::as' . strtoupper($type) . '($configs);';
		// Ausführen
		eval("\$return = " . $config_class);
		// Einstellungen zurück geben
		return $return;
	}
	/**
	 * Gibt die gewünschte Konfiguration zurück
	 *
	 * @param string $var_name
	 * @param string $key
	 * @return object
	 */
	public function getCfgValue ($var_name, $key = false)
	{
		return $this->config_obj->getCfgValue($var_name, $key);
	}	
	/**
	 * Gibt Konfigurationsdatei des Moduls zurück
	 * Es wird nur XML Unterstützt, die Endung der Datei darf nicht angegeben werden
	 * 
	 * @param string $file
	 */
	public static function getModuleConfig($module, $file=null)
	{
	    // Path bestimmen
	    $path = Modules::path($module).'/etc/';
	    // Konfigurationsdatei bestimmen
		$filename = is_null($file) ? $module : $file ;
		$filename.= '.xml';		
		// Alles zurück geben
		return self::doCfgParse($path.$filename);
	}
}

?>