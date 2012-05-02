<?php
/**
 * 	Konfigurationsbackend für die XML-Dateien
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Config
 * 	@version 1.0
 *
 * 	$Id: config.xml.php 68 2008-08-19 08:24:58Z lko $
 */
class config_xml implements interface_config
{
    /**
     * Konfigurationsdaten
     *
     * @var array
     */
    protected $config = array();
    /**
     * Fehlermeldung
     *
     * @var string
     */
    private static $error = null;
    /**
     * Konstruktor der Classe
     *
     */
    public function __construct ()
    {
        $this->config = @simplexml_load_file(realpath(CONF_ROOT_DIR . "/config.xml"));
        if (! $this->config)
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
    }
    /**
     * Parsen einer Konfigurationsdatei
     *
     * @param string $name
     * @return object
     */
    public static function doCfgParse ($name)
    {
        if(file_exists($name))
        {
            $configs = @simplexml_load_file($name);
            if (! $configs)
            {
                self::$error = error_get_last();
                throw new Exception(self::$error['message']);
            }
            return $configs;
        }
        else
        {
            throw new Exception('Config file not found');
        }
    }
    /**
     * Importiert Werte in die Konfiguration
     *
     * @param string $group
     * @param string $name
     * @param string $value
     * @todo config_xml::doCfgImport muss noch implementiert werden
     */
    public function doCfgImport ($group, $name, $value = null)
    {
    }
    /**
     * Ausgabe von der Konfigurationsdatei als XML
     *
     * @param object $config
     * @return SimpleXMLElement
     */
    public static function asXML ($config)
    {
        if ($config instanceof SimpleXMLElement)
            return $config->asXML();
        else
        {
            $xml = self::object_to_simplexml($config);
            return $xml->asXML();
        }
    }
    /*	
	public function __sleep()
	{
	    $this->config = $this->asXML($this->config);
	    return array('config');
	}
	
	public function __wakeup()
	{
	    $this->__construct();
	    #$this->config=simplexml_load_string($this->config);    
	}

	/**
	 * gibt die Einstellungen zur�ck
	 *
	 * @return object
	 */
    public function doCfgExport ()
    {
        return $this->config;
    }
    /**
     * Gibt das gesuchte Element zur�ck
     *
     * @param string $var_name
     * @param string $key
     * @return object
     */
    public function getCfgValue ($var_name, $key = false)
    {
        $var_value = false;
        foreach ($this->config as $name => $value)
        {
            if ($var_name == $name)
            {
                if (! $key)
                {
                    $var_value = $value;
                }
                else
                {
                    $var_value = $this->config->$name;
                }
            }
        }
        return $var_value;
    }
    /**
     * Wandlet ein Objekt zur einem Simplexml Objekt um
     *
     * @param object $data
     * @param string $name
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement
     */
    public static function object_to_simplexml ($data, $name = "config", &$xml = null)
    {
        if (is_null($xml))
        {
            $xml = new SimpleXMLElement("<{$name}/>");
        }
        foreach ($data as $key => $value)
        {
            if (is_object($value))
            {
                $xml->addChild($key);
                self::object_to_simplexml($value, $name, $xml->$key);
            }
            else
            {
                // nur ein einziges Element vorhanden
                if (get_object_vars($value))
                {
                    $xml->addChild($key);
                    self::object_to_simplexml($value, $name, $xml->$key);
                } // Ein Array wurde �bergeben
                elseif (is_array($value))
                {
                    foreach ($value as $element)
                    {
                        if (! is_object($element) && ! is_array($element))
                        {
                            $new_key = $xml->addChild($key, $element);
                        }
                        else
                        {
                            $new_key = $xml->addChild($key);
                            self::object_to_simplexml($element, $name, $new_key);
                        }
                    }
                }
                else
                {
                    if ($value == '')
                        $value = null;
                    $xml->addChild($key, $value);
                }
            }
        }
        return $xml;
    }
    /**
     * Vereint zweit SimpleXML Objekte
     *
     * @param SimpleXMLElement $xml_parent
     * @param SimpleXMLElement $xml_children
     * @param string $linkingNode
     * @param int $child_count
     * @param boolean $xml
     * @return SimpleXMLElement
     */
    public static function simplexml_merge (SimpleXMLElement $xml_parent, SimpleXMLElement $xml_children, $linkingNode = "linkingNode", $child_count = 0, $xml = false)
    {
        if (! $xml)
        {
            $xml = $xml_parent->addChild($linkingNode);
        }
        else
        {
            $xml = $xml_parent[$child_count];
        }
        $child_count = 0;
        foreach ($xml_children->children() as $k => $v)
        {
            if ($xml->$k)
            {
                $child_count ++;
            }
            if ($v->children())
            {
                $xml->addChild($k);
                self::simplexml_merge($xml->$k, $v, '', $child_count, true);
            }
            else
            {
                if (is_null($v) || $v == '')
                {
                    $xml->addChild($k);
                }
                else
                {
                    $xml->addChild($k, $v);
                }
            }
        }
        return $xml;
    }
}
?>
