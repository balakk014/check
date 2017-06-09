<?php defined('SYSPATH') OR die('No direct access allowed.');?>
<!DOCTYPE html>
<html>
    <head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
	<!-- <META HTTP-EQUIV="EXPIRES" CONTENT="Mon, 22 Jul 2002 11:12:01 GMT"> -->	
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0" />
	<link rel="shortcut icon" href="<?php echo URL_BASE; ?>public/<?php echo UPLOADS; ?>/favicon/<?php echo $footer_contents['site_favicon']; ?>" --type="image/x-icon" />
	      <title><?php echo $page_title . ' | ' . CLOUD_SITENAME; ?></title>

	<link type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/bootstrap.css" rel="stylesheet" />
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/dashboard/jquery.noty.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/dashboard/noty_theme_default.css" />
	<link type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/admin_style.css" rel="stylesheet" />
	<!--Start to Include the tab menu css -->
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/glowtabs.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/admin_new/fullcalendar.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/admin_new/ui_custom.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/admin/css/admin_new/media_style.css" />
	<!--End-->
	<?php if (isset($_SESSION['userid'])) { ?>
    	<script>
    	    var language = '<?php echo $js_language; ?>';
    	</script>
	<?php } ?>
	<?php
	if ($action == 'transaction_details') {
	    if (SHOW_MAP != 1) {
		?>
		<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&sensor=true"></script>	
	    <?php } ?>
    	<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/gmaps.js"></script>
	<?php }
	?>
	<?php
	$alert_message = array('email_exists' => 'Email already exists', 'phone_exists' => 'Phone number already exists');
	$encoded_message = json_encode($alert_message);
// Common Variable For TDispatch 
	?>
	<script>
	    var URL_BASE = "<?php echo URL_BASE; ?>";
	    var IMGPATH = "<?php echo IMGPATH; ?>";
	    var SHOW_MAP = "<?php echo SHOW_MAP; ?>";
	    var ACTION = "<?php echo $action; ?>";
	    var LOCATION_LATI = "<?php echo LOCATION_LATI; ?>";
	    var LOCATION_LONG = "<?php echo LOCATION_LONG; ?>";
	    var ALERT_MESSAGES = '<?php echo $encoded_message; ?>';
	    var DEFAULT_DATE_FORMAT_SCRIPT = '<?php echo DEFAULT_DATE_FORMAT_SCRIPT; ?>';
	    var DEFAULT_TIME_SHOW = '<?php echo DEFAULT_TIME_SHOW; ?>';
	    var DEFAULT_TIME_FORMAT_SCRIPT = '<?php echo DEFAULT_TIME_FORMAT_SCRIPT; ?>';
	</script>
	<?php
	$session = Session::instance();
	$menuEnable = $session->get("left_menu_enable");
	?>
       
    </head>
    <body <?php
	if ($action == 'login' || $action == 'forgot_password') {
	    echo 'class="nobg loginPage"';
	}
	?> <?php if ($menuEnable != '' && $menuEnable == 0) { ?> class="sidebar_hide sidebar-minize sidebar-collapse"<?php } ?> >
	<input type="hidden" name="baseurl" id="baseurl" value="<?php echo URL_BASE; ?>">

	<?php
	if ($action == 'login' || $action == 'forgot_password') {
	    echo new View("admin/header");
	}
	?>
	<div class="wrapper">
            <?php if ($action != 'editprofile_user') { ?>
	    <header class="main_header">
		<div class="header clearfix">
		    <?php if (($action != 'login' && $action != 'forgot_password')) {?>
			<?php
			if ($action != 'login') {
			    echo new View("admin/header_inner");
			}
			?>
    		</div>
    	    </header>
            <?php // if ($action != 'editprofile_user') { ?>
    	    <div class="main-sidebar">
    		<section class="sidebar">
    <?php echo new View("admin/admin_menu");  ?>
    		</section>
    	    </div>
            <?php } ?>
    	    <div id="container"><!-- Content container -->
    		<!-- Content -->
    		<div id="content" <?php if ($controller == "tdispatch") { ?>class="dispatch_content"<?php } ?> >
    		    <!-- Breadcrumbs line -->
    		    <div class="crumbs">
    			<ul id="breadcrumbs" class="breadcrumb">
				    <?php if (($controller != "admin" || $controller != "manager" || $controller != "company") && $action != "dashboard" && $action!="home") { ?>
				    <li>
					<?php
					if ($usertype == 'C') {
					    $link = URL_BASE . 'company/dashboard';
					} else if ($usertype == 'M') {
					    $link = URL_BASE . 'manager/dashboard';
					} else {
					    $link = URL_BASE . 'package/home';
					}
					$atag_start = '<a href=' . $link . ' title=' . $link . '>';
					$atag_end = '</a>';
					?>	
				<?php echo $atag_start . __('account_breadcrumb') . $atag_end; ?>
				    </li>
			    <?php } ?>
    			    <li><p><?php echo $page_title; ?></p></li>
    			</ul>
			    <?php /* if($_SESSION['user_type'] == 'C' || $_SESSION['user_type'] == 'M') {
			      if($controller=='tdispatch' && ($action=='managebooking' || $action=='recurrentbooking' || $action=='frequent_location' || $action=='frequent_journey' || $action=='tdispatch_settings')) {?>
			      <div class="button blackB" style="float:right;margin:5px;">
			      <input type="button" name="add_booking_popup" style="padding: 7px 18px 8px;" id="add_booking_popup" value="<?php echo __('add_booking'); ?>" title="<?php echo __('add_booking'); ?>" >
			      </div>
			      <?php } } */ ?>
    		    </div>
    		    <!-- breadcrumbs line -->
    		    <!-- General form elements -->
    		    <div id="map12"> </div>

    <?php if ($action != "dashboard" && $action != "withdrawrequest" && $action != "withdrawdeatil") { ?>  <div class="right_lay"> 

    			<div class="row-fluid <?php if ($action == "dashboard") { ?>dash_home<?php } ?>"><?php } ?>
				<?php if ($action != "dashboard" && $action != "withdrawrequest"  && $action != "withdrawdeatil") { ?>
			<?php /*    <div class="navbar" style="margin:0;min-height: 0px;">
					<div class="navbar-inner">
					    <h6>
                                            <i class="icon-align-justify"></i>
						<?php
						echo $page_title;
						?>
					    </h6>
					</div>
				    </div> */?>
				<?php } else { /* ?>
				  <div class="title_bar">
				  <h1><?php
				  //echo $page_title;
				  if (COMPANY_CID == 1 || SUBDOMAIN == 'demo') {
				  //echo ' (Trash and Block option not work for demo users )';
				  } */
				    ?>

				    <?php } ?>    					
    			    <div class="container_content">
				    <?php
				//endif;
                                    }
				$sucessful_message = Message::display();
				if ($sucessful_message) {
				    ?>    	
    				<div id="messagedisplay">
                                    <div class="msg_inn">
				    <?php echo $sucessful_message; ?>
    				    </div>
    				</div>
			<?php } ?>    				   
<?php echo $content; ?>
			    </div>
			
    <?php if ($action != "dashboard" && $action != "withdrawrequest") { ?></div></div>  <?php } ?>
		</div>		  
	
		<!-- /content -->
	    </div>	
	<!-- /content container -->
        <?php  if ($action != 'editprofile_user') { ?>
<?php echo new View("admin/package_footer"); ?>  
        <?php } ?>
</div>      
<?php if (isset($_SESSION['userid'])) { ?>
        <script type="text/javascript">
    	$(document).ready(function () {
    	    if($('#messagedisplay')){
    		$('#messagedisplay').animate({opacity: 1.0}, 2000)
    		$('#messagedisplay').fadeOut('slow');
    	    }
			//for side bar menu
			$(".content-wrapper").css({'min-height':($(".wrapper .main-sidebar").height()+'px')});
			
			 $('.sidebar-toggle').click(function(){
				$("body").toggleClass("sidebar_hide");
				if ($("body").hasClass("sidebar_hide")) {
					$("body").addClass("sidebar-minize");
					$("body").addClass("sidebar-collapse");
					setMenuEnable(0);
				} else {
					$("body").removeClass("sidebar-minize");
					$("body").removeClass("sidebar-collapse");
					setMenuEnable(1);
				}
			});
			$("#content").addClass('content_right');
			
			 if ($(window).width() < 800) {
					$('.sidebar-toggle').click(function(){
				$("body").toggleClass("sidebar_hide");
				if ($("body").hasClass("sidebar_hide")) {
					$("body").addClass("sidebar-open");
					$("body").removeClass("sidebar-collapse");
					setMenuEnable(0);
				} else {
					$("body").removeClass("sidebar-open");
					$("body").removeClass("sidebar-collapse");
					setMenuEnable(1);
				}
				});
			}
			
			if ($(window).width() < 900) { 
				$("body").removeClass("sidebar-collapse");
				$('.sidebar-toggle').click(function(){
					$('body').toggleClass('sidebar-open');
					if (sessionStorage.sidebarin == 0) {
					$("body").addClass("sidebar-minize");
					$("body").removeClass("sidebar-collapse");
					} else {
					$("body").removeClass("sidebar-minize"); 
					$("body").removeClass("sidebar-collapse");
					}
				});
			}
        
    <?php if ($controller == "tdispatch") { ?>
		$.ajax({
		    url: SrcPath+"tdispatch/addbooking", 
		    cache: false, 
		    dataType: 'html',
		    success: function(response) 
		    {
			$('#map12').html(response);
			/*** This is used for load the map with out hidden *****/
			var center = map.getCenter();
			google.maps.event.trigger(map, "resize");
			map.setCenter(center);
			/*** This is used for load the map with out hidden *****/
			//$('.widget.addbooking_widget').slideToggle("slow");
			$('.widget.addbooking_widget').show();
			$('.widget.addbooking_widget').hide();
		    } 
		});
    <?php } ?>
        });
        
       function setMenuEnable(id)
	   {
			$.ajax({
			method:"post",
			url: SrcPath+"admin/setMenuEnable",
			data:"data="+id,
			cache: false, 
			dataType: 'html',
			success: function(response) { } 
			});
		}
        /*For Top right Menu Add booking popup
    		    $("#add_booking_popup").click(function(){
    			    var count = 100000,
    			    $btn = $('#add_booking_popup'); 
    			    $btn.val($btn.val());
        			
    			    $btn.val($btn.val().replace(count,count-1));
    			    count--;
    			    if(count%2) {
    				    //$('#map12').html('<img src="'+SrcPath+'/public/admin/images/loader.gif" />');

    				    $('.widget.addbooking_widget').slideToggle("slow");
    				    if( typeof(map) != 'undefined'){
    					    var center = map.getCenter();
    					    google.maps.event.trigger(map, "resize");
    					    map.setCenter(center);
    				    }
        				
    				    $('#append_result').html("");
    				    // For append script 
    				    jQuery(function($) {
    					    var scrpt = document.createElement("script");
    					    scrpt.type = "text/javascript";
    					    scrpt.src = "<?php echo URL_BASE; ?>public/common/js/tdispatch_addbooking.js";
    					    $("#append_result").append(scrpt);
    				    });

    			    }else{
    				    $('#append_result').html("");
    				    // For append script 
    				    jQuery(function($) {
    					    var scrpt = document.createElement("script");
    					    scrpt.type = "text/javascript";
    					    scrpt.src = "<?php echo URL_BASE; ?>public/common/js/tdispatch_addbooking.js";
    					    $("#append_result").append(scrpt);
    				    });
    				    $('.widget.addbooking_widget').slideToggle("slow");
    			    }
    		    });
        		
    		    //For Side Menu Add booking popup
    		    var count_li = 100000,
    		    $btn_li = $('#add_booking_popup_li'); 
    		    $btn_li.val($btn_li.val());
        		
    		    $btn_li.click(function(){
    			    $btn_li.val($btn_li.val().replace(count_li,count_li-1));
    			    count_li--;
    			    if(count_li%2) {
    				    //$('#map12').html('<img src="'+SrcPath+'/public/admin/images/loader.gif" />');
    				    $('.widget.addbooking_widget').slideToggle("slow");
    				    if( typeof(map) != 'undefined'){
    					    var center = map.getCenter();
    					    google.maps.event.trigger(map, "resize");
    					    map.setCenter(center);
    				    }
    			    }else{
    				    $('.widget.addbooking_widget').slideToggle("slow");
    			    }
    		    });
        		
    	    }); */
        	
        </script>


        <script type="text/javascript">

    	//===== Hide/show Menubar =====//
    	$('.fullview').click(function(){
        		
    	    /*** This is used for load the map with out hidden *****
    			    var center = map.getCenter();
    			    google.maps.event.trigger(map, "resize");
    			    map.setCenter(center);
    		    /*** This is used for load the map with out hidden *****/
    	    $("body").toggleClass("clean");
    	    $('#sidebar').toggleClass("show-sidebar mobile-sidebar");
    	    $('#content').toggleClass("full-content");
    	});
        	
    	$(window).resize(function(){
			if ($(window).width() < 900) {
			if (sessionStorage.sidebarin == 0) {
				$("body").addClass("sidebar-minize");
				$("body").removeClass("sidebar-collapse");
			} else {
				$("body").removeClass("sidebar-minize"); 
				$("body").removeClass("sidebar-collapse");
			}
			  
			}
		});

        </script>  
<?php }       
?>  
   
</body>
</html>
