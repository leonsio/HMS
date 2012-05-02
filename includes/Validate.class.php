<?php
/**
 * Zentralle Validierungsklasse
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 	@version 1.0
 * 
 * 	$Id$
 */
class Validate
{
    /**
     * Enter description here...
     *
     * @param unknown_type $str
     * @param unknown_type $safe
     * @todo
     */
    public static function isSting ($str, $safe = false)
    {
    }
    /**
     * Check and Return a boolean value.
     *
     * checks to see if a variable is a boolean style value
     * works for "1", "true", "ja", "on" and "yes"
     * works for "0", "false", "nein", "off" and "no"
     *
     * @param mixed $str
     * @return bool
     */
    public function isBool ($str)
    {
        // German translation hack ;)
        if (strtoupper($str) == 'JA')
        {
            $str = 'YES';
        }
        if (strtoupper($str) == 'NEIN')
        {
            $str = 'NO';
        }
        // return filter result
        return filter_var($str, FILTER_VALIDATE_BOOLEAN) ? true : false;
    }
    /**
     * �berpr�ft eine IP auf ihre G�ltigkeit.
     *
     * 
     * @param string $str
     * @param array $options
     * @return boolean
     */
    public static function isIP ($str, $options = null)
    {
        if (function_exists('filter_var'))
        {
            // platzhalter definieren
            $flags = 0;
            // Typ bestimmen
            if (isset($options['type']))
            {
                $flags = ($options['type'] == 4) ? $flags | FILTER_FLAG_IPV4 : $flags | FILTER_FLAG_IPV6;
            }
            if (isset($options['nopriv']) && $options['nopriv'] == true)
            {
                $flags = $flags | FILTER_FLAG_NO_PRIV_RANGE;
            }
            if (isset($options['nores']) && $options['nores'] == true)
            {
                $flags = $flags | FILTER_FLAG_NO_RES_RANGE;
            }
            if (! filter_var($str, FILTER_VALIDATE_IP, array(
                'flags' => $flags
            )) === FALSE)
            {
                throw new Exception('IP address is not valid or has a wrong format');
            }
            else
            {
                return true;
            }
        }
        else
        {
            if (! isset($options['type']))
            {
                if (self::isIPv4($str) || self::isIPv6($str))
                {
                    return true;
                }
            }
            else
            {
                $type = 'isIPv' . intval($options['type']);
                if (method_exists('SNLvalidate', $type))
                {
                    self::$type($str);
                }
            }
        }
    }
    /**
     * �berpr�ft eine Email auf ihre G�ltigkeit.
     *
     * Als erweiteren Optionen ist es m�glich beim Mailserver die Email zu �berpr�fen
     * 
     * @param string $str
     * @param array $options
     * @return boolean
     */
    public static function isEmail ($str, $extended = false)
    {
        // Part 0. use filter-exctension, if installed
        if (function_exists('filter_var'))
        {
            if (filter_var($str, FILTER_VALIDATE_EMAIL) === FALSE)
            {
                throw new Exception('Email address is not valid');
            }
        } // Part 1. split email and length check
        else
        {
            $mail = explode('@', $str);
            if (($mail[0] && strlen($mail[0]) <= 64) && ($mail[1] && strlen($mail[1]) <= 255))
            {
                // Part 2. IP-check
                if (self::isIP($mail[1]))
                {
                    throw new Exception('Domain-part can not be IP');
                }
                // Part 3. content check
                // local-part
                if (! preg_match("@^[A-Za-z0-9!#$%&'*+-/=?^_`{|}~][A-Za-z0-9!#$%&'*+-/=?^_`{|}~\.]{0,63}$@i", $mail[0]))
                {
                    throw new Exception('Email local-part is not valid');
                }
                // domain-part
                if (! self::isDomain($mail[1]))
                {
                    throw new Exception('Email domain-part is not valid');
                }
            }
            else
            {
                throw new Exception('Email length is not RFC2822 conform');
            }
        }
        // Part 4. extended Mailserver check
        if ($extended)
        {
            // try to find the mailserver
            if (@getmxrr($mail[1], $MXHost))
            {
                $ConnectAddress = $MXHost[0];
            }
            else
            {
                $ConnectAddress = $mail[1];
            }
            // Verbindung aufbauen
            $Connect = @fsockopen($ConnectAddress, 25);
            // @todo: muss fuer greylisting angepasst werden
            if ($Connect)
            {
                if (ereg("^220", $Out = fgets($Connect, 1024)))
                {
                    // kennt server die Email?
                    fputs($Connect, "HELO {$_SERVER['HTTP_HOST']}\r\n");
                    $Out = fgets($Connect, 1024);
                    fputs($Connect, "MAIL FROM: <{$mail}>\r\n");
                    $From = fgets($Connect, 1024);
                    fputs($Connect, "RCPT TO: <{$mail}>\r\n");
                    $To = fgets($Connect, 1024);
                    fputs($Connect, "QUIT\r\n");
                    fclose($Connect);
                    if (! ereg("^250", $From) || ! ereg("^250", $To))
                    {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }
        else
        {
            return true;
        }
    }

    /**
     * �berpr�ft ein Datum auf ihre G�ltigkeit
     * 
     * Folgende Optionen werden unterst�tzt:
     * 
     * options[output]     = UNIX_TIMESTAMP  => Ausgabe des Datums als UNIX Timestamp
     * options[output]     = MYSQL_DATETIME  => Ausgabe des Datums als MYSQL DATETIME Format
     * options[null]       = true/false      => Leeres Datum akzeptieren ?    
     * 
     * Die Eingabe kann in Deutsch/Englisch erfolgen
     *
     * @param string $date
     * @param array $options 
     * @return mixed
     */
    public static function isDate ($date, $options = "")
    {
        // Leerzeichen am Anfang und Ende entfernen
        $date = trim($date);
        $monate = array(
            "januar" => "1" , 
            "februar" => "2" , 
            "m&auml;rz" => "3" , 
            "april" => "4" , 
            "mai" => "5" , 
            "juni" => "6" , 
            "juli" => "7" , 
            "august" => "8" , 
            "september" => "9" , 
            "oktober" => "10" , 
            "november" => "11" , 
            "dezember" => "12" , 
            "january" => "1" , 
            "february" => "2" , 
            "march" => "3" , 
            "may" => "5" , 
            "june" => "6" , 
            "july" => "7" , 
            "october" => "10" , 
            "december" => "12" , 
            "jan" => "1" , 
            "feb" => "2" , 
            "m&auml;r" => "3" , 
            "apr" => "4" , 
            "jun" => "6" , 
            "jul" => "7" , 
            "aug" => "8" , 
            "sep" => "9" , 
            "okt" => "10" , 
            "nov" => "11" , 
            "dez" => "12" , 
            "mar" => "3" , 
            "oct" => "10" , 
            "dec" => "12"
        );
        // Leeres Datum zulassen
        if (($date == "" || $date == "0000-00-00 00:00:00") && $options["null"])
            return true;
            // Default false
        $check = 0;
        // Datum analysieren und zerlegen
        /*
                Europaische Formate
                01.01.2004  1.1.04
                01,03,2004  03/12/2004
        */
        if (preg_match("!^([\d]{1,2})[\.|\,|\/]([\d]{1,2})[\.|\,|\/]([\d]{2,4})[\s]*(([\d]{1,2})\:([\d]{1,2})(\:([\d]{1,2}))?(\s?(pm|am))?)?$!i", $date, $format))
        {
            $tag = $format[1];
            $monat = $format[2];
            $jahr = $format[3];
        } /*
                US Format
                2003-12-30
        */
        elseif (preg_match("!^([\d]{2,4})\-([\d]{1,2})\-([\d]{1,2})[\s]*(([\d]{1,2})\:([\d]{1,2})(\:([\d]{1,2}))?(\s?(pm|am))?)?$!i", $date, $format))
        {
            $tag = $format[3];
            $monat = $format[2];
            $jahr = $format[1];
        } /*
                20040228
        */
        elseif (preg_match("!^((([\d]{8})))[\s]*(([\d]{1,2})\:([\d]{1,2})(\:([\d]{1,2}))?(\s?(pm|am))?)?$!i", $date, $format))
        {
            $tag = substr($format[1], - 2);
            $monat = substr($format[1], 4, 2);
            $jahr = substr($format[1], 0, 4);
        } /*
                14.02.{aktuelles jahr}
        */
        elseif (preg_match("!^(([\d]{1,2})\.([\d]{1,2})\.)[\s]*(([\d]{1,2})\:([\d]{1,2})(\:([\d]{1,2}))?(\s?(pm|am))?)?$!i", $date, $format))
        {
            $tag = $format[2];
            $monat = $format[3];
            $jahr = date("Y");
        } /*
                18. Mai 2005
        */
        elseif (preg_match("!^([\d]{1,2})[\.|\,]\s([a-zA-Z]+)[\s]([\d]{2,4})[\s]*(([\d]{1,2})\:([\d]{1,2})(\:([\d]{1,2}))?(\s?(pm|am))?)?$!i", $date, $format))
        {
            $tag = $format[1];
            $jahr = $format[3];
            $format[2] = htmlentities(strtolower($format[2]));
            if (array_key_exists($format[2], $monate))
                $monat = $monate[$format[2]];
            else
                return false;
        } /*
                Mai 18, 2005
        */
        elseif (preg_match("!^([a-z]+)[\s]([\d]{1,2})[\,|\.]\s([\d]{2,4})[\s]*(([\d]{1,2})\:([\d]{1,2})(\:([\d]{1,2}))?(\s?(pm|am))?)?$!i", $date, $format))
        {
            $tag = $format[2];
            $jahr = $format[3];
            $format[1] = htmlentities(strtolower($format[1]));
            if (array_key_exists($format[1], $monate))
                $monat = $monate[$format[1]];
            else
                return false;
        }
        $stunde = ($format[5] == "") ? $stunde = 0 : $stunde = $format[5];
        $minute = ($format[6] == "") ? $minute = 0 : $minute = $format[6];
        $sekunde = ($format[8] == "") ? $sekunde = 0 : $sekunde = $format[8];
        $pm_am = $format[10];
        // Zeit ueberprufung
        if (($pm_am != "" && ($stunde > 12 || $stunde < 1)) || $stunde >= 25 || $minute >= 60 || $sekunde >= 60)
            return false;
        if ($pm_am == "pm")
            $stunde = 12 + $stunde;
        if (strlen($jahr) == 2)
        {
            if ($jahr > 69)
                $jahr = 1900 + $jahr;
            else
                $jahr = 2000 + $jahr;
        }
        // Jahr kann nicht 3 stellig sein
        if (strlen($jahr) == 3)
            return false;
        $check = @checkdate($monat, $tag, $jahr);
        if ($check || $options["nocheck"])
        {
            switch ($options["output"])
            {
                case "UNIX_TIMESTAMP":
                    $string = mktime($stunde, $minute, $sekunde, $monat, $tag, $jahr);
                    break;
                case "MYSQL_DATETIME":
                    $string = $jahr . "-" . $monat . "-" . $tag . " " . $stunde . ":" . $minute . ":" . $sekunde;
                    break;
            }
        }
        if (! isset($options["output"]))
            return $check;
        else
            return $string;
    }
    /**
     * �berpr�ft eine Domain auf ihre G�ltigkeit.
     *
     * Als erweiteren Optionen ist es m�glich DNS-Checks vorzunehmen oder TLD zu �berpr�fen
     * 
     * @param string $str
     * @param array $options
     * @return boolean
     */
    public static function isDomain ($str, $options = null)
    {
        // delete whitespaces
        $str = trim($str);
        // last point is not important for the check
        if (substr($str, - 1) == '.')
            $str = substr($str, 0, - 1);
            // define depth
        $levels = explode('.', $str);
        // RFC Length check
        if (strlen($str) > 255)
        {
            throw new Exception('Domain length is invalid');
        }
        // Simple syntax check
        if (! preg_match("/^[\w\.\-\_]+$/", $str))
        {
            throw new Exception('Domain has wrong syntax');
        }
        // Minimal level check
        if (isset($options['minlevel']) && $options['minlevel'] >= 1)
        {
            if (count($levels) < $options['minlevel'])
            {
                throw new Exception('Domain lavel is invalid');
            }
        }
        // TLD check ( tld-list comes from root server )
        if (isset($options['tldcheck']) && $options['tldcheck'] == true)
        {
            if (file_exists(SNL_ROOT_DIR . '/$share$/tld_list'))
            {
                $tlds = unserialize(file_get_contents(SNL_ROOT_DIR . '/$share$/tld_list'));
            }
            else
            {
                throw new Exception('TLD data not found');
            }
            if (! in_array(strtoupper(array_pop($levels)), $tlds))
            {
                throw new Exception('Unknown TLD');
            }
        }
        // DNS check, needs internet connection to the domain
        if (isset($options['dnscheck']) && $options['dnscheck'] == true)
        {
            if (! @checkdnsrr($str, 'ANY'))
            {
                throw new Exception('DNS check failed');
            }
        }
        // Return OKay
        return true;
    }
    /**
     * Prueft ob ein String Sonderzeichen enthaelt
     *
     * @param string $str
     * @param boolean $convert
     * @return boolean
     */
    public static function isSafeString ($str, $convert = false)
    {
        $pattern = "[a-zA-Z0-9]";
        if (trim($str) == "")
            return false;
        $str_arr = str_split($str);
        foreach ($str_arr as $id => $char)
        {
            if (! preg_match("!$pattern!", $char))
            {
                if ($convert)
                    unset($str_arr[$id]);
                else
                    return false;
            }
        }
        if ($convert)
            return implode($str_arr);
        else
            return true;
    }
    /**
     * Ueberpruft ob die IP der IPv4 Form entspricht
     *
     * @param string $str
     * @return boolean
     * @todo options-array anlegen und erweitere Optionen einbauen
     */
    public static function isIPv4 ($str, $blacklist = null)
    {
        if (trim($str) == "")
            return false;
            // bitte die verbotenen IPs selbst eintragen
        $block = array(
            "0.0.0.0" , 
            "255.0.0.0" , 
            "255.255.0.0" , 
            "255.255.255.0" , 
            "255.255.255.255"
        );
        if (is_array($blacklist))
        {
            $block = array_merge($block, $blacklist);
        }
        // hilfsvariable
        $check=null;
        // implode('|', $block )
        foreach ($block as $v)
        {
            $check .= $v . "|";
        }
        $check = substr($check, 0, - 1);
        if (preg_match("!$check!", $str))
            return false;
        if (function_exists('filter_var'))
        {
            if (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) === FALSE)
            {
                throw new Exception('IP-Address has wrong format, please use XXX.XXX.XXX.XXX');
            }
            else
            {
                return true;
            }
        }
        $num = "([01]?\d\d?|2[0-4]\d|25[0-5])";
        if (! preg_match("!^$num\.$num\.$num\.$num$!", $str))
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * Pr�ft eine IPv6 Adresse auf ihre G�ltigkeit
     *
     * @param string $str
     * @return boolean
     */
    public static function isIPv6 ($str)
    {
        // vorerst nur mit der Filter-Erweiterung
        if (! function_exists('filter_var'))
        {
            throw new Exception('filter-extension is not installed');
        }
        // check
        if (filter_var($str, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) === FALSE)
        {
            return false;
        }
        else
        {
            return true;
        }
    }
    /**
     * Pr�ft die Sicherheit eines Paswords
     *
     * @param string $str
     * @return boolean
     */
    public static function safe_password ($str)
    {
        // mind 7 zeichen
        if (strlen($str) < 8)
            return false;
            // keine leer zeichen
        if (! preg_match("!^\S*$!", $str))
            return false;
            // mind 2 zahlen
        if (! preg_match("![\d]{1}.*[\d]{1}!", $str))
            return false;
        return true;
    }

    /**
     * 
     * This function takes 2 arguments, an IP address and a "range" in several
     * different formats.
     * Network ranges can be specified as:
     * 1. Wildcard format:     1.2.3.*
     * 2. CIDR format:         1.2.3/24  OR  1.2.3.4/255.255.255.0
     * 3. Start-End IP format: 1.2.3.0-1.2.3.255
     * The function will return true if the supplied IP is within the range.
     * Note little validation is done on the range inputs - it expects you to
     * use one of the above 3 formats.
     * 
     * Source website: http://www.pgregg.com/projects/php/ip_in_range/
     * 
     * @param string $ip
     * @param mixed $range
     * @return boolean
     */
    public static function isIPinRange($ip, $range) 
    {
        // Range can be array of ranges
        if(is_array($range))
        {
            // set output status
            $status=false;
            // process each range
            foreach($range as $entity)
            {
                $result = self::isIPinRange($ip, $entity);
                // set status if found
                if($result == true)
                {
                    $status = true;
                }    
            }
            // return status
            return $status;
        }
        // check if $ip is valid
        if(!Validate::isIPv4($ip)) return false;
        // IP = RANGE
        if($ip==$range) return true;
        // range in format d.d.d.d/d
        if (strpos($range, '/') !== false) 
        {
            // $range is in IP/NETMASK format
            list($range, $netmask) = explode('/', $range, 2);
            if (strpos($netmask, '.') !== false) 
            {
                // $netmask is a 255.255.0.0 format
                $netmask = str_replace('*', '0', $netmask);
                $netmask_dec = ip2long($netmask);
                return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
            } 
            else 
            {
                // $netmask is a CIDR size block
                // fix the range argument
                $x = explode('.', $range);
                while(count($x)<4) $x[] = '0';
                list($a,$b,$c,$d) = $x;
                $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
                $range_dec = ip2long($range);
                $ip_dec = ip2long($ip);
    
                # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
                #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));
    
                # Strategy 2 - Use math to create it
                $wildcard_dec = pow(2, (32-$netmask)) - 1;
                $netmask_dec = ~ $wildcard_dec;
    
                return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
            }
        } 
        else 
        {
            // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
            if (strpos($range, '*') !==false) 
            { // a.b.*.* format
                // Just convert to A-B format by setting * to 0 for A and 255 for B
                $lower = str_replace('*', '0', $range);
                $upper = str_replace('*', '255', $range);
                $range = "$lower-$upper";
            }
    
            if (strpos($range, '-')!==false) 
            { // A-B format
                list($lower, $upper) = explode('-', $range, 2);
                $lower_dec = (float)sprintf("%u",ip2long($lower));
                $upper_dec = (float)sprintf("%u",ip2long($upper));
                $ip_dec = (float)sprintf("%u",ip2long($ip));
                return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
            }
            // Return    
            return false;
        }
    }
}
?>
