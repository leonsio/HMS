<?php

/** 
 * @author        Leonid Kogan <leon@leonsio.com>
 * @copyright     Leonid Kogan <leon@leonsio.com>
 * @license       CC-by-nc-sa http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package       HMS
 * 
 * 
 */

class Modules
{
	private static $modules = null;
	
	public static function getModules()
	{
		if(!is_array(self::$modules))
		{
			$dir = new DirectoryIterator(APP_ROOT.'modules/system');
			foreach ($dir as $file)
			{
				if(!$file->isDot() && $file->isDir())
				{
					self::$modules[]=$file->getFilename();
				}
			}
			// Ausgabe
			return self::$modules;
		}
		else 
		{
			return self::$modules;
		}
	}
	
	public static function isActive($module)
	{
		if(!self::exists($module))
		{
			return false;
		}
		if(file_exists(APP_ROOT.'modules/system/'.$module.'/.disabled'))
		{
			return false;
		}
		else 
		{
			return true;
		}
	}
	
	public static function exists($module)
	{
		if(!in_array($module, self::getModules()))
		{
			return false;
		}
		else
		{
			return true;
		}		
	}
	
	public static function path($module)
	{
		return realpath(APP_ROOT.'modules/system/'.$module);
	}
}

?>