<?php defined('SYSPATH') or die('No direct script access.');
/** 
 * Custom exception handler for typical 404/500 error
 * 
 * @author Lysender
 *
 */
class Kohana_Exception extends Kohana_Kohana_Exception
{
    public static function handler(Throwable $e)
    {
        /**Custom page for errors */
       switch (get_class($e))
        {
            case 'HTTP_Exception_404':
                if (Kohana::$environment === Kohana::DEVELOPMENT)
				{
					return Kohana_Kohana_Exception::handler($e);
				} else {
					$response = new Response;
					$response->status(404);
					$view = new View('error/404'); // Path : application/views/error
					$view->message = $e->getMessage();
					echo $response->body($view)->send_headers()->body();
					if (is_object(Kohana::$log))
					{
						// Add this exception to the log
						Kohana::$log->add(Log::ERROR, $e);
						// Make sure the logs are written
						Kohana::$log->write();
					}  
				}              
                return TRUE;
                break;
                
            case 'Database_Exception':
				if (Kohana::$environment === Kohana::DEVELOPMENT)
				{
					return Kohana_Kohana_Exception::handler($e);
				} else {
					$response = new Response;
					$view = new View('error/200'); // Path : application/views/error
					$view->message = $e->getMessage();
					echo $response->body($view)->send_headers()->body();
					if (is_object(Kohana::$log))
					{
						// Add this exception to the log
						Kohana::$log->add(Log::ERROR, $e);
						// Make sure the logs are written
						Kohana::$log->write();
					}                
					return TRUE;
				}
				break;

            default:
               if (Kohana::$environment === Kohana::DEVELOPMENT)
				{
					return Kohana_Kohana_Exception::handler($e);
				} else {
					$response = new Response;
					$response->status(404);
					$view = new View('error/404'); // Path : application/views/error
					$view->message = $e->getMessage();
					echo $response->body($view)->send_headers()->body();
					if (is_object(Kohana::$log))
					{
						// Add this exception to the log
						Kohana::$log->add(Log::ERROR, $e);
						// Make sure the logs are written
						Kohana::$log->write();
					}  
				}              
                return TRUE;
                break;
        } 
    }
}

