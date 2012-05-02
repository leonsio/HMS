<?php
namespace app;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class monitoring
{

    /**
     * Templateobjekt
     *
     * @var \Template
     */
    private $template = null;

    private $db;
    
    /**
     * Hauptobjekt
     *
     * @var \HMS
     */
    private $parent = null;

    /**
     * Konstruktor der Klasse
     * 
     * @param $parent \HMS           
     * @param $template \Template           
     */
    function __construct (\HMS $parent,\Template $template)
    {
        $this->parent = &$parent;
        $this->template = &$template;
        // Charts laden
        $this->db = new \Db($parent->getCfgValue('DB'));
        $this->template->set_header_menu(array());
    }
    
    public function show($id='')
    {
        $return = array();
        $this->template->set_title('Monitoring');
        $this->template->set_header('Chart Test'); 
        
        $result = $this->db->query("
                SELECT value, UNIX_TIMESTAMP(timestamp) as timestamp 
                FROM HMS_DATA.`".CURRENT_TABLE."`  
                WHERE address='CUX3100001:1' AND `key`='TEMPERATURE' ORDER BY timestamp DESC"); 
        while($temp = $this->db->fetch($result))
        {
            $return[] = sprintf("[%d, %f]", $temp->timestamp*1000, $temp->value);
            
        } 
        // Variablen zuordnen
        $this->template->assign('temp', $return);
    }
}

?>