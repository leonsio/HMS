<?php
/**
 * Client für das SOAP Service
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@version 1.0
 *  @id $Id: client.soap.php 68 2008-08-19 08:24:58Z lko $
 */
class client_soap extends SoapClient
{
    public function SoapClient ()
    {
        parent::__construct();
    }
}
?>
