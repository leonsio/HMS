<?php

/**
 * Klasse mit Consolenoperationen
 * 
 * Wird verwendet um CLI Befehle, Parameter und Flags zu analysieren
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *
 */
abstract class Console
{
    /**
     * Liste aller Befehle die durch Hilfe nicht ausgegeben werden sollen
     *
     * @var array
     */
    protected $cmd_ignore = array(
        '__construct' , 
        'out' , 
        'err' , 
        'in' , 
        '__destruct'
    );
    /**
     * Exitcode mit dem sich die Anwendung beenden soll
     *
     * @var int
     */
    protected $exit_code = 0;
    /**
     * Konstruktor der Klasse
     * 
     * Interpertiert die �bergebenen Parameter und ruft die entsprechenden Funktionen auf
     *
     */
    public function __construct ()
    {
        // f�r den Fall dass die Konstanten nicht definiert sind
        if (! defined('STDIN'))
        {
            define('STDIN', fopen('php://stdin', 'r'));
        }
        if (! defined('STDOUT'))
        {
            define('STDOUT', fopen('php://stdout', 'w'));
        }
        if (! defined('STDERR'))
        {
            define('STDERR', fopen('php://stderr', 'w'));
        }
        // Parameter parsen
        $params = $this->parse_params();
        // Falls keine Parameter angegeben wurden hilfe ausgeben
        if (! count($params['cmd']))
        {
        	$this->err();
            $this->err("ERROR: Sie haben keinen Befehl angegeben");
            $this->help();
        }
        // schauen was man aufrufen soll
        if (! method_exists($this, $params['cmd'][0]) && ! in_array($params['cmd'][0], $this->cmd_ignore))
        {
            $this->err("ERROR: Befehl {$params['cmd'][0]} ist unbekannt");
            $this->help();
        }
        else
        {
        	// Hilfe ist ein Sonderfall, es vertr�gt als zweiten Parameter einen Befehl
            if ($params['cmd'][0] == 'help' && isset($params['cmd'][1]))
            {
                $this->help($params['cmd'][1]);
            }
            // An sonsten alle Parameter und Flags an die gew�nschte Funktion �bergeben
            else
            {
                $this->{$params['cmd'][0]}($params['param'], $params['flag']);
            }
        }
    }
    /**
     * Interpretiert Eingabeparameter
     * 
     * Versteht Eingabem�glichkeiten in Form von
     * 
     * --param=value
     * -flag
     * cmd
     * 
     * Bsp. cli.php cmd --param1=value1 -flag1 --param2='ein langer string' -flag2
     *
     * @return array
     * @todo bei flags und cmds soll =... abgeschnitten werden
     */
    protected function parse_params ()
    {
        $args = $_SERVER['argv'];
        array_shift($args);
        $ret = array(
            'cmd' => array() , 
            'param' => array() , 
            'flag' => array()
        );
        foreach ($args as $arg)
        {
            // Is it a command? (prefixed with --)
            if (substr($arg, 0, 2) === '--')
            {
            	$data=explode('=',$arg,2);
            	$com = substr($data[0],2);
            	$ret['param'][$com]= !isset($data[1]) ? true : $data[1];
                continue;
            }
            // Is it a flag? (prefixed with -)
            elseif (substr($arg, 0, 1) === '-')
            {
                $ret['flag'][] = substr($arg, 1);
                continue;
            }
            else
            {
                $ret['cmd'][] = $arg;
                continue;
            }
        }
        return $ret;
    }
/*    
    function parse_params ()
    {
        $args = $_SERVER['argv'];
    	array_shift($args);
    	$args = join($args, ' ');
    	$match = null;
    	preg_match_all('/ (--\w+ (?:[= ] [^-]+ [^\s-] )? ) | (-\w+) | (\w+) /x', $args, $match);
    	$args = array_shift($match);
    	$ret = array(
    			'cmd' => array() ,
    			'param' => array() ,
    			'flag' => array()
    	);
    	foreach ($args as $arg)
    	{
    		// Is it a command? (prefixed with --)
    		if (substr($arg, 0, 2) === '--')
    		{
    			$value = preg_split('/[= ]/', $arg, 2);
    			$com = substr(array_shift($value), 2);
    			$value = join($value);
    			$ret['param'][$com] = ! empty($value) ? $value : true;
    			continue;
    		}
    		// Is it a flag? (prefixed with -)
    		if (substr($arg, 0, 1) === '-')
    		{
    			$ret['flag'][] = substr($arg, 1);
    			continue;
    		}
    		$ret['cmd'][] = $arg;
    		continue;
    	}
    	return $ret;
    }
*/        
    /**
     * Gibt eine erweiterte Hilfe zur m�glichen Befehlen an
     * 
     * Durch die Angabe von 'help BEFEHL' wird die Hilfeseite des jeweiligen Befehls aufgerufen
     * Auf dieser finden sich meistens Informationen zu den verwendeten Parametern und Flags
     *
     * @param string $cmd
     */
    public function help ($cmd = '')
    {
        if ($cmd == '' || count($cmd) == 0)
        {
            $this->out();
            $this->out("SYNTAX:\t	BEFEHL		[--PARAM1=... --PARAM2=... usw.]");
            $this->out();
            $this->out("Folgende Befehle sind möglich:");
            $this->out();
            $class = new ReflectionClass($this);
            foreach ($class->getMethods() as $method)
            {
                if (! in_array($method->name, $this->cmd_ignore))
                {
                    if ($method->isPublic())
                    {
                        $this->out("\t{$method->name}", 0);
                        $doc = stripslashes($method->getDocComment());
                        // Kommentarzeichen entfernen
                        $doc = str_replace('/**', '', $doc);
                        $doc = str_replace('*/', '', $doc);
                        $doc = str_replace(' *', '', $doc);
                        // Nur die 1. Zeile des Kommentars ausgeben
                        $lines = explode("\n", $doc);
                        $this->out("\t{$lines[1]}");
                    }
                }
            }
            
            die($this->out("\n\tFuer weitere Informationen geben Sie bitte 'help BEFEHL' an\n"));
        }
        // Falls eine Methode angefragt wurde
        if (method_exists($this, $cmd) && ! in_array($cmd, $this->cmd_ignore))
        {
            $method = new ReflectionMethod(get_class($this), $cmd);
            if ($method->isPublic() && ! in_array($cmd, $this->cmd_ignore))
            {
                $doc = stripslashes($method->getDocComment());
                // Kommentarzeichen entfernen
                $doc = str_replace('/**', '', $doc);
                $doc = str_replace('*/', '', $doc);
                $doc = str_replace(' *', '', $doc);
                // Parameterdefinitionen entferen
                $doc = preg_replace('!@(.*)\n!s', '', $doc);
                $this->out($doc, 0);
                $this->out();
            }
            else
            {
                die($this->err("ERROR: Befehl ist unbekannt"));
            }
        }
        else
        {
            die($this->err("ERROR: Befehl ist unbekannt"));
        }
    }
    /**
     * Pr�ft ob Variablen leer bzw. nicht gesetzt sind
     *
     * @param array $check
     * @param array $tocheck
     */
    protected function __check_empty ($check, $tocheck)
    {
        foreach ($check as $value)
        {
            if (! isset($tocheck[$value]) || $tocheck[$value] == '')
            {
                die($this->err("--{$value} darf nicht leer sein"));
            }
        }
    }
    /**
     * Gibt Ausgabe auf der Console aus
     *
     * @param string $message
     * @param boolean $newline
     */
    public function out ($message = null, $newline = true)
    {
        $newline = ($newline) ? "\n" : '';
        // Nachricht ausgeben
        fwrite(STDOUT, $message . $newline);
    }
    /**
     * Gibt eine Error Nachricht auf der Console aus
     *
     * @param string $message
     * @param boolean $newline
     */
    public function err ($message = null, $newline = true)
    {
        $newline = ($newline) ? "\n" : '';
        // Nachricht ausgeben, neue Zeile beginnen
        fwrite(STDERR, $message . $newline);
        // Bei Outputchannel = ERR exitcode setzen
        $this->exit_code = ($this->exit_code == 0) ? 1 : $this->exit_code;
    }
    /**
     * Liest Eingabe von der Console
     *
     * @param string $message
     * @param array $var
     */
    public function in ($message = null, &$var = null)
    {
    }
    
    
    /**
     * Destruktor der Klasse, beendet die Ausf�hrung mit einem Exit-Code
     *
     */
    public function __destruct ()
    {
        exit($this->exit_code);
    }
}
?>