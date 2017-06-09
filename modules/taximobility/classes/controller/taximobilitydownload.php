<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/******************************************
* Contains Download Controller
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
********************************************/
//For controlling Downloads
class Controller_TaximobilityDownload extends Controller_Siteadmin
{
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $this->is_login();
        $session = Session::instance();
        $user_type=$session->get('user_type');
        if($user_type=='C'){  
            Message::error(__('invalid_access'));
            $this->request->redirect(URL_BASE."company/dashboard");
        }        
        else if($user_type=='M') {
            Message::error(__('invalid_access'));
            $this->request->redirect(URL_BASE."manager/dashboard");
        }
        $this->action = $this->request->action();
    }
    public function action_downloadfiles()
    {
        $file_path = $this->request->query( "file_path" );
        $name      = $this->request->query( "file_name" );
        $mime_type = $this->request->query( "mime_type" );
        if ( $file_path != "" ) {
            $file_split           = explode( '.', $file_path );
            $extension            = isset( $file_split[1] ) ? $file_split[1] : "";
            $ios_lang_path        = DOCROOT . SAMPLE_IOS_LANG_FILES;
            $ios_color_path       = DOCROOT . SAMPLE_IOS_COLORCODE_FILES;
            $android_lang_path    = DOCROOT . SAMPLE_ANDROID_LANG_FILES;
            $android_color_path   = DOCROOT . SAMPLE_ANDROID_COLORCODE_FILES;
            $ios_default_path     = DOCROOT . IOS_DEFAULT_CUSTOMIZE_FILES;
            $android_default_path = DOCROOT . ANDROID_DEFAULT_CUSTOMIZE_FILES;
            $web_custom_lang_path = CUSTOMLANGPATH.'i18n/';
            $web_default_lang_path= APPPATH.'i18n/';
            
            switch ( $extension ) {
                case "php":
                    $file = DOCROOT . 'application/i18n/' . $file_path;
                    break;
                case "xml":
                    $filesplit = explode( '_', $file_split[0] );
                    $namesplit = explode( '-', $name );
                    
                    if ( in_array( 'customize', $filesplit ) || in_array( 'default', $filesplit ) ) {
                        if ( isset( $namesplit[0] ) ) {
                            switch ( $namesplit[0] ) {
                                case 'Driver_Android':
                                    $file = $android_default_path . 'driver/' . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $android_lang_path . 'driver/' . $new_file_path;
                                    }
                                    break;
                                case 'Passenger_Android':
                                    $file = $android_default_path . 'passenger/' . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $android_lang_path . 'passenger/' . $new_file_path;
                                    }
                                    break;
                                case 'Android_Driver_colorcode':
                                    $file = $android_default_path . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $android_color_path . $new_file_path;
                                    }
                                    break;
                                case 'Android_Passenger_colorcode':
                                    $file = $android_default_path . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $android_color_path . $new_file_path;
                                    }
                                    break;
                                case 'IOS_Driver_colorcode':
                                    $file = $ios_default_path . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $ios_color_path . $new_file_path;
                                    }
                                    break;
                                case 'IOS_Passenger_colorcode':
                                    $file = $ios_default_path . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $ios_color_path . $new_file_path;
                                    }
                                    break;
                                case 'Web_lang_default':
                                    $file = $web_default_lang_path . $file_path;
                                    break;
                                case 'Web_lang_customize':
                                    $file = $web_custom_lang_path . $file_path;
                                    break;
                            }
                        } else {
                            Message::error( __( 'fail_upload_fail_info' ) );
                            $this->request->redirect( '/package/preferences' );
                        }
                    } else {
                        if ( isset( $namesplit[0] ) ) {
                            switch ( trim( $namesplit[0] ) ) {
                                case 'Driver_Android':
                                    $file = $android_lang_path . 'driver/' . $file_path;
                                    break;
                                case 'Passenger_Android':
                                    $file = $android_lang_path . 'passenger/' . $file_path;
                                    break;
                                case 'Android_Driver_colorcode':
                                    $file = $android_color_path . $file_path;
                                    break;
                                case 'Android_Passenger_colorcode':
                                    $file = $android_color_path . $file_path;
                                    break;
                                case 'IOS_Driver_colorcode':
                                    $file = $ios_color_path . $file_path;
                                    break;
                                case 'IOS_Passenger_colorcode':
                                    $file = $ios_color_path . $file_path;
                                    break;
                            }
                        } else {
                            Message::error( __( 'fail_upload_fail_info' ) );
                            $this->request->redirect( '/package/preferences' );
                        }
                    }
                    break;
                case "strings":
                    $filesplit = explode( '_', $file_split[0] );
                    $namesplit = explode( '-', $name );
                    if ( in_array( 'customize', $filesplit ) || in_array( 'default', $filesplit ) ) {
                        if ( isset( $namesplit[0] ) ) {
                            switch ( $namesplit[0] ) {
                                case 'Driver_IOS':
                                    $file = $ios_default_path . 'driver/' . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $ios_lang_path . 'driver/' . $new_file_path;
                                    }
                                    break;
                                case 'Passenger_IOS':
                                    $file = $ios_default_path . 'passenger/' . $file_path;
                                    if(file_exists($file))
                                    {
                                        $file = $file;
                                    }else{
                                        $new_file_path = str_replace('_default','',$file_path);
                                        $file = $ios_lang_path . 'passenger/' . $new_file_path;
                                    }
                                    break;
                            }
                        } else {
                            Message::error( __( 'fail_upload_fail_info' ) );
                            $this->request->redirect( '/package/preferences' );
                        }
                    } else {
                        if ( isset( $namesplit[0] ) ) {
                            switch ( $namesplit[0] ) {
                                case 'Driver_IOS':
                                    $file = $ios_lang_path . 'driver/' . $file_path;
                                    break;
                                case 'Passenger_IOS':
                                    $file = $ios_lang_path . 'passenger/' . $file_path;
                                    break;
                            }
                        } else {
                            Message::error( __( 'fail_upload_fail_info' ) );
                            $this->request->redirect( '/package/preferences' );
                        }
                    }
                    break;
            }
        }
        /*
        This function takes a path to a file to output ($file),  the filename that the browser will see ($name) and  the MIME type of the file ($mime_type, optional).
        */
        //Check the file premission
        if ( !is_readable( $file ) ) {
            Message::error( __( 'File not found or inaccessible!' ) );
            $this->request->redirect( '/package/preferences' );
        }
        $size             = filesize( $file );
        $name             = rawurldecode( $name );
        /* Figure out the MIME type | Check in array */
        $known_mime_types = array(
			"txt" => "text/plain",
            "strings" => "text/plain",
            "php" => "text/plain",
            "xml" => "application/atom+xml" 
        );
        if ( $mime_type == '' ) {
            $file_extension = strtolower( substr( strrchr( $file, "." ), 1 ) );
            if ( array_key_exists( $file_extension, $known_mime_types ) ) {
                $mime_type = $known_mime_types[$file_extension];
            } else {
                $mime_type = "application/force-download";
            }
        }
        //turn off output buffering to decrease cpu usage
        @ob_end_clean();
        // required for IE, otherwise Content-Disposition may be ignored
        if ( ini_get( 'zlib.output_compression' ) ) {
            ini_set( 'zlib.output_compression', 'Off' );
        }
        header( 'Content-Type: ' . $mime_type );
        header( 'Content-Disposition: attachment; filename="' . $name . '"' );
        header( "Content-Transfer-Encoding: binary" );
        header( 'Accept-Ranges: bytes' );
        /* The three lines below basically make the 
        download non-cacheable */
        header( "Cache-control: private" );
        header( 'Pragma: private' );
        header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
        // multipart-download and download resuming support
        if ( isset( $_SERVER['HTTP_RANGE'] ) ) {
            list( $a, $range ) = explode( "=", $_SERVER['HTTP_RANGE'], 2 );
            list( $range ) = explode( ",", $range, 2 );
            list( $range, $range_end ) = explode( "-", $range );
            $range = intval( $range );
            if ( !$range_end ) {
                $range_end = $size - 1;
            } else {
                $range_end = intval( $range_end );
            }
            $new_length = $range_end - $range + 1;
            header( "HTTP/1.1 206 Partial Content" );
            header( "Content-Length: $new_length" );
            header( "Content-Range: bytes $range-$range_end/$size" );
        } else {
            $new_length = $size;
            header( "Content-Length: " . $size );
        }
        /* Will output the file itself */
        $chunksize  = 1 * ( 1024 * 1024 ); //you may want to change this
        $bytes_send = 0;
        if ( $file = fopen( $file, 'r' ) ) {
            if ( isset( $_SERVER['HTTP_RANGE'] ) )
                fseek( $file, $range );
            while ( !feof( $file ) && ( !connection_aborted() ) && ( $bytes_send < $new_length ) ) {
                $buffer = fread( $file, $chunksize );
                print( $buffer ); //echo($buffer); // can also possible
                flush();
                $bytes_send += strlen( $buffer );
            }
            fclose( $file );
        } else
        //If no permissiion
            die( 'Error - can not open file.' );
        //die
        die();
    }
} // End Download
