<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
/****************************************************************
 * Contains CMS details
* @Package: Taximobility
* @Author: taxi Team
* @URL : taximobility.com
 ********************************************************************/
Class Controller_TaximobilityCms extends Controller_Website
{
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $siteusers      = Model::factory( 'siteusers' );
        $this->template = USERVIEW . "template";
    }
    /** for about us pages**/
    public function action_aboutus()
    {
        $cms                     = Model::factory( 'cms' );
        $content_cms             = $cms->get_cmscontent();
        $view                    = View::factory( USERVIEW . 'cms_pages' )->bind( 'cmscontent', $content_cms );
        $this->template->content = $view;
    }
}