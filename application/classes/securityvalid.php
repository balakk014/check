<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Abstract Securityvalid integration.
 */
class Securityvalid
{
    public function __construct()
    {
        //Set Session Instance
        $this->session = Session::instance();
    }
   
    /*
     *  Desc:This functions shortens the string as specified in length given and apends the given string
     *  Type:Utility function
     *  Params:$string - string to shorten, $len -- result string length, $astr -- string to append with shortened string
    */
    public static function sanitize_inputs($input_array, $spl_array = array())
    {
        $convmap = array(
            0x80,
            0xffff,
            0,
            0xffff
        );
        foreach ($input_array as $key => $value) {
            if (in_array($key, $spl_array)) {
                $input_array[$key] = str_replace("'", '', str_replace("\\","",str_replace("\\n","",htmlentities(mb_encode_numericentity(self::stripspecialtags(self::gen_mysql_desanitize(urldecode($value))), $convmap, 'UTF-8'), ENT_QUOTES, "UTF-8", false))));
            } else {
                $input_array[$key] = str_replace("'", '', str_replace("\\","",str_replace("\\n","",self::gen_mysql_sanitize($value))));
            }
        }
        return $input_array;
    }
    
    /*
     *  Desc:This functions shortens the string as specified in length given and apends the given string
     *  Type:Utility function
     *  Params:$string - string to shorten, $len -- result string length, $astr -- string to append with shortened string
    */
    public static function sanitize_multiple_array_inputs($input_array, $spl_array = array())
    {
        $convmap = array(
            0x80,
            0xffff,
            0,
            0xffff
        );
        $first_array = $second_array = $third_array = array();
        foreach ($input_array as $general_key => $general_value)
        {
            if(is_array($general_value))
            {
                foreach ($general_value as $first_array_key => $first_array_value)
                {
                    if (in_array($first_array_key, $spl_array))
                    {
                        $first_array[$first_array_key] = str_replace("\\","",str_replace("\\n","",htmlentities(mb_encode_numericentity(self::stripspecialtags(self::gen_mysql_desanitize(urldecode($first_array_value))), $convmap, 'UTF-8'), ENT_QUOTES, "UTF-8", false)));
                    } else {
                        $first_array[$first_array_key] = str_replace("\\","",str_replace("\\n","",self::gen_mysql_sanitize($first_array_value)));
                    }
                }
                $input_array[$general_key] = $first_array;
            }else{
                if (in_array($general_key, $spl_array)) {
                    $input_array[$general_key] = str_replace("\\","",str_replace("\\n","",htmlentities(mb_encode_numericentity(self::stripspecialtags(self::gen_mysql_desanitize(urldecode($general_value))), $convmap, 'UTF-8'), ENT_QUOTES, "UTF-8", false)));
                } else {
                    $input_array[$general_key] = str_replace("\\","",str_replace("\\n","",self::gen_mysql_sanitize($general_value)));
                }
            } 
        }
        return $input_array;
    }
    
    
    /*
     *  Desc:This functions shortens the string as specified in length given and apends the given string
     *  Type:Utility function
     *  Params:$string - string to shorten, $len -- result string length, $astr -- string to append with shortened string
    */
    public static function sanitize_input_strings($input_string, $spl_array = array())
    {
        $convmap = array(
            0x80,
            0xffff,
            0,
            0xffff
        );
            $input_string = str_replace("\\","",str_replace("\\n","",self::gen_mysql_sanitize($input_string)));
        return $input_string;
    }
    
    /*
     * This function strips tags which is from advanced text editors and which only strips tags which leads
     * to any vulerability
     * form, input, select, textarea, iframe, link, script,
     * 
     * - str - string to strip the special tags
     * - except - you can exclude from any tag from the default one
     * - returns the striped text
     */
    
    public static function stripspecialtags($str = "", $except = array())
    {
        $spltags = array_diff(array(
            "form",
            "input",
            "select",
            "textarea",
            "iframe",
            "link",
            "script",
            "option",
            "head",
            "meta",
            "object",
            "embed"
        ), $except);
        return self::strip_spec_tags($str, $spltags);
    }
    
    public static function strip_spec_tags($str, $except)
    {
        //$str = html_entity_decode ( $str, ENT_QUOTES );
        $str        = self::gen_mysql_desanitize($str);
        $pptrnArr   = array();
        $replaceArr = array();
        foreach ($except as $tag) {
            $ptrnArr[]    = "/<\/?" . $tag . "(.|\s)*?>/";
            $replaceArr[] = '';
        }
        return preg_replace($ptrnArr, '', $str);
    }
    
    public static function gen_mysql_sanitize($str = "")
    {
        $convmap = array(
            0x80,
            0xffff,
            0,
            0xffff
        );
        //return Database::instance()->escape(htmlentities(mb_encode_numericentity(strip_tags(self::gen_mysql_desanitize($str)), $convmap, 'UTF-8'), ENT_QUOTES, "UTF-8", false));
        return htmlentities(mb_encode_numericentity(strip_tags(self::gen_mysql_desanitize($str)), $convmap, 'UTF-8'), ENT_QUOTES, "UTF-8", false);
    }
    
    public static function gen_mysql_desanitize($str = "")
    {
        $convmap = array(
            0x80,
            0xffff,
            0,
            0xffff
        );
        return urldecode(mb_decode_numericentity(str_replace(array(
            "'",
            "\""
        ), array(
            "&#039;",
            "&quot;"
        ), html_entity_decode($str, ENT_QUOTES, "UTF-8")), $convmap, 'UTF-8'));
    }
    
    /*
     *  Desc:This functions shortens the string as specified in length given and apends the given string
     *  Type:Utility function
     *  Params:$string - string to shorten, $len -- result string length, $astr -- string to append with shortened string
     */
    public static function string_shrink($string, $len, $astr)
    {
        $string = self::gen_mysql_desanitize($string);
        $strlen = strlen($string);
        if ($strlen <= $len) {
            return $string;
        }
        return self::gen_mysql_sanitize(mb_substr($string, 0, $len, 'UTF-8') . $astr);
    }
    

} // End Securityvalid
