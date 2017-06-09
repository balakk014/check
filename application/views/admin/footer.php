<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
	<?php if(isset($_SESSION['userid'] )) { $tdispatch="";
	if($action == 'addbooking' || $action == 'managebooking' || $action == 'recurrentbooking' || $action == 'frequent_location' || $action == 'frequent_journey' || $action == 'accounts' || $action == 'tdispatch_settings' || $action == 'edit_recurrent_booking' || $action == 'edit_frequentlocation' || $action == 'add_frequentlocation' || $action == 'add_frequentjourney' || $action == 'edit_frequentjourney' || $action == 'add_accounts' || $action == 'edit_accounts' || $action == 'add_groups' || $action == 'edit_groups' || $action == 'add_users' || $action == 'edit_users'){ $tdispatch = 'active'; } ?>
	<?php if((($_SESSION['user_type'] == 'C')||($_SESSION['user_type'] == 'M')) && $tdispatch=="active") { ?>
	<script type="text/javascript" src="<?php echo URL_BASE;?>public/common/js/jquery.age.js"></script>
	<script type="text/javascript">
	load_logcontent()
	//setInterval(function(){load_logcontent() }, 10000);					 
	function load_logcontent()
	{
				var dataS = '';
				var SrcPath = $('#baseurl').val();
				var response;
				$.ajax
				({ 			
					type: "POST",
					url: SrcPath+"tdispatch/get_log_content", 
					data: dataS, 
					cache: false, 
					dataType: 'html',
					success: function(response){ $('#log_content').html(response); }		 
				});	
	}

		$(document).ready(function(){
		var current_path = $(location).attr('hash');
		$('.age').age();
		//onloading page remain in min state
		$("#jsrp_related").animate( { height:"40px" });
		var max_image = "<?php echo IMGPATH;?>maximize.png";
		$("#close_btn").attr({src:max_image});
		$("#jsrp_related").toggle(
			function(){
				var imgSrc= $("#close_btn").attr("src");
				var findimg = imgSrc.split('/').pop();
				//For Replacing the Menu Images
				//==============================
				/*var toggle_image="<?php echo IMGPATH;?>minus.png";
				if(findimg == "minus.png")
				var toggle_image="<?php echo IMGPATH;?>maximize.png";
				$("#close_btn").attr({src:toggle_image});
				$("#jsrp_related").animate( { height:"40px" }, { queue:false, duration:500 });*/
				var toggle_image="<?php echo IMGPATH;?>minus.png";
				if(findimg == "minus.png")
				var toggle_image="<?php echo IMGPATH;?>maximize.png";
				$("#close_btn").attr({src:toggle_image});
		   		$("#jsrp_related").animate( { height:"300px" }, { queue:false, duration:500 });
		},
		function(){
				var imgSrc= $("#close_btn").attr("src");
				var findimg = imgSrc.split('/').pop();
				//For Replacing the Menu Images
				//==============================
				/*var toggle_image="<?php echo IMGPATH;?>minus.png";
				if(findimg == "minus.png")
				var toggle_image="<?php echo IMGPATH;?>maximize.png";
				$("#close_btn").attr({src:toggle_image});
		   		$("#jsrp_related").animate( { height:"300px" }, { queue:false, duration:500 });*/
		   		var toggle_image="<?php echo IMGPATH;?>minus.png";
				if(findimg == "minus.png")
				var toggle_image="<?php echo IMGPATH;?>maximize.png";
				$("#close_btn").attr({src:toggle_image});
				$("#jsrp_related").animate( { height:"40px" }, { queue:false, duration:500 });
		});
		});
	</script>
	<?php } } ?>
	<script>
	function to_timestamp(date)
	{
		//return (new Date(date.split(".").join("-")).getTime())/1000;
	}
	</script>
	<?php 
        $current_url= Request::$current->url();        
        if(isset($_SESSION['user_type']) && $_SESSION['user_type']=='A' && PACKAGE_TYPE==0 && ($current_url!='/manager/forgot_password' && $current_url!='/manager/login' && $current_url!='/company/login' && $current_url!='/admin/forgot_password' && $current_url!='/admin/login' && $current_url!='/package/account_plan' && $current_url!='/package/editprofile_user/1' && $current_url!='/package/billing_info' && $current_url!='/package/billing_confirm')){?>
        <div class="sticky_message">
            <div class="sticky_message_content"><?php 
            if(PACKAGE_TYPE==0){
                $str_message=__('free_trial');
            }/*else if(PACKAGE_TYPE==1){
                $str_message=__('basic').' plan';
            }else if(PACKAGE_TYPE==2){
                $str_message=__('plantinum').' plan';
            }else if(PACKAGE_TYPE==3){
                $str_message=__('enterprise').' plan';
            }*/
            if (!empty(EXPIRY_TIME) && EXPIRY_TIME < strtotime(CURRENT_TIMEZONE_DATE))
            {
                $expiry_day_message= str_replace('##plan_name##', $str_message,__('expired_message'));
              
            }else{ 
                $str_trail_expiry_days= str_replace('+','',TRIAL_EXPIRY_DAYS);
                if(TRIAL_EXPIRY_DAYS==0)
                {
                    $expiry_day_message=str_replace('##plan_name##', $str_message,__('expired_message_today'));
                }else{
                    $expiry_days=str_replace('##expiry_days##',$str_trail_expiry_days,__('trial_expiry_days'));
                    $expiry_day_message=str_replace('##plan_name##',$str_message,$expiry_days);                    
                }
            }
            echo $expiry_day_message;?></div>
            <div class="sticky_message_action"><a class="common_butt" href="<?php echo URL_BASE.'package/account_plan';?>" title="<?php echo __('select_aplan'); ?>"><?php echo __('select_aplan'); ?></a></div>
        </div>
        
	<?php } ?>
<!-- Footer -->
<?php if(isset($_SESSION['userid'] )) { ?>
	<div id="footer">
		<div class="copyrights">
			<?php if(COMPANY_CID==0){ echo $footer_contents['site_copyrights']; }else{ echo COMPANY_COPYRIGHT; } ?>
		</div>
	</div>
<div id="fade"></div>
	<script>
    $(document).ready(function(){
     $.sidebarMenu($('.sidebar-menu'));
     });
     
     
$.sidebarMenu = function(menu) {
  var animationSpeed = 300;
  
  $(menu).on('click', 'li a', function(e) {
    var $this = $(this);
    var checkElement = $this.next();

    if (checkElement.is('.treeview-menu') && checkElement.is(':visible')) {
      checkElement.slideUp(animationSpeed, function() {
        checkElement.removeClass('menu-open');
      });
      checkElement.parent("li").removeClass("active");
    }

    //If the menu is not visible
    else if ((checkElement.is('.treeview-menu')) && (!checkElement.is(':visible'))) {
		//Get the parent menu
		var parent = $this.parents('ul').first();
		//Close all open menus within the parent
		var ul = parent.find('ul:visible').slideUp(animationSpeed);
		//Remove the menu-open class from the parent
		ul.removeClass('menu-open');
		//Get the parent li
		var parent_li = $this.parent("li");
		//Open the target menu and add the menu-open class
		checkElement.slideDown(animationSpeed, function() {
			//Add the class active to the parent li
			checkElement.addClass('menu-open');
			parent.find('li.active').removeClass('active');
			parent_li.addClass('active');
		});
    }
	
    //if this isn't a link, prevent the page from being redirected
    if (checkElement.is('.treeview-menu')) {
      e.preventDefault();
    }
  });
}
</script>
<?php } ?>


<!-- /footer -->


