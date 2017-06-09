<?php defined('SYSPATH') or die('No direct script access.');

class Kohana extends Kohana_Core {
    
    protected static $_paths = array(APPPATH, SYSPATH,CUSTOMLANGPATH);
    
    public static function modules(array $modules = NULL)
	{
		if ($modules === NULL)
		{
			// Not changing modules, just return the current set
			return Kohana::$_modules;
		}

		// Start a new list of include paths, APPPATH first
		$paths = array(APPPATH);

		foreach ($modules as $name => $path)
		{
			if (is_dir($path))
			{
				// Add the module to include paths
				$paths[] = $modules[$name] = realpath($path).DIRECTORY_SEPARATOR;
			}
			else
			{
				// This module is invalid, remove it
				throw new Kohana_Exception('Attempted to load an invalid or missing module \':module\' at \':path\'', array(
					':module' => $name,
					':path'   => Debug::path($path),
				));
			}
		}

		// Finish the include paths by adding SYSPATH
		$paths[] = SYSPATH;
                // Finish the include paths by adding SYSPATH
		$paths[] = CUSTOMLANGPATH;
		// Set the new include paths
		Kohana::$_paths = $paths;

		// Set the current module list
		Kohana::$_modules = $modules;

		foreach (Kohana::$_modules as $path)
		{
			$init = $path.'init'.EXT;

			if (is_file($init))
			{
				// Include the module initialization file once
				require_once $init;
			}
		}

		return Kohana::$_modules;
	}
        

	public static function deinit()
	{
		if (Kohana::$_init)
		{
			// Removed the autoloader
			spl_autoload_unregister(array('Kohana', 'auto_load'));

			if (Kohana::$errors)
			{
				// Go back to the previous error handler
				restore_error_handler();

				// Go back to the previous exception handler
				restore_exception_handler();
			}

			// Destroy objects created by init
			Kohana::$log = Kohana::$config = NULL;

			// Reset internal storage
			Kohana::$_modules = Kohana::$_files = array();
			Kohana::$_paths   = array(APPPATH, SYSPATH,CUSTOMLANGPATH);

			// Reset file cache status
			Kohana::$_files_changed = FALSE;

			// Kohana is no longer initialized
			Kohana::$_init = FALSE;
		}
	}

}
