<?php

/**
 * XMLRPC Server Klasse
 * 
 * Funktioniert als WebService und auch als Consolen Transport
 * Bietet Möglichkeit um Methoden als Funktionen oder als Klasse hinzuzufügen
 *
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@subpackage Server
 * 	@version 1.0
 * 
 * 	@id $Id$
 */
class server_xmlrpc implements interface_server
{
    /**
     * Request als XML
     * @var string
     */
    private $request = null;
    /**
     * Request geparst als Array
     * @var array
     */
    private $request_parsed = null;
    /**
     * Systemmethoden
     * 
     * @var array
     */
    private $system_methods = array('system.methodsignature', 
                                    'system.methodHelp', 'system.listmethods', 'system.describemethods', 
                                     'system.getcapabilities');

    /**
     * Speicher der internen Funktionsnamen
     *
     * @var array
     */
    private $methods = array();

    /**
     * Ressourse f�r die Klasse
     *
     * @var resource
     */
    private $server = null;

    /**
     * Name der Klasse die hinzugef�gt wurde
     *
     * @var string
     */
    private $class = null;

    /**
     * Typ der Methodenspeicherung
     *
     * @var string
     */
    private $type = null;

    /**
     * Style der Methodenansprache
     *
     * @var string
     */
    private $execute = 'default';

    /**
     * Konstruktor der Klasse
     *
     * @param $options array           
     */
    function __construct (array $options)
    {
        // Style der Methodenabfrage setzen
        if (isset($options['style']))
        {
            $this->execute = $options['style'];
        }
        // Eigenen Errorhandler benutzen f�r ALLE Fehlermeldungen
        set_error_handler(
                array("server_xmlrpc", "fault"), E_ALL);
        // XMLRPC Server initalisieren
        $this->server = xmlrpc_server_create();
    }

    /**
     * Startet den XMLRPC Server
     *
     * @param $type string           
     */
    public function handle ($type = 'http')
    {
        // set type
        $this->type = $type;
        // In Abh�ngigkeit von dem Typ die Requestdaten lesen
        switch ($type)
        {
            case 'http':
                global $HTTP_RAW_POST_DATA;
                $this->request = $HTTP_RAW_POST_DATA;
                break;
            case 'console':
                $this->request = fread(STDIN, 8192);
                break;
        }
        // Methode und Abfrageparameter bestimmen
        $methode = null;
        $this->request_parsed = xmlrpc_decode_request($this->request, &$methode);
       # file_put_contents('/tmp/request_' . microtime(), print_r($this->request, 1));
        // Methode/Funktion aufrufen
        if ($this->execute == strtolower('hms'))
        {
            // Sondermethoden abgreifen
            if(strtolower($methode) == 'system.multicall')
            {
                $this->multiCall();
            }
            elseif (in_array(strtolower($methode), $this->system_methods))
            {
                return xmlrpc_server_call_method($this->server, $this->request, null);
            }
            else 
            {    
                // [class::]methode(param1,param2,param3.....)
                echo $this->_call_method($methode, $this->request_parsed);
            }
        }
        else
        {
            // default style [class::]methode(methode,param[])
            echo xmlrpc_server_call_method($this->server, $this->request, null);
        }
    }
    /**
     * mehrere Funktionen auf ein Mal aufrufen
     */
    private function multiCall()
    {
       # file_put_contents('/tmp/data4_' . microtime(), print_r($this->request_parsed, 1));
         foreach($this->request_parsed[0] as $request)
         {
            # file_put_contents('/tmp/data3_' . microtime(), print_r($request, 1));
             echo $this->_call_method($request['methodName'], $request['params']);
         }   
    }
    /**
     * F�gt eine Funktion zum Server hinzu
     *
     * @param $name string           
     * @return boolean
     */
    public function addFunction ($name)
    {
        // setClass und addFunctions kann man nicht zeitgleich benutzen
        if ($this->type == 'object')
        {
            throw new Exception('you have already set one Class');
        }
        // Type setzen
        $this->type = 'function';
        // Methode registrieren
        xmlrpc_server_register_method($this->server, $name, $name);
        // und sich merken
        $this->methods[] = $name;
        return true;
    }

    /**
     * F�gt eine Klasse und deren public Methoden hinzu
     *
     * @param $obj string           
     * @return boolean
     */
    public function setClass ($obj)
    {
        // man kann nicht mehrere Klassen verwenden
        if (! is_null($this->class))
        {
            throw new Exception('Class already set');
        }
        // setClass und addFunctions kann man nicht zeitgleich benutzen
        if ($this->type == 'function')
        {
            throw new Exception(
                    'Some functions already set, use addFunction to add more');
        }
        // Klasse merken
        $this->class = $obj;
        // Informationen �ber die Klasse besorgen
        $desc = new ReflectionClass($this->class);
        foreach ($desc->getMethods() as $method)
        {
            // Parameter durchsuchen
            $args = array();
            foreach ($method->getParameters() as $param)
            {
                $args[] = '$' . $param->getName();
            }
            // Lambda Methode erstellen
            $args = implode(',', $args);
            // Name der Methode bestimmen
            $methodname = $method->getName();
            // Code erzeugen
            if ($method->isStatic() && $method->isPublic())
            {
                $code = "return {$this->class}::{$methodname}({$args});";
            }
            else
            {
                $code = "\$obj = new {$this->class}(); ";
                $code .= "return \$obj->{$methodname}({$args});";
            }
            // Funktion erstellen
            $function_name = create_function($args, $code);
            // Methode zum Server hinzuf�gen
            xmlrpc_server_register_method($this->server, $methodname, 
                    $function_name);
            // und sich merken
            $this->methods[] = $function_name;
        }
        // Type setzen
        $this->type = 'object';
        return true;
    }



    /**
     * Ruft eine Methode der Klasse auf
     *
     * @param $request string           
     * @return string
     */
    private function _call_method ($methode, $params)
    {
#        file_put_contents('/tmp/data_' . microtime(), print_r($methode, 1).print_r($params, 1));
        // Methode Aufrufen
        if ($this->type == 'function')
        {
            // Funktion aufrufen
            if (! function_exists($methode) && in_array($methode, 
                    $this->methods))
            {
                $this->fault('server', 'Methode not found');
            }
            $result = @call_user_func_array($methode, $params[0]);
        }
        else
        {
            // Klasse initalisieren und aufrufen
            $obj = new $this->class();
            if (! method_exists($obj, $methode))
            {
                $this->fault('server', 'Methode not found');
            }
            try
            {
                $result = @call_user_func_array(
                        array($obj, $methode), $params);
            }
            catch (Exception $e)
            {
                $this->fault('server', $e->getMessage());
            }
        }
        // Daten codiert zur�ckgeben
        return xmlrpc_encode($result);
    }

    /**
     *
     * @param $code unknown_type           
     * @param $string unknown_type           
     * @param $file unknown_type           
     * @param $line unknown_type           
     */
    public function fault ($code, $string, $file = null, $line = null)
    {
/*
        // Darf nur aufgerufen werden, wenn Server l�uft
        if (! $this->server)
        {
            die("Error: $string in '$file', line '$line'");
        }
*/
        // Header setzen
        header("Content-type: text/xml; charset=UTF-8");
        // Antwort vorbereiten
        $fileline = '';
        if (! is_null($file) && ! is_null($line))
        {
            $fileline = "in at $file:$line";
        }
        $_SERVER['HOST'] = (isset($_SERVER['HOST'])) ? $_SERVER['HOST'] : 'localhost';
        print
                (xmlrpc_encode(
                        array('faultCode' => $code, 
                                'faultString' => "Remote XMLRPC Error from " .
                                         $_SERVER['HOST'] . ": $string $fileline")));
        // Alles beenden
        die();
    }

    /**
     * Destruktor der Klasse
     *
     * Beendet den Server
     */
    function __destruct ()
    {
        // Server beenden
        xmlrpc_server_destroy($this->server);
        // Errorhandler wiederherstellen
        restore_error_handler();
    }
}
?>
