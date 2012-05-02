<?php
/**
 * 	Wrapper für libssh2
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 * 	$Id$
 */
class class_ssh
{
    /**
     * Connect Resource
     *
     * @var ressource
     */
    private $connect = null;
    /**
     * Verbindungsangabe ( DSN )
     *
     * @var string
     */
    private $dsn = null;
    /**
     * Authentifikationsmethode
     *
     * @var int
     * @note null = none, 1 = password, 2 = pubfile, 3 = hostfile
     */
    private $auth_type = null;
    /**
     * Weitere Verbindungsparameter
     *
     * @var array
     */
    private $auth_options = null;
    /**
     * Fehlermeldungen: Verbindung oder Shell
     *
     * @var string
     */
    private $error = null;
    /**
     * Letzter Befehl
     *
     * @var string
     */
    private $last_cmd = null;
    /**
     * SSH2 Stream auf dem Remote-Host
     *
     * @var resource
     */
    private $stream = null;
    /**
     * Statuscode des letzten Befehls
     *
     * @var int
     */
    private $status_code = null;
    /**
     * Konstruktor der Klasse, zweiter Parameter kann auch das Password sein
     *
     * @param string $dsn
     * @param mixed $auth
     * @param array $options
     * @note $options[user_ssh_dir]
     * @note $options[user_pub_key]
     * @note $options[user_priv_key]
     * @note $options[user_key_passwd]
     * @note $options[user_password]
     * @note $options[host_pub_key]
     * @note $options[host_priv_key]
     * @note $options[host_key_passwd]
     * @todo host-key Methode muss implementiert werden
     */
    public function __construct ($dsn, $auth = null, $options = array())
    {
        $this->dsn = $dsn;
        $data = array();
        if (! preg_match('#([\w]+)@([\w\.]+):?([\d]+)?#is', $dsn, $data))
        {
            throw new Exception('Could not parse DSN-data');
        }
        // Bei einem Integer den Typ setzen
        if (is_int($auth))
        {
            $this->auth_type = $auth;
        }
        // Falls es angegeben, Auth-Type probieren
        if (! is_null($auth))
        {
            switch ($auth)
            {
                case 1: // Password
                    if ($options['user_password'] == '')
                    {
                        throw new Exception('User password must be set');
                    }
                    $this->auth_options['user_password'] = $options['user_password'];
                    break;
                case 2: // Pub-Priv Key		
                    if ($options['user_ssh_dir'])
                    {
                        // versuche automatisch Schluessel zu finden
                        $this->parse_ssh_dir($options['user_ssh_dir']);
                    }
                    else
                    { /* manuelles setzen von pub/priv keys */
                    }
                    @$this->auth_options['user_key_passwd'] = ($options['user_key_passwd']) ? $options['user_key_passwd'] : null;
                    break;
                case 3: // Hostkey (untested)
                    break;
                // Falls es ein String ist, muss es Password sein
                case is_string($auth):
                    $this->auth_options['user_password'] = $auth;
                    $this->auth_type = 1;
                    break;
            }
        }
        // Verbinde...
        $this->connect($data[2], $data[1], @$data[3]);
    }
    /**
     * Ausf�hren von einem Befehl auf dem Server
     *
     * @param string $cmd Befehl was ausgef�hrt werden soll
     * @param boolean $halt_on_error Bei true wird bei einer Fehlermeldung Exception generiert
     * @return resource
     */
    public function execute ($cmd, $halt_on_error = false)
    {
        // Befehl an die Shell schicken und Status abfragen
        if (! $this->stream = @ssh2_exec($this->connect, $cmd . '; echo $?'))
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
        // letzten Befehl merken
        $this->last_cmd = $cmd;
        // Fehler in der Shell abfangen
        if ($halt_on_error)
        {
            if ($this->fetch_error())
            {
                throw new Exception($this->error);
            }
        }
        return $this->stream;
    }
    /**
     * Ausgabe von der Fehlermeldung der Shell
     *
     * @return mixed
     */
    public function fetch_error ()
    {
        if (! is_resource($this->stream))
        {
            throw new Exception('No resource given');
        }
        $stderr = ssh2_fetch_stream($this->stream, SSH2_STREAM_STDERR);
        stream_set_blocking($stderr, true);
        while ($line = fgets($stderr))
        {
            $this->error .= $line;
        }
        if ($this->error == '')
            return false;
        else
            return $this->error;
    }
    /**
     * Ausgabe der Anwendung
     *
     * @param boolean $array
     * @return mixed
     */
    public function fetch ($array = false)
    {
        if (! is_resource($this->stream))
        {
            throw new Exception('No resource given');
        }
        $output = array();
        $stdio = ssh2_fetch_stream($this->stream, SSH2_STREAM_STDIO);
        stream_set_blocking($stdio, true);
        while ($line = fgets($stdio))
        {
            $output[] = $line;
        }
        // letzte Zeile ist STAUS
        for ($i = count($output) - 1; $i >= 0; $i --)
        {
            if (trim($output[$i]) != '')
            {
                $this->status_code = $output[$i];
                unset($output[$i]);
                break;
            }
            unset($output[$i]);
        }
        if ($array)
            return $output;
        else
            return implode("\n", $output);
    }
    /**
     * gibt das aktuelle Statuscode des Befehls zur�ck
     *
     * @return int
     */
    public function get_status ()
    {
        return $this->status_code;
    }
    /**
     * Gibt die aktuelle Shell zur�ck
     *
     * @return resource
     */
    public function get_shell ()
    {
        if (! $shell = @ssh2_shell($this->connect, 'vt102', null, 100, 24, SSH2_TERM_UNIT_CHARS))
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
        return $shell;
    }
    /**
     * Verschiebt die Date von Host zum Client
     *
     * @param string $remote
     * @param string $local
     * @return boolean
     */
    public function get ($remote, $local)
    {
        if (! $status = @ssh2_scp_recv($this->connect, $remote, $local))
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
        return true;
    }
    /**
     * Legt die Datei auf dem Remote Server an
     *
     * @param string $local
     * @param string $remote
     * @param int $mode
     * @return boolean
     */
    public function put ($local, $remote, $mode = 0644)
    {
        if (! $status = @ssh2_scp_send($this->connect, $local, $remote, $mode))
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
        return true;
    }
    /**
     * Listet die Pub-Keys auf dem Server
     * 
     * @todo scheint nicht richtig zu funktioneiren? libssh2-problem? falsche version?
     * @todo libssh2 0.10 ausprobieren....	
     * @todo man brauch auf dem Zielserver publickey subsystem
     */
    public function list_pubkeys ()
    {
        $init = ssh2_publickey_init($this->connect);
        return $init;
    }
    /**
     * Stellt Verbindung mit einem SSH Server her
     *
     * @param string $host
     * @param string $username
     * @param integer $port
     * @return boolean
     * @todo hostfile-Methode muss implementiert werden
     */
    private function connect ($host, $username, $port = "22")
    {
        $port = ((string) $port != '') ? $port : "22";
        // Verbindung herstellen, Standardport ist hardcodiert
        $this->connect = @ssh2_connect($host, (int) $port);
        if ($this->connect == false)
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
        // Benutzer Authentifizieren
        switch ($this->auth_type)
        {
            case 1: // Password
                // should not work....
                $result = @ssh2_auth_password($this->connect, $username, $this->auth_options['user_password']);
                break;
            case 2: // Pub-Priv Key 
                // Key-Password setzen, falls vorhanden
                $key = ($this->auth_options['user_key_passwd']) ? $this->auth_options['user_key_passwd'] : null;
                $result = @ssh2_auth_pubkey_file($this->connect, $username, $this->auth_options['user_pub_key'], $this->auth_options['user_priv_key'], $key);
                break;
            case 3: // Hostfile (untested)
                $result = '';
                break;
            case null: //  none, sollte eigentlich nie klappen
            default:
                $result = @ssh2_auth_none($this->connect, $username);
                break;
        }
        // Fehlermeldung ausgeben
        if ($result === false)
        {
            $this->error = error_get_last();
            throw new Exception($this->error['message']);
        }
        elseif (is_array($result)) // Moegliche Methoden bei 'none' Auth
        {
            throw new Exception('Server accepts only this auth-methods: ' . implode(', ', $result));
        }
        else // ansonsten alles sauber :D
        {
            return true;
        }
        // absicherung
        return false;
    }
    /**
     * Durchsucht den Benutzer-SSH Ordner
     *
     * @param string $path
     */
    private function parse_ssh_dir ($path)
    {
        // Wir nehmen SPL, fuer php >= 5
        $dir = new DirectoryIterator($path);
        // tmp variable
        $found = array();
        // das muss noch angepasst werden
        foreach ($dir as $value)
        {
            // Es wurde ein Ordner .ssh gefunden
            if ($value->isDir() && $value != '.ssh')
                continue;
                // damit bin ich noch ungleucklich, nach switch umwandeln
            if ((string) $value == '.ssh')
                $found['ssh'] = true;
            if ((string) $value == 'id_dsa')
                $found['id_dsa'] = true;
            if ((string) $value == 'id_dsa.pub')
                $found['id_dsa.pub'] = true;
            if ((string) $value == 'id_rsa')
                $found['id_rsa'] = true;
            if ((string) $value == 'id_rsa.pub')
                $found['id_rsa.pub'] = true;
        }
        // Schluessel setzen oder erneut durchlaufen bis was gefunden wird
        if (@$found['id_dsa'] && @$found['id_dsa.pub'])
        {
            $this->auth_options['user_pub_key'] = str_replace('//', '/', $path . '/id_dsa.pub');
            $this->auth_options['user_priv_key'] = str_replace('//', '/', $path . '/id_dsa');
        }
        elseif (@$found['id_rsa'] && @$found['id_rsa.pub'])
        {
            $this->auth_options['user_pub_key'] = str_replace('//', '/', $path . '/id_dra.pub');
            $this->auth_options['user_priv_key'] = str_replace('//', '/', $path . '/id_rsa');
        }
        elseif (@$found['ssh'])
        {
            $this->parse_ssh_dir($path . '/.ssh');
        }
        else // an sonsten raus hier
        {
            throw new Exception('Could not find user key-files');
        }
        return false;
    }
}
?>