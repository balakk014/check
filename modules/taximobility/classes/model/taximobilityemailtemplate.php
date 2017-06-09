<?php defined('SYSPATH') or die('No direct script access.');
/****************************************************************
* Contains Emailtemplate Model
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************************************/
class Model_TaximobilityEmailtemplate extends Model
{
    public function __construct()
    {
        $this->session = Session::instance();
        $this->userid  = $this->session->get("userid");
    }
    public function emailtemplate($template_file, array $replace_variables, $content = "")
    {
        if (file_exists($template_file)) {
            $template = file_get_contents($template_file);
            $template = str_replace(TEMPLATE_CONTENT, $content, $template);            
            if (is_array($replace_variables)) {				
                $template = str_replace(array_keys($replace_variables), array_values($replace_variables), $template);
                return $template;
            }
        } else {
            return 1;
        }
    }
}
