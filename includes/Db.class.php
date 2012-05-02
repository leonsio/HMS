<?php
/**
 *  Wrapper für die class_db
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage DB
 * 	@version 1.0
 *  @id $Id: SNLdb.class.php 68 2008-08-19 08:24:58Z lko $
 * 	
 */
class Db extends class_db
{
    /**
     * Aktuelle Datenbank Engine
     *
     * @var string
     */
    private $engine = 'mysqli';
    /**
     * Konstruktor der Klasse, setzt die Einstellungen für die class_db
     *
     * @param \Config $config
     */
    public function __construct ($config = null)
    {
        parent::__construct($config, $config->engine);
    }
    /**
     * Standardausgabe
     * 
     * @return string
     */
    public function __toString ()
    {
        return "Dies ist die Datenbankengine des HMS";
    }
    /**
     * Einstellungen für das Serialisieren
     *
     * @return array
     */
    public function __sleep ()
    {
        return array(
            'parent' , 
            'recent_con' , 
            'sql' , 
            'query_count' , 
            'result'
        );
    }
    public function get_engine ()
    {
        return $this->engine;
    }
}
?>
