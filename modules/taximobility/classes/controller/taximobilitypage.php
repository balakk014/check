<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );
Class Controller_TaximobilityPage extends Controller_Website
{
    public function __construct( Request $request, Response $response )
    {
        parent::__construct( $request, $response );
        $siteusers      = Model::factory( 'siteusers' );
        $this->template = USERVIEW . "template";
        //passengers model
        $id             = $this->session->get( 'id' );
    }
    /** for about us pages**/
    public function action_aboutus()
    {
        $cms                     = Model::factory( 'cms' );
        $content_cms             = $cms->getcmscontent( 'about-us' );
        $view                    = View::factory( USERVIEW . 'cms_pages' )->bind( 'cmscontent', $content_cms );
        $this->meta_title        = isset( $content_cms[0]['meta_title'] ) ? $content_cms[0]['meta_title'] : "";
        $this->meta_keywords     = isset( $content_cms[0]['meta_keyword'] ) ? $content_cms[0]['meta_keyword'] : "";
        $this->meta_description  = isset( $content_cms[0]['meta_description'] ) ? $content_cms[0]['meta_description'] : "";
        $this->template->content = $view;
    }
    /** for Our action_privacy_policy pages**/
    public function action_privacy_policy()
    {
        $cms                     = Model::factory( 'cms' );
        $content_cms             = $cms->getcmscontent( 'privacy-policy' );
        $view                    = View::factory( USERVIEW . 'cms_pages' )->bind( 'cmscontent', $content_cms );
        $this->meta_title        = isset( $content_cms[0]['meta_title'] ) ? $content_cms[0]['meta_title'] : "";
        $this->meta_keywords     = isset( $content_cms[0]['meta_keyword'] ) ? $content_cms[0]['meta_keyword'] : "";
        $this->meta_description  = isset( $content_cms[0]['meta_description'] ) ? $content_cms[0]['meta_description'] : "";
        $this->template->content = $view;
    }
    /** for Our Terms & conditions pages**/
    public function action_terms_conditions()
    {
        $cms                     = Model::factory( 'cms' );
        $content_cms             = $cms->getcmscontent( 'terms-and-conditions' );
        $view                    = View::factory( USERVIEW . 'cms_pages' )->bind( 'cmscontent', $content_cms );
        $this->meta_title        = isset( $content_cms[0]['meta_title'] ) ? $content_cms[0]['meta_title'] : "";
        $this->meta_keywords     = isset( $content_cms[0]['meta_keyword'] ) ? $content_cms[0]['meta_keyword'] : "";
        $this->meta_description  = isset( $content_cms[0]['meta_description'] ) ? $content_cms[0]['meta_description'] : "";
        $this->template->content = $view;
    }
    /** for Help pages**/
    public function action_common_cms()
    {
        $pageurl                 = $this->request->param( 'pageurl' );
        $cms                     = Model::factory( 'cms' );
        $content_cms             = $cms->getcmscontent( $pageurl );
        $view                    = View::factory( USERVIEW . 'cms_pages' )->bind( 'cmscontent', $content_cms );
        $this->template->content = $view;
    }
}

