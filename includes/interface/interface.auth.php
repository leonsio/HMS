<?php
/**
 * Interface f�r die Auth-Backends
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Auth
 * 	@version 1.0
 * 
 * 	$Id: interface.auth.php 68 2008-08-19 08:24:58Z lko $
 */
interface interface_auth
{
    /**
     * Konstuktor der Classe
     *
     * @param HMS $parent
     * @param object $config
     */
    public function __construct (HMS $parent, $config);
    /**
     * Login-Funktion
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login ($username, $password);
}