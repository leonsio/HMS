<?php
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class auth_static implements interface_auth
{
    /**
     * Hauptobject
     *
     * @var SNLmain
     */
    private $parent = null;
    /**
     * Konfigurationsobject
     *
     * @var object
     */
    private $config = null;
    /**
     * Konstuktor der Classe
     *
     * @param Main $parent
     * @param object $config
     */
    public function __construct (HMS $parent, $config)
    {
        $this->parent = &$parent;
        $this->config = $config;
    }
    /**
     * Login-Funktion	
     *
     * @param string $username
     * @param string $password
     * @return boolean
     */
    public function login ($username, $password)
    {
        // Darf nicht direkt aufgerufen werden
        if (is_null($this->parent))
        {
            throw new Exception("Diese Methode darf nicht eigenst�ndig aufgerufen werden");
        }
        $login = false;
        foreach ($this->config->user as $user)
        {
            $c_username = (string) $user->username;
            $c_password = (string) $user->password;
            if ($username == $c_username)
            {
                // Security Funktion initalisieren
                if (isset($user->sechash))
                {
                    $secfunc = (string) strtolower($user->sechash);
                }
                // Password Security Funktion anwenden
                if (isset($secfunc) && function_exists($secfunc))
                {
                    if ($secfunc($password) == $c_password)
                    {
                        $login = true;
                    }
                }
                if ($password == $c_password)
                {
                    $login = true;
                }
            }
        }
        return $login;
    }
}
?>