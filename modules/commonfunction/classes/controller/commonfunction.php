<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Commonfunction Controler
 *
 * Is recomended to include all the specific module's controlers
 * in the module directory for easy transport of your code.
 *
 * @package    Commonfunction
 * @category   Base
 * @author     Myself Team
 * @copyright  (c) 2012 Myself Team
 * @license    http://kohanaphp.com/license.html
 */
class Controller_Commonfunction extends Controller {

    public function action_index()
    {
        
        // Instanciating the Module Class
        $Commonfunction = new Commonfunction;
        
        // Or Call a Statis Method
        //Commonfunction::show_category();

    }   
    
} // End Welcome
