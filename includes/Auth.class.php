<?php
/**
 * Authentification/Authorisation Klasse der SNL
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Auth
 * 	@version 1.0
 * 
 * 	$Id$
 */
class Auth
{
    /**
     * Auth Identifer bei "einfachen" Authorisiereung
     *
     * @var boolean
     * @example {1,0}
     */
    private $authorized = false;
    /**
     * Hauptobjekt der HMS
     *
     * @var resource
     */
    private $parent = null;
    /**
     * Bei erweiterten Authorisierung 
     *
     * @var array
     * @example array('static' => false, 'sql' => true)
     */
    private $engine = null;
    /**
     * Konfigurationsobjekt der Backends
     *
     * @var object
     */
    private $engine_conf = null;
    /**
     * Konstruktor der Classe
     *
     * @param Main $parent
     */
    public function __construct (HMS $parent)
    {
        $this->parent = $parent;
        $config = $this->parent->getCfgValue("AUTH");
        // Backends-Array bilden
        $this->engine = explode('/', $config->engine);
        // values mit key vertauschen und value auf 0 setzen
        $this->engine = array_fill_keys($this->engine, 0);
        unset($config->engine);
        foreach($config as $engine => $type)
        {
            $this->engine_conf->$engine = (string) $type;
        }
    }
    /**
     * Gibt an ob der Benutzer angemeldet ist oder nicht
     *
     * @return mixed
     */
    public function isAuth ()
    {
        if (count($this->engine) == 1)
        {
            return $this->authorized;
        }
        else
        {
            return $this->engine;
        }
    }
    /**
     * Force Benutzer Authentifizierung
     */
    public function setAuth()
    {
        $this->authorized=1;
        $this->engine = array('static'=>1);
    }
    /**
     * Gibt die Gruppen ID/Namen zurück
     *	
     *	@return array
     */
    public function getGroups ()
    {
        return $this->parent->groups->getGroups($this);
    }
    /**
     * Logt einen Benutzer ein, benutzt dabei verschiedene Backends
     *
     * @param string $username
     * @param string $password
     * @return mixed
     */
    public function login ($username, $password)
    {
        // Darf nicht direkt aufgerufen werden
        if (is_null($this->parent))
        {
            throw new Exception("Diese Methode darf nicht eigenständig aufgerufen werden");
        }
        // mind. 1. Engine muss vorhanden sein
        if (! is_array($this->engine))
        {
            throw new Exception("Keine Login-Backends gefunden");
        }
        // Alle Backends durchlaufen und Benutzer anmelden
        foreach ($this->engine_conf as $type => $value)
        {
            // Name festlegen
            $auth_class = 'auth_' . $type;
            // Instanz initalisieren
            $auth = new $auth_class($this->parent, $this->parent->doCfgParse(CONF_ROOT_DIR."/Auth/auth.{$type}.xml", $value));
            // Loginfunktion anwenden
            $this->engine[$type] = $auth->login($username, $password);
            unset($value);
        }
        // Aufräumen
        unset($auth, $auth_class, $type);
        // Bei nur einer Engine
        if (count($this->engine) == 1)
        {
            $this->authorized = $this->engine[key($this->engine)];
        }
        // Rückgabe	
        return $this->isAuth();
    }
    /**
     * Variablen fürs Speichern vorbereiten
     */
    public function __sleep()
    {
        return array('authorized', 'engine', 'parent', 'engine_conf');
    }
    /**
     * Methode zum initalisieren des Objekts nach dem Aufwachen
     */
    public function __wakeup()
    {
        if(is_null($this->parent))
        {
       #     $this->parent=HMS::init();
        }
    }
}
?>