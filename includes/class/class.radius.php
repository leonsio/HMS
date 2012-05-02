<?php
define('WISPR', 14122);
define('LOCATION_ID', 1);
define('LOCATION_NAME', 2);
define('RADIUS_UPDATE', 3);
/**
 * class RADIUS
 *
 *  Base class for RADIUS
 *
 * @package HMS 
 * 
 * $Id$
 */
class class_radius
{
    /**
     * List of RADIUS servers.
     * @var  array
     * @see  addServer(), putServer()
     */
    private $_servers = array();
    /**
     * Resource.
     * @var  resource
     * @see  request(), close()
     */
    private $res = null;
    /**
     * Request.
     * @var  integer
     * @see  request(), close()
     */
    private $req = null;
    /**
     * Username for authentication and accounting requests.
     * @var  string
     */
    private $username = null;
    /**
     * Password for plaintext-authentication (PAP).
     * @var  string
     */
    private $password = null;
    /**
     * List of known attributes.
     * @var  array
     * @see  dumpAttributes(), getAttributes()
     */
    private $attributes = array();
    /**
     * List of raw attributes.
     * @var  array
     * @see  dumpAttributes(), getAttributes()
     */
    private $rawAttributes = array();
    /**
     * List of raw vendor specific attributes.
     * @var  array
     * @see  dumpAttributes(), getAttributes()
     */
    private $rawVendorAttributes = array();
    /**
     * Adds a RADIUS server to the list of servers for requests.
     *
     * At most 10 servers may be specified.    When multiple servers 
     * are given, they are tried in round-robin fashion until a 
     * valid response is received
     *
     * @access public
     * @param  string  $servername   Servername or IP-Address
     * @param  integer $port         Portnumber
     * @param  string  $sharedSecret Shared secret
     * @param  integer $timeout      Timeout for each request
     * @param  integer $maxtries     Max. retries for each request          
     * @return void
     */
    public function addServer ($servername = 'localhost', $port = 0, $sharedSecret = 'testing123', $timeout = 3, $maxtries = 3)
    {
        $this->_servers[] = array(
            $servername , 
            $port , 
            $sharedSecret , 
            $timeout , 
            $maxtries
        );
    }
    /**
     * Returns an error message, if an error occurred.
     *
     * @access public
     * @return string
     */
    public function getError ()
    {
        return radius_strerror($this->res);
    }
    /**
     * Puts an attribute.
     *
     * @access public
     * @param  integer $attrib       Attribute-number
     * @param  mixed   $port         Attribute-value
     * @param  type    $type         Attribute-type
     * @return bool  true on success, false on error
     */
    public function putAttribute ($attrib, $value, $type = null)
    {
        $status = false;
        if ($type == null)
        {
            $type = gettype($value);
        }
        switch ($type)
        {
            case 'integer':
                return radius_put_int($this->res, $attrib, $value);
            case 'addr':
                return radius_put_addr($this->res, $attrib, $value);
            case 'string':
            default:
                return radius_put_attr($this->res, $attrib, $value);
        }
        return $status;
    }
    /**
     * Puts a vendor-specific attribute.
     *
     * @access public
     * @param  integer $vendor       Vendor (MSoft, Cisco, ...)
     * @param  integer $attrib       Attribute-number
     * @param  mixed   $port         Attribute-value
     * @param  type    $type         Attribute-type
     * @return bool  true on success, false on error
     */
    public function putVendorAttribute ($vendor, $attrib, $value, $type = null)
    {
        $status = false;
        if ($type == null)
        {
            $type = gettype($value);
        }
        switch ($type)
        {
            case 'integer':
                return radius_put_vendor_int($this->res, $vendor, $attrib, $value);
            case 'addr':
                return radius_put_vendor_addr($this->res, $vendor, $attrib, $value);
            case 'string':
            default:
                return radius_put_vendor_attr($this->res, $vendor, $attrib, $value);
        }
        return $status;
    }
    /**
     * Puts standard attributes.
     *
     * @access public
     */
    public function putStandardAttributes ()
    {
        // Benutzer informationen werden ebene hoeher gesetzt
        $this->putAttribute(RADIUS_NAS_IP_ADDRESS, '195.244.232.34', 'addr');
        $this->putAttribute(RADIUS_NAS_PORT_TYPE, RADIUS_WIRELESS_IEEE_802_11);
        $this->putVendorAttribute(WISPR, LOCATION_ID, 'isocc=DE,cc=49,ac=431,network=www.net-you.de');
        $this->putVendorAttribute(WISPR, LOCATION_NAME, 'hotspotlocation');
        // Optional 
        $this->putAttribute(RADIUS_NAS_IDENTIFIER, 'wlan.net-you.de');
    }
    /**
     * Configures the radius library.
     *
     * @access public
     * @param  string  $servername   Servername or IP-Address
     * @param  integer $port         Portnumber
     * @param  string  $sharedSecret Shared secret
     * @param  integer $timeout      Timeout for each request
     * @param  integer $maxtries     Max. retries for each request          
     * @return bool  true on success, false on error
     * @see addServer()
     */
    public function putServer ($servername, $port = 0, $sharedsecret = 'testing123', $timeout = 3, $maxtries = 3)
    {
        if (! radius_add_server($this->res, $servername, $port, $sharedsecret, $timeout, $maxtries))
        {
            return false;
        }
        return true;
    }
    /**
     * Sends a prepared RADIUS request and waits for a response
     *
     * @access public
     * @return mixed  true on success, false on reject, PEAR_Error on error
     */
    public function send ()
    {
        foreach ($this->_servers as $s)
        {
            // Servername, port, sharedsecret, timeout, retries
            if (! $this->putServer($s[0], $s[1], $s[2], $s[3], $s[4]))
            {
                return false;
            }
        }
        $this->putStandardAttributes();
        $req = radius_send_request($this->res);
        if (! $req)
        {
            return false;
        }
        switch ($req)
        {
            case RADIUS_ACCESS_ACCEPT:
                if (is_subclass_of($this, 'radius_acct'))
                {
                    return $this->raiseError('RADIUS_ACCESS_ACCEPT is unexpected for accounting');
                }
                return true;
            case RADIUS_ACCESS_REJECT:
                return false;
            case RADIUS_ACCOUNTING_RESPONSE:
                if (is_subclass_of($this, 'radius_pap'))
                {
                    return $this->raiseError('RADIUS_ACCOUNTING_RESPONSE is unexpected for authentication');
                }
                return true;
            default:
                return $this->raiseError("Unexpected return value: $req");
        }
    }
    /**
     * Reads all received attributes after sending the request.
     *
     * This methos stores know attributes in the property attributes, 
     * all attributes (including known attibutes) are stored in rawAttributes 
     * or rawVendorAttributes.
     * NOTE: call this functio also even if the request was rejected, because the 
     * Server returns usualy an errormessage
     *
     * @access public
     * @return bool   true on success, false on error
     */
    public function getAttributes ()
    {
        while ($attrib = radius_get_attr($this->res))
        {
            if (! is_array($attrib))
            {
                return false;
            }
            $attr = $attrib['attr'];
            $data = $attrib['data'];
            $this->rawAttributes[$attr] = $data;
            switch ($attr)
            {
                case RADIUS_FRAMED_IP_ADDRESS:
                    $this->attributes['framed_ip'] = radius_cvt_addr($data);
                    break;
                case RADIUS_FRAMED_IP_NETMASK:
                    $this->attributes['framed_mask'] = radius_cvt_addr($data);
                    break;
                case RADIUS_FRAMED_MTU:
                    $this->attributes['framed_mtu'] = radius_cvt_int($data);
                    break;
                case RADIUS_FRAMED_COMPRESSION:
                    $this->attributes['framed_compression'] = radius_cvt_int($data);
                    break;
                case RADIUS_SESSION_TIMEOUT:
                    $this->attributes['max_Time'] = radius_cvt_int($data);
                    break;
                case RADIUS_IDLE_TIMEOUT:
                    $this->attributes['timeout'] = radius_cvt_int($data);
                    break;
                case RADIUS_SERVICE_TYPE:
                    $this->attributes['service_type'] = radius_cvt_int($data);
                    break;
                case RADIUS_CLASS:
                    $this->attributes['radius_Class'] = radius_cvt_string($data);
                    break;
                case RADIUS_FRAMED_PROTOCOL:
                    $this->attributes['framed_protocol'] = radius_cvt_int($data);
                    break;
                case RADIUS_FRAMED_ROUTING:
                    $this->attributes['framed_routing'] = radius_cvt_int($data);
                    break;
                case RADIUS_FILTER_ID:
                    $this->attributes['filter_id'] = radius_cvt_string($data);
                    break;
                case 85:
                    $this->attributes['radius_Update'] = radius_cvt_int($data);
                    break;
                case RADIUS_VENDOR_SPECIFIC:
                    $attribv = radius_get_vendor_attr($data);
                    if (! is_array($attribv))
                    {
                        return false;
                    }
                    $vendor = $attribv['vendor'];
                    $attrv = $attribv['attr'];
                    $datav = $attribv['data'];
                    $this->rawVendorAttributes[$vendor][$attrv] = $datav;
                    if ($vendor == RADIUS_VENDOR_MICROSOFT)
                    {
                        switch ($attrv)
                        {
                            case RADIUS_MICROSOFT_MS_CHAP2_SUCCESS:
                                $this->attributes['ms_chap2_success'] = radius_cvt_string($datav);
                                break;
                            case RADIUS_MICROSOFT_MS_CHAP_ERROR:
                                $this->attributes['ms_chap_error'] = radius_cvt_string(substr($datav, 1));
                                break;
                            case RADIUS_MICROSOFT_MS_CHAP_DOMAIN:
                                $this->attributes['ms_chap_domain'] = radius_cvt_string($datav);
                                break;
                            case RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_POLICY:
                                $this->attributes['ms_mppe_encryption_policy'] = radius_cvt_int($datav);
                                break;
                            case RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_TYPES:
                                $this->attributes['ms_mppe_encryption_types'] = radius_cvt_int($datav);
                                break;
                            case RADIUS_MICROSOFT_MS_CHAP_MPPE_KEYS:
                                $demangled = radius_demangle($this->res, $datav);
                                $this->attributes['ms_chap_mppe_lm_key'] = substr($demangled, 0, 8);
                                $this->attributes['ms_chap_mppe_nt_key'] = substr($demangled, 8, RADIUS_MPPE_KEY_LEN);
                                break;
                            case RADIUS_MICROSOFT_MS_MPPE_SEND_KEY:
                                $this->attributes['ms_chap_mppe_send_key'] = radius_demangle_mppe_key($this->res, $datav);
                                break;
                            case RADIUS_MICROSOFT_MS_MPPE_RECV_KEY:
                                $this->attributes['ms_chap_mppe_recv_key'] = radius_demangle_mppe_key($this->res, $datav);
                                break;
                            case RADIUS_MICROSOFT_MS_PRIMARY_DNS_SERVER:
                                $this->attributes['ms_primary_dns_server'] = radius_cvt_string($datav);
                                break;
                        }
                    }
                    break;
            }
        }
        return $this->attributes;
    }
    /**
     * Frees resources.
     *
     * Calling this method is always a good idea, because all security relevant
     * attributes are filled with Nullbytes to leave nothing in the mem.
     *
     * @access public
     */
    public function close ()
    {
        if ($this->res != null)
        {
            radius_close($this->res);
            $this->res = null;
        }
        $this->password = str_repeat("\0", strlen($this->password));
    }
}
?>