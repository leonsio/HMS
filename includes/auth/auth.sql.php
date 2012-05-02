<?php
/**
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class auth_sql implements interface_auth
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
    public function __construct (SNLmain $parent, $config)
    {
        $this->parent = &$parent;
        $this->config = $config->sql;
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
        // Query
        $query = sprintf("SELECT %s FROM %s WHERE %s='%s' LIMIT 1", $this->config->passfield, $this->config->table, $this->config->userfield, addslashes($username));
        // result hollen
        $result = $this->parent->db->query($query, true);
        $passfield = (string) $this->config->passfield;
        // Security Funktion initalisieren
        if (isset($this->config->sechash))
        {
            $secfunc = (string) strtolower($this->config->sechash);
        }
        // Password Security Funktion anwenden
        if (isset($secfunc) && function_exists($secfunc))
        {
            if ($secfunc($password) == $result->$passfield)
            {
                $login = true;
            }
        }
        elseif ($result->$passfield == $password)
        {
            $login = true;
        }
        return $login;
    }
}
?>
