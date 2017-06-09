<?php defined('SYSPATH') OR die('No direct access allowed.'); ?>
<div id="home">
   <?php if($home_array['banner_image_1'] != '') {
      if(file_exists(DOCROOT.PUBLIC_UPLOADS_LANDING_FOLDER.$home_array['banner_image_1'])) { ?>
   <img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?><?php echo $home_array['banner_image_1']; ?>" />
   <?php } else { ?>
   <img class="default_banner" alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/default_banner.png" />
   <?php } } else { ?>
   <img class="default_banner" alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/default_banner.png" />
   <?php } ?>
   <div class="banner_inner">
      <div class="banner_content">
         <?php echo $home_array['banner_content']; ?>
         <div class="app_outer">
            <ul>               
               <li><a target="_blank" href="<?php echo $home_array['passenger_app_ios_store_link']; ?>" title="<?php echo __('app_store'); ?>"><img alt="<?php echo __('app_store'); ?>" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/banner_iosstore.png" /></a></li>
               <li>  <a target="_blank" href="<?php echo $home_array['passenger_app_android_store_link']; ?>" title="<?php echo __('play_store'); ?>"><img alt="<?php echo __('play_store'); ?>" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/banner_androidstore.png" /></a></li>
            </ul>
         </div>
      </div>
   </div>
</div>
<?php /*<div id="about-us" style="<?php echo "background:#".$home_array['about_bg_color']; ?>"> */ ?>
<div id="about-us">
	<div class="about_left">
		<?php /* <img alt="Taxi" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/about_left.png" /> */ ?>
		<?php
		if($home_array['frontend_car'] != '') {
			if(file_exists(DOCROOT.PUBLIC_UPLOADS_LANDING_FOLDER.$home_array['frontend_car'])) { ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?><?php echo $home_array['frontend_car']; ?>" />
			<?php } else {  ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>theme_1/about_left.png" />
			<?php	}  
		}else{  ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>theme_1/about_left.png" />
		<?php } ?>
		
	</div>
   <div class="about_right">
      <?php echo $home_array['about_us_content']; ?>
   </div>
</div>
<?php /*<div id="join-us-driver" style="<?php echo "background:#".$home_array['app_bg_color']; ?>">*/ ?>
<div id="join-us-driver" <?php if(strtolower($home_array['app_bg_color_1']) != 'ffffff') { ?> style="<?php echo "background:#".$home_array['app_bg_color_1']; ?>" <?php } ?>>
    <div class="join_usright">		
	<?php
		if($home_array['frontend_mobile'] != '') {
			if(file_exists(DOCROOT.PUBLIC_UPLOADS_LANDING_FOLDER.$home_array['frontend_mobile'])) { ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?><?php echo $home_array['frontend_mobile']; ?>" />
			<?php } else {  ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/frontend_mobile.png" />
			<?php	}  
		}else{  ?>
			<img alt="play store" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/frontend_mobile.png" />
	<?php } ?>
   
   </div>
    <div class="join_usleft">
      <?php echo $home_array['app_content']; ?>
      <div class="join_social">
         <a target="_blank" href="<?php echo $home_array['app_ios_store_link']; ?>" title="<?php echo __('app_store'); ?>"><img alt="<?php echo __('app_store'); ?>" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/app_store.png" /></a>
         <a target="_blank" href="<?php echo $home_array['app_android_store_link']; ?>" title="<?php echo __('play_store'); ?>"><img alt="<?php echo __('play_store'); ?>" src="<?php echo URL_BASE.PUBLIC_UPLOADS_LANDING_FOLDER; ?>/theme_1/play_store.png" /></a>
      </div>
   </div>
   
</div>
<?php /*<div id="contact-us" style="<?php echo "background:#".$home_array['footer_bg_color']; ?>"> */ ?>
<div id="contact-us" <?php if(strtolower($home_array['footer_bg_color_1']) != 'ffffff') { ?> style="<?php echo "background:#".$home_array['footer_bg_color_1']; ?>" <?php } ?>>
   <div class="inner_contact">
      <div class="contact_head">
         <h4>KEEP IN TOUCH</h4>
      </div>
      <div class="contact_btm">
          <div class="contact_det" id="contact_det">
            <form name="company_form" class="FlowupLabels" method="post" onsubmit="return companyFormSubmit();">
			
			<div class="form-group fl_wrap">
                            <label class='fl_label' for='name'>Name</label>
                            <input class="form-control fl_input" name="name" type="text" value="" maxlength="50">
							<em id="name_error"></em>
                        </div>
						
						<div class="form-group fl_wrap">
                            <label class='fl_label' for='phone'>Mobile Number</label>
                            <input class="form-control fl_input" name="phone" type="text" value="">
							<em id="phone_error"></em>
                        </div>
						
						<div class="form-group fl_wrap">
                            <label class='fl_label' for='email'>Email</label>
                            <input class="form-control fl_input" name="email" type="text" value="" maxlength="85">
							<em id="email_error"></em>
                        </div>
						<div class="form-group form-group_type">
						<select name="type">
                        <option value=""><?php echo __('select_type'); ?></option>
                        <option value="1"><?php echo __('driver'); ?></option>
                        <option value="2"><?php echo __('passenger'); ?></option>
                     </select>
                     <em id="type_error"></em>
					 </div>
					 
					 <div class="form-group fl_wrap_text">
                            <label class='fl_label' for='email'>Drop your Message here...</label>
					  <textarea class="form-control fl_input" name="message" maxlength="250" minlength="10" ></textarea>
                     <em id="message_error"></em>
					 </div>
              <div class="form-group">
                     <input type="submit" id="form_submit_btn" value="Submit" name="company_form_submit"/>
                     <input type="submit" id="loading_btn" value="Please Wait..." disabled="disabled" style="display:none;"/>
                  </div>
             
            </form>
         </div>
         <div class="contact_btmleft">
            <?php echo $home_array['contact_us_content']; ?>
		</div>
         
      </div>
   </div>
</div>

<div id="fade"></div>
<script>
   function companyFormSubmit()
   {
   	var validate = 1;
   	var name = document.company_form.name.value.trim();
   	var email = document.company_form.email.value.trim();
   	var phone = document.company_form.phone.value.trim();
   	var type = document.company_form.type.value.trim();
   	var message = document.company_form.message.value.trim();
   	var l = email.indexOf("@");
   	var h = email.lastIndexOf(".");
   	var p = "!#$%^&*()+=[]\\';,/{}|\":<>?";
   	for (var v = 0; v < document.company_form.email.value.length; v++) {
   		if (p.indexOf(document.company_form.email.value.charAt(v)) != -1) {
   			$("#email_error").html("Remove special characters");
   			validate = 0;
   			return false;
   		}
   	}
   	
   	if (name == "") {
   		$("#name_error").html("Enter your name");
   		validate = 0;
   	} else {
   		$("#name_error").html("");
   	}
   	
   	if (email == "") {
   		$("#email_error").html("Enter your email ID");
   		validate = 0;
   	} else if (l < 1 || h < l + 2 || h + 2 >= email.length) {
   		$("#email_error").html("Invalid email");
   		validate = 0;
   	} else {
   		$("#email_error").html("");
   	}
   	
   	if (phone == "") {
   		$("#phone_error").html("Enter your mobile number");
   		validate = 0;
   	} else {
   		$("#phone_error").html("");
   	}
   
   	if (type == "") {
   		$("#type_error").html("Choose your type");
   		validate = 0;
   	} else {
   		$("#type_error").html("");
   	}
   	
   	if (message == "") {
   		$("#message_error").html("Enter your message");
   		validate = 0;
   	} else {
   		$("#message_error").html("");
   	}
   	
   	if(validate == 1) {
   		$('#form_submit_btn').hide();
   		$('#loading_btn').show();
   		document.company_form.submit();
   	} else {
   		return false;
   	}
   }
</script>
 <script type="text/javascript">
(function($) {
    $.fn.FlowupLabels = function( options ){
    
        var defaults = {
            // Useful if you pre-fill input fields or if localstorage/sessionstorage is used. 
            feature_onLoadInit:     true,
            // Class names used for focus and populated statuses
            class_focused:      'focused',
            class_populated:    'populated'
        },
        settings = $.extend({}, defaults, options);
  
  
        return this.each(function(){
            var $scope  = $(this);
    
            $scope.on('focus.flowupLabelsEvt', '.fl_input', function() {
                $(this).closest('.fl_wrap').addClass(settings.class_focused);
            })
            .on('blur.flowupLabelsEvt', '.fl_input', function() {
                var $this = $(this);
                
                if ($this.val().length) {
                    $this.closest('.fl_wrap')
                        .addClass(settings.class_populated)
                        .removeClass(settings.class_focused);
                } 
                else {
                    $this.closest('.fl_wrap')
                        .removeClass(settings.class_populated + ' ' + settings.class_focused);
                }
            });
        
    
            // On page load, make sure it looks good
            if (settings.feature_onLoadInit) {
                $scope.find('.fl_input').trigger('blur.flowupLabelsEvt');
            }
        });
    };
})( jQuery );

(function(){
    $('.FlowupLabels').FlowupLabels({
        feature_onInitLoad: true,
        class_focused: 'focused',
        class_populated: 'populated'
    });
})();

(function($) {
    $.fn.FlowupLabels = function( options ){
    
        var defaults = {
            // Useful if you pre-fill input fields or if localstorage/sessionstorage is used. 
            feature_onLoadInit:     true,
            // Class names used for focus and populated statuses
            class_focused:      'focused',
            class_populated:    'populated'
        },
        settings = $.extend({}, defaults, options);
  
  
        return this.each(function(){
            var $scope  = $(this);
    
            $scope.on('focus.flowupLabelsEvt', '.fl_input', function() {
                $(this).closest('.fl_wrap_text').addClass(settings.class_focused);
            })
            .on('blur.flowupLabelsEvt', '.fl_input', function() {
                var $this = $(this);
                
                if ($this.val().length) {
                    $this.closest('.fl_wrap_text')
                        .addClass(settings.class_populated)
                        .removeClass(settings.class_focused);
                } 
                else {
                    $this.closest('.fl_wrap_text')
                        .removeClass(settings.class_populated + ' ' + settings.class_focused);
                }
            });
        
    
            // On page load, make sure it looks good
            if (settings.feature_onLoadInit) {
                $scope.find('.fl_input').trigger('blur.flowupLabelsEvt');
            }
        });
    };
})( jQuery );

(function(){
    $('.FlowupLabels').FlowupLabels({
        feature_onInitLoad: true,
        class_focused: 'focused',
        class_populated: 'populated'
    });
})();

</script>

