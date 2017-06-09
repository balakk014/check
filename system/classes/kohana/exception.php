<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception {

    public static function handler(Throwable $e)
    {
        if (Kohana::$environment === Kohana::DEVELOPMENT OR Kohana::$is_cli)
        {
            // Pass to Kohana if we're in the development or command line environment
            parent::handler($e);
        }
        else
        {
            try
            {
                if (is_object(Kohana::$log))
                {
                    // Add this exception to the log
                    Kohana::$log->add(Log::ERROR, parent::text($e));

                    // Make sure the logs are written
                    Kohana::$log->write();
                }

                // Default parameters.
                $params = array
                (
                    'action'  => 500,
                    'message' => rawurlencode($e->getMessage())
                );

                if ($e instanceof Kohana_Http_Exception)
                {
                    // Set a valid HTTP status code
                    $params['action'] = $e->getCode();
                }

                // Get the error uri
                $uri = Route::get('error')->uri($params);

                // Error sub-request.
                echo Request::factory($uri)
                    ->execute()
                    ->send_headers()
                    ->body();
            }
            catch(Throwable $e)
            {
                // Clean the output buffer if one exists
                ob_get_level() and ob_clean();

                // Display the exception text
                echo parent::text($e), "\n",
                     parent::text(func_get_arg(0)), "\n";

                // Exit with an error status
                exit(1);
            }
        }
    }

} // End Kohana_Exception

