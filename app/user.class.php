<?php
namespace app;
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 */

class user
{
    /**
     * Templateobjekt
     * 
     * @var \Template
     */
    private $template = null;
    /**
     * Hauptobjekt
     * 
     * @var \HMS
     */
    private $parent = null;
    
    function __construct (\HMS $parent, \Template $template)
    {
        $this->parent = &$parent;
        $this->template = &$template;
        $this->template->set_header_menu(array());
    }
    
    function login()
    {
        if($this->parent->auth->isAuth())
        {
            header('Location: '. BASE_URL);
            return;
        }
        if(isset($_POST['task']) && $_POST['task'] == 'login')
        {
            if($this->parent->login($_POST['username'], $_POST['password']))
            {
                $this->template->set_msg(null, null);
                header('Location: '. BASE_URL);
                exit;
            }
            else
            {
                $this->template->set_msg('Falsche Zugangsdaten', 'error');
            }
        }
        $this->template->set_title('Login');
        $this->template->set_header('Login');
    }
}

?>