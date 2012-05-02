<?php
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class auth_file implements interface_auth
{
    /**
     * Hauptobject
     *
     * @var HMS
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
        // Formatierung anwenden
        $format = $this->config->format;
        $f_data = explode($this->config->delimeter, $format);
        $user_index = array_search('uservar', $f_data);
        $pass_index = array_search('passvar', $f_data);
        // Benutzerdatei laden
        $file = file($this->config->file);
        foreach ($file as $line)
        {
            $data = explode($this->config->delimeter, $line);
            if (trim($data[$user_index]) == $username)
            {
                // Security Funktion initalisieren
                if (isset($this->config->sechash))
                {
                    $secfunc = (string) strtolower($this->config->sechash);
                }
                // Password Security Funktion anwenden
                if (isset($secfunc) && function_exists($secfunc))
                {
                    if ($secfunc($password) == trim($data[$pass_index]))
                    {
                        $login = true;
                    }
                }
                elseif (trim($data[$pass_index]) == $password)
                {
                    $login = true;
                }
            }
        }
        return $login;
    }
}
?>