<?php
/**
 *  Wrapper fuer PDO bzw. MySQLi funktionen
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
class class_db
{
    /**
     * Konfigurationsdaten
     *
     * @var object
     * @see __construct()
     */
    private $config = null;
    /**
     * Engine für die DB-Anbindung
     *
     * @var string
     * @see __construct()
     */
    private $engine = null;
    /**
     * Verbindungslink zu der Datenbank
     *
     * @var object
     */
    private $connect = null;
    /**
     * Letzte aktive Verbindung
     *
     * @var object
     */
    private $recent_con = null;
    /**
     * Result einer Query-Abfrage
     *
     * @var object
     */
    private $result = null;
    /**
     * Aktuelle Query
     *
     * @var string
     */
    private $sql = null;
    /**
     * Anzahl der Queries
     *
     * @var integer
     * @see query()
     */
    private $query_count = 0;
    /**
     * Konstruktor der Klasse
     *
     * @param object $config
     * @param string $engine
     * @return object
     */
    public function __construct ($config, $engine = 'mysqli')
    {
        $this->config = $config;
        $this->engine = $engine;
        return $this->connect();
    }
    /**
     * Verbindungsaufbau zu der Datenbank
     *
     * @return object
     */
    private function connect ()
    {
        if ($this->engine == 'pdo')
        {
            $dns = sprintf("%s:dbname=%s;host=%s", $this->config->dbtype, $this->config->dbname, $this->config->dbhost);
            $this->connect = new PDO($dns, $this->config->dbuser, $this->config->dbpass);
        }
        else
        {
            $this->connect = new mysqli($this->config->dbhost, $this->config->dbuser, $this->config->dbpass, $this->config->dbname);
            if (mysqli_connect_errno())
            {
                throw new Exception(mysqli_connect_error(), mysqli_connect_errno());
            }
        }
        return $this->connect;
    }
    /**
     * F�gt eine Abfrage an der Datenbank durch
     * Gibt bei Bedarf ein Element zur�ck
     *
     * @param string $query 	SQL-Abfrage
     * @param integer $just_one Falls angegeben gibt ein Result zur�ck
     * @return object
     */
    function query ($query, $just_one = 0)
    {
        // Setzen von "aktuellen" Abfragen
        $this->recent_con = & $this->connect;
        $this->sql = & $query;
        $this->query_count ++;
        // Query abschicken
        $this->result = $this->connect->query($query);
        if (! $this->result && $this->engine == 'mysqli')
        {
            throw new Exception(mysqli_error($this->connect), mysqli_errno($this->connect));
        }
        // Falls angegeben ein Result abschicken
        if ($just_one)
        {
            $return = $this->fetch($this->result);
            return $return;
        }
        // Zur�ckgeben
        return $this->result;
    }
    /**
     * Gibt die Daten aus einer Abfrage zur�ck
     *
     * @param object $result
     * @return object
     */
    function fetch ($result = null)
    {
        $result = (is_null($result)) ? $this->result : $result;
        if ($this->engine == 'pdo')
        {
            return $result->fetch(PDO::FETCH_OBJ);
        }
        else
        {
            $data = $result->fetch_object();
            if (mysqli_error($this->connect))
            {
                throw new Exception(mysqli_error($this->connect), mysqli_errno($this->connect));
            }
            return $data;
        }
    }
    /**
     * Letzte Insert-ID in die Datenbank
     *
     * @param string $name
     * @return integer
     */
    function insert_id ($name = '')
    {
        if ($this->engine == 'pdo')
        {
            return $this->connect->lastInsertId($name);
        }
        else
        {
            return $this->connect->insert_id;
        }
    }
    /**
     * Gibt die Anzahl der Elemente in der Datenbank
     *
     * @return integer
     */
    public function num_rows ()
    {
        if ($this->engine == 'pdo')
        {
            return $this->result->rowCount();
        }
        else
        {
            return $this->result->num_rows;
        }
    }
    /**
     * Gibt die Anzahl der Elemente in der Datenbank
     *
     * @return integer
     */
    public function affected_rows ()
    {
        if ($this->engine == 'pdo')
        {
            return $this->result->rowCount();
        }
        else
        {
            return $this->connect->affected_rows;
        }
    }
    public function escape_string ($string)
    {
        if ($this->engine == 'pdo')
        {
            return $this->connect->quote($string);
        }
        else
        {
            return "'" . $this->connect->escape_string($string) . "'";
        }
    }
    /**
     * Beendet die Verbindung zur der Datenbank
     *
     */
    public function disconnect ()
    {
        if ($this->engine == 'pdo')
        {
            $this->connect = null;
        }
        else
        {
            $this->result->close();
            $this->connect->close();
        }
        unset($this->connect, $this->result);
    }
    public function __sleep ()
    {
        return array(
            'recent_con' , 
            'sql' , 
            'query_count' , 
            'result'
        );
    }
}
?>
