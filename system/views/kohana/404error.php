<?php defined('SYSPATH') OR die("No direct access allowed.");
if(!defined('PROTOCOL'))
{
DEFINE('PROTOCOL','http');
DEFINE('URL_BASE',url::base(PROTOCOL,TRUE));	
}
?>

<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/media_style.css" />
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/style.css" />
<div class="error404_sec">
	<div class="center">
		<div class="error404_img_block">
			<div class="error404_img"> </div>
		</div>
		<div class="error404_info_block">
			<h1 class="error404_title">Oops, you found a Missing Page</h1>
			<p class="error404_info">Sorry, but there was some problem finding the page you requested.</p>
			<a class="error404_link" title="Taximobility" href="<?php echo URL_BASE;?>"><?php echo URL_BASE;?></a>
		</div>
	</div>
</div>

<?php /*
<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/common.css" />
<div id="banner_outer1" class="clearfix"> </div>
<div id="container_outer1 fl" class="clearfix">
                <div class="container clearfix">
                    <div class="about_content">
                        <div class="about_top">
                         
                            
                         
                        </div>
		    <div class="content">
                            <!--how it work-->
                            <div class="how_it_work_total1">
                                <div class="error_four">
                                     <h1>Page not found</h1><h1>404 </h1>
                                     
                                     <p>You've Lost your way.<a href="<?php echo URL_BASE;?>"> Need to go home?</a></p>
                                 </div>
                            </div>
            </div>
                        <div class="containerbox_bot"></div>
</div>
		
</div>
</div>
</div>
*/ ?>


