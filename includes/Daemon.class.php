<?php
/**
 * 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 *  
 */
declare(ticks = 1);
set_time_limit(0);

class Daemon
{
	/**
	 * Laufzeit für den Daemon
	 *
	 * @var int
	 */
	private $time = null;
	
	/**
	 * Fork Callback
	 *
	 * @var array
	 */
	private $fork = null;
	
    public function __construct ($options)
    {
    	
    }
    
    public function set_gid($id)
    {
    	if(is_int($id))
    	{
    		$group['gid']=$id;
    	}
    	elseif(is_string($id))
    	{ 
 			$group=posix_getgrnam('vds');
    	}
    	else 
    	{
    		die('undifined ID format');
    	}
    	// kurzer Check
		if(!isset($group['gid']) || $group['gid'] == 0)
		{
			die('user not found');
		}
		// ID setzen
		posix_setgid($group['gid']);
		posix_setegid($group['gid']);    	
    }
    
    public function set_uid($id)
    {
    	if(is_int($id))
    	{
    		$user['uid']=$id;
    	}
    	elseif(is_string($id))
    	{ 
 			$user=posix_getpwnam('vds');
    	}
    	else 
    	{
    		die('undifined ID format');
    	}
    	// kurzer Check
		if(!isset($user['uid']) || $user['uid'] == 0)
		{
			die('user not found');
		}
		// ID setzen
		posix_setuid($user['uid']);
		posix_seteuid($user['uid']);
    }
    
    public function set_child($callback, $params= null)
    {
    	// Prüft das Callback
		$this->_check_callback($callback);
		// Fork Array fühlen
		if(is_array($callback))
		{
			if(is_object($callback[0]))
			{
				$class = $callback[0];
			}
			else 
			{
				$class = $callback[0];
			}
			// Fork Array anlegen
			$this->fork=array($class, $callback[1]);	
			$this->fork_params = $params;
		}

    }
    
    
    public function sig_handler($signr)
    {
       	switch ($signr)
	    {
	        case SIGINT:
	        case SIGTERM:      
	            exit(0);
	            break;
	        case SIGHUP:
	            break;
	        default:
	            echo $signr;
	    }    	
    }  

    
    /**
     * Startet den Daemon
     *
     */
    public function handle()
    {
    	
    }
    
    
    public function set_handler($callback = null)
    {
 	    // Prüft das Callback
		$this->_check_callback($callback);
    	// Beenden
    	pcntl_signal(SIGTERM,	array($this, "sig_handler"));
		// Neustarten
		pcntl_signal(SIGHUP, 	array($this, "sig_handler"));
		// STRG+C = Beenden, nur fuer tests
		pcntl_signal(SIGINT, 	array($this, "sig_handler"));
		
    }
    
    public function set_error($callback = null)
    {
    	if(is_null($callback))
    	{
    		$callback=array('HMSError', 'echoraw_fault');
    	}
    	// Prüft das Callback
		$this->_check_callback($callback);
    	// Error Handler für die normale Fehlermeldungen
    	set_error_handler($callback);
		// Error Handler für die Exceptions
		set_exception_handler($callback);
    }  

    private function _check_callback($callback)
    {
       	if(is_array($callback))
    	{
    		if(count($callback)!=2)
    		{
    			die('array must containts 2 elements');
    		}
    		if(!is_object($callback[0]) && !class_exists($callback[0]))
    		{
    			die("class {$callback[0]} does not exists");
    		}
    	}
    	if(is_string($callback))
    	{
    		if(!function_exists($callback))
    		{
    			die("function {$callback} does not exists");
    		}
    	}    	
    }
}
?>