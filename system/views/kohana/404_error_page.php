<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="title" content="About Us | Taxi Mobility" />
<meta name="keywords" content="taxi app, about us." />
<meta name="description" content="Taxi Mobility dispatch system is a sincere effort to reduce public inconvenience in booking taxi and helping taxi companies with an intelligent taxi dispatch software." />

<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

      
    
	<link rel="shortcut icon" href="<?php echo URL_BASE; ?>public/uploads/favicon/5319a6200b16434b31b13f04.png" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/slidebars.css" /> 
		
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/style.css" />
	
	
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/jquery.selectbox.css"  />   
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/bootstrap.min.css"  /> 
	       
    <link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/media_style.css" />    
    <script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery1.11.0.js"></script>
    
        <!-- menu script start -->       
        <script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/slidebars.js"></script>
    <!-- menu script end -->
    
    <!--<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery.min.js"></script>
    <script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery-1.9.1.min.js"></script> -->
	   	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/bootstrap-timepicker.min.css"  /> 
	<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/bootstrap-timepicker.min.js"></script>	
    <script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/viewportchecker.js"></script>
		<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery.lightbox_me.js"></script>
	<title>About Us | Taxi Mobility</title>
<!--[if lte IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo URL_BASE; ?>public/common/css/ie8.css" />
<![endif]-->


<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-43899653-3', 'auto');
  ga('send', 'pageview');

</script>


<script>
var $ = jQuery.noConflict();
jQuery.browser={};(function(){jQuery.browser.msie=false;
jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();
</script>
</head>
<body    class="body_non_bg"  > 
<input type="hidden" name="baseurl" id="baseurl" value="<?php echo URL_BASE; ?>">
<noscript>Your browser does not support JavaScript!</noscript>
		
	<div id="advance_form_search1" style="display:none">

<form action="<?php echo URL_BASE; ?>advance-search.html" method="post" id="form_advance_id">
  <input type="text" name="search_country" value="" id="advance_search_country" />
  <input type="text" name="search_city" value="" id="advance_search_city" />
</form>

</div>
<script type="text/javascript">
function citySelect(URL,country_id,city_id,city_name)
{
/*
$('#advance_form_search1').html('<form action="<?php echo URL_BASE; ?>advance-search.html" method="post" id="form_advance_id">' +
  '<input type="text" name="search_country" value="' + country_id + '" />' +
  '<input type="text" name="search_city" value="' + city_name + '" />' +
  '</form>');
  
var form = $('<form action="<?php echo URL_BASE; ?>advance-search.html" method="post">' +
  '<input type="text" name="search_country" value="' + country_id + '" />' +
  '<input type="text" name="search_city" value="' + city_name + '" />' +
  '</form>');
*/
$('#advance_search_country').val(country_id);
$('#advance_search_city').val(city_name);
$('#form_advance_id').submit();


}
</script>

  <script>
$(document).ready(function(){ 
$( ".header_select_city" ).click(function() {
$( "#city_part_outer_div" ).slideToggle( "slow" );
$(this).toggleClass("active");           
$(".header_select_city").removeClass("active"); 
});
 });
</script>

<div class="city_part_outer" id="city_part_outer_div">
    <div class="city_liting_inner">

	
    <div class="city_listing" style=border:none;padding-left:0;>
        <h2>China </h2>
        <ul>
		<li><a title="No city available">No city available</a></li>
		</ul>
	</div>
	
    <div class="city_listing" >
        <h2>German </h2>
        <ul>
	            <li><a href="javascript:;"   onclick="citySelect('<?php echo URL_BASE; ?>users/setsessioncity','2','2','Postdam')"  title="Postdam">Postdam</a></li>
	     	</ul>
	</div>
	
    <div class="city_listing" >
        <h2>India </h2>
        <ul>
	            <li><a href="javascript:;"   onclick="citySelect('<?php echo URL_BASE; ?>users/setsessioncity','1','1','Coimbatore')"  title="Coimbatore">Coimbatore</a></li>
	     	            <li><a href="javascript:;"   onclick="citySelect('<?php echo URL_BASE; ?>users/setsessioncity','1','3','Erode')"  title="Erode">Erode</a></li>
	     	</ul>
	</div>
	
    <div class="city_listing" >
        <h2>Japan </h2>
        <ul>
	            <li><a href="javascript:;"   onclick="citySelect('<?php echo URL_BASE; ?>users/setsessioncity','4','4','Nagasagi')"  title="Nagasagi">Nagasagi</a></li>
	     	</ul>
	</div>
    </div>
</div>


<div class="header_outer_cont">
            <div class="header_outer_inner clearfix">
                <div class="logo">                
                    <h1><span id="current_time" style="display:none;"></span>                  	
                                       	
                        <a href="<?php echo URL_BASE; ?>" title="Taximobility" ><img src="<?php echo URL_BASE; ?>public/uploads/site_logo/logo.png" ></a>
                                            </h1>
                </div>
	                <div class="root_header_left_menu">
                    <!--<span class="header_menu">Menu</span>-->
                    <div class="header_menu_left_inner">
                        <div class="header_menus_common">
			<!-- Taximobility -->
			<a href="#" title="Menu" class="toggleMenu" style="display:none;">&nbsp;</a>                            
			<!-- Taximobility -->
                        <ul  class="hnav">
                            <li> <a  href="<?php echo URL_BASE; ?>"  title="Home">Home</a></li>

			<!-- Taximobility Design-->
			    <li ><a href="<?php echo URL_BASE; ?>features.html" title="Features">Features</a></li> 
			    			    			    <li ><a href="<?php echo URL_BASE; ?>pricing.html" title="pricing">pricing</a></li> 			    
			     <li  class="active" ><a href="<?php echo URL_BASE; ?>about-us.html" title="about us">about us</a></li> 
			<!-- Taximobility Design-->


		
                                     
                            <li><a   href="<?php echo URL_BASE; ?>contact-us.html" title="Contact us">Contact us</a></li>	
							<li ><a href="http://demo.taximobility.com/" title="demo">Demo</a></li>
                            <li class="header_demo"><a href="javascript:;" onclick="gettrialpopup()" title="Try it Free">Try it Free</a></li>                                                  
                        </ul>
                        
                         
                        
                    </div>
                    </div>
                </div>
                             
                   <div class="right_side_login">                   
                    
					                                    </div>                   
                                               
                               <!-- menus-start-->
                <div class="common_menus_midl">
                    
                </div>
                <!-- menus-end-->
            </div>
            
        </div>
        
            

  
 <script type="text/javascript">
   $(document).ready(function(){
           $('.tax_left_ican').click(function(){
               $('div.drop_custome_left').css('display','none');
               $(this).next('div.tax_left_ican').css('display','block');            
               $('a').removeClass('select');
               $(this).addClass('select');  
	   });            
    });
	
		</script>	



<!-- City Start Here -->
	<div id="citylist_popup">
                <h2>Select your city</h2>                

				<div class="login_total">                                               
                        <div class="selectcity_form">
                           <form name="search_citylist"  id="search_citylist" method="post" action="<?php echo URL_BASE; ?>advance-search.html">
                            <ul>
                                <li>
					<span>Country</span>
					<div class="controls">
							<div class="input-prepend">
							  <span class="add-on"><i class="country_icon"></i></span>
							<select name="search_country" id="search_country" class="required" title="Select the Country">
							<option value="">--Select--</option>
														<option value="3"  >China</option>
														<option value="2"  >German</option>
														<option value="1"  >India</option>
														<option value="4"  >Japan</option>
														</select>


							</div>
					</div>  
					<span id="country_error" class="signup_error"  style="margin-right:95px;"></span>
				</li>

                                <li>
					<span>City</span>
					<div class="controls">
							<div class="input-prepend">
							  <span class="add-on"><i class="city_icon"></i></span>
							<div id="country_citylist">
								<select name="search_city" id="search_city" class="required" title="Select the City">
								<option value="">--Select--</option>
																<option value="Coimbatore"  >Coimbatore</option>
																<option value="Erode"  >Erode</option>
																<option value="Nagasagi"  >Nagasagi</option>
																<option value="Postdam"  >Postdam</option>
																</select>
							</div>	


							</div>
					</div>      
					<span id="city_error" class="signup_error" style="margin-right:120px;"></span>                          
				</li>
								
                                <li><span></span>
                                        <input type="button" id="submit_citysearch" value="Submit" class="btn btn-inverse" title="Submit"  />                                        
                                  </li>
                               
                                     </ul>                      
						  </form> 
						 </div>
                  </div>
                  <a id="close_x" class="close sprited" href="#"></a>
</div>


	<div id="current_shiftstatus">			
	
				<span class='btn btn-mini btn-danger' style="display:none;" onclick="drivershift('1')">Shift IN</span>
		<input name='driver_current_status' id='driver_current_status' type='hidden' value='OUT'>
			</div>


<!-- City End Here -->       
<script>

$('#submit_citysearch').click(function(){

    var country = document.search_citylist.search_country.value;    
    var city = document.search_citylist.search_city.value;

    if(country =='' || city == '')
    {
       valid = true;

        if(country == '')
        {
            $('#country_error').html("Please Select the Country");
            return false;
        }

        if(city == '')
        {
            $('#country_error').html('');
            $('#city_error').html("Please Select the City");
            return false;
        }
    }
    else{

             $('#country_error').html('');
             $('#city_error').html('');

	document.getElementById("search_citylist").submit();
   }
});


   function showcitylist()
   {
			$("#citylist_popup").lightbox_me({centered: true, onLoad: function() {
			}});					
			event.preventDefault();

   }	

    $("#search_country").change(function() {

      		var countryid= $("#search_country").val();

		  $.ajax({
			url:"<?php echo URL_BASE; ?>users/country_citylist",
			type:"get",
			data:"country_id="+countryid,
			success:function(data){

			$('#country_citylist').html();
			$('#country_citylist').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    });
</script>
<script>

var ww = document.body.clientWidth;

$(document).ready(function() {
	$(".hnav li a").each(function() {
		if ($(this).next().length > 0) {
			$(this).addClass("parent");
		}
	});
	
	$(".toggleMenu").click(function(e) {
		e.preventDefault();
		$(this).toggleClass("active");
		$(".hnav").slideToggle("medium");
	});
	adjustMenu();
})

$(window).bind('resize orientationchange', function() {
	ww = document.body.clientWidth;
	adjustMenu();
});

var adjustMenu = function() {
	if (ww < 780) {
		$(".toggleMenu").css("display", "inline-block");
		if (!$(".toggleMenu").hasClass("active")) {
			$(".hnav").hide();
		} else {
			$(".hnav").show();                        
		}
		$(".hnav li").unbind('mouseenter mouseleave');
		$(".hnav li a.parent").unbind('click').bind('click', function(e) {
			// must be attached to anchor element to prevent bubbling
			e.preventDefault();
			$(this).parent("li").toggleClass("hover");                                                
		});
	} 
	else if (ww >= 780) {
		$(".toggleMenu").css("display", "none");
		$(".hnav").show();
		$(".hnav li").removeClass("hover");
		$(".hnav li a").unbind('click');
		$(".hnav li").unbind('mouseenter mouseleave').bind('mouseenter mouseleave', function() {
		 	// must be attached to li so that mouseleave is not triggered when hover over submenu
		 	$(this).toggleClass('hover');
		});
	}
}

</script>

<!--error page start-->

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
	
<!--error page end-->
    			       	
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/validation/jquery.validate.js"></script>

<!-- footer start-->

<div class="footer_outer">
    <div class="footer_outer_inner">
        <div class="footer clearfix">
            <div class="clearfix">
            			<div class="footer_products footer_products1">
                        <h6>Learn More</h6>
                        <ul>
						 							<li><a href="<?php echo URL_BASE; ?>about-us.html" title="About us">About us</a></li>
							<li><a href="<?php echo URL_BASE; ?>termsconditions.html" rel="nofollow" title="Terms & Conditions">Terms & Conditions</a></li> 
                            <li><a href="<?php echo URL_BASE; ?>privacypolicy.html" rel="nofollow" title="Privacy & Policy">Privacy & Policy</a></li>
                            <li><a href="<?php echo URL_BASE; ?>tutorial.html" title="Tutorial">Tutorial</a></li>  
                            
				                        </ul>
                    </div>
                                   <div class="footer_products">
                        <h6>Company</h6>
                        <ul>
							
                           <li><a href="<?php echo URL_BASE; ?>features.html" title="Features">Features</a></li>  
                           <li><a href="<?php echo URL_BASE; ?>faq.html" title="FAQ">FAQ</a></li>  
                                                    
                        </ul>
                    </div>
                                          
                    <div class="footer_products footer_rgt">
                            <h6>Social</h6>
                            <ul>
								                                <li><a class="facebook2" href="https://www.facebook.com/ndotin" title="Facebook" target="_blank"></a></li>
                                <li><a class="twitter" href="https://twitter.com/ndottechnology" title="Twitter" target="_blank"></a></li>                                
                                <li><a class="google" href="https://plus.google.com/116949491348741826853" title="Google+" target="_blank"></a></li> 
                                <li><a class="linked_in" href="https://www.linkedin.com/company/ndot-technologies" title="LinkedIn" target="_blank"></a></li>
                                                            </ul>
                    </div>       
        </div>
                        <p class="copy_r">©2014 Taximobility. All rights reserved.</p>
        </div>
    </div>

</div>
<!-- footer end-->
<!-- Login & Register Start Here -->

<div id="sign_up">
    <div class="sign_up_top">

    </div>
    <div class="sign_up_mid">
        <div class="login_title_top">
            <div class="login_ttle">
                <span class="log_image"></span>
                <b>Log In</b>
            </div>
            <div class="sign_up_ttle">
                <span class="sign_image"></span>
                <b>Sign Up</b>
            </div>

        </div>

        <div class="login_total">
            <div class="login_lft">
                <h2></h2>		

                <div class="login_form" id="login_form">
                    <form name="login" method="post" action="<?php echo URL_BASE; ?>passengers/login">
                        <ul>

                            <li>
                                                        
                                <div class="controls">
                                    <div class="input-prepend">
                                        
                                        <input type="text" name="lphone" id="lphone" placeholder="Phone" value="" ><span class="sing-phone"></span>
                                    </div>
                                    <input type="hidden" name="isbooking" id="isbooking" value="">
                                    <input type="hidden" name="d_id" id="d_id" value="">
                                </div>                    
                                <span style="color:red;float:right;width:235px;" id="lphone_error" class="phone_custom_error"></span>
                            </li>

                            <li>
                                <span></span>
                                <div class="controls">
                                    <div class="input-prepend">
                                       
                                        <input type="password" name="password" id="pwd" placeholder="Password" value="" /> <span class="sing_pasword"></span>
                                    </div>
                                </div>                             
                                <span style="color:red;float:right;width:235px;" id="pwd_error" class="login_custom_error"></span>
                            </li>


                            <li>
                                <div class="remenber">
                                <input type="checkbox" name="remember" id="remember" />
                                <label>Remember me</label>
                                </div>
                            </li>
                            <li>
                             
                                <input type="button" value="Login" class="btn btn-inverse" title="Login" onclick="return validatelogin();" />

                           
                            <span id="p_error"></span>
                            <span class="login_link"><a href="<?php echo URL_BASE; ?>passengers/forgotpassword" title="Forgot Password?">Forgot Password?</a></span>                                                
                        </ul>                            
                </div>                        
                </form>
            </div>

            <div class="login_rgt">
                <h2></h2>

                <div class="login_form" >
                    <form name="signup" method="post" action="<?php echo URL_BASE; ?>passengers/signup">
                        <ul>
                           <li>                              
                                <div class="controls">
									<select style="width:310px;margin-left:23px;"  name="salutation" id="salutation" >
										<option value="">Select Salutation</option>	
										<option value="Mr">Mr</option>
										<option value="Mrs">Mrs</option>
									</select>                                        
                                        <span class="sing_name"></span>
                                    </div>
                                <span style="color:red;margin-right:32px;" class="signup_error" id="salutation_error"></span>
                            </li>
                            <li>
                              
                                <div class="controls">
                                    <div class="input-prepend">
                                        
                                        <input type="text" name="name" placeholder="First Name" id="name" value="" />
                                        <span class="sing_name"></span>
                                    </div>
                                </div> 							     
                                <span style="color:red;" class="signup_error" id="name_error"></span>
                            </li>

                            <li>
                              
                                <div class="controls">
                                    <div class="input-prepend">
                                        
                                        <input type="text" name="lname" placeholder="Last Name" id="lname" value="" />
                                        <span class="sing_name"></span>
                                    </div>
                                </div> 							     
                                <span style="color:red;" class="signup_error" id="lname_error"></span>
                            </li>

                            <li>
                                                           
                                <div class="controls">
                                    <div class="input-prepend">
                                       
                                        <input type="text"  name="email" placeholder="Email" id="email" value="" />
                                         <span class="sing-mail"></span>
                                    </div>
                                </div>	    
                                <span style="color:red;" class="signup_error" id="email_error"></span>
                            </li>

                            <li>
                               
                                <div class="controls">
                                    <div class="input-prepend">
                                       
                                        <input type="text"  name="phone" id="phone" value="" placeholder="Phone Number" />
                                         <span class="sing-phone"></span>
                                    </div>
                                </div>
                                <span style="color:red;" class="signup_error" id="phone_error"></span>
                            </li>

                            <li>
                              
                                <textarea name="address" id="address" placeholder="Address" /></textarea>
                                <span style="color:red;" class="signup_error" id="address_error"></span>
                            </li>

                            <li>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <input type="text"  name="creditcard_no" id="creditcard_no" value="" placeholder="Credit Card Number" maxlength="16" />
                                         <span class="sing_pasword"></span>
                                    </div>
				    	
                                </div>
                                <span style="color:red;" class="signup_error" id="creditcard_no_error"></span>
                            </li>

                            <li>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <input type="text"  name="creditcard_cvv" id="creditcard_cvv" value="" placeholder="CVV" maxlength="4" />
                                         <span class="sing_pasword"></span>
                                    </div>
                                </div>
                                <span style="color:red;" class="signup_error" id="creditcard_cvv_error"></span>
                            </li>


                                <li>
				<div class="controls">
				
			<select style="width:120px;margin-left:24px"  name="expdatemonth" id="expDateMonth" class="credit_card fl">
					<option value="">Month</option>	

        								<option value="01" >01</option>
									<option value="02" >02</option>
									<option value="03" >03</option>
									<option value="04" >04</option>
									<option value="05" >05</option>
									<option value="06" >06</option>
									<option value="07" >07</option>
									<option value="08" >08</option>
									<option value="09" >09</option>
									<option value="10" >10</option>
									<option value="11" >11</option>
									<option value="12" >12</option>
							</select>
			<select style="width:120px;margin-left:20px;" name="expdateyear" id="expDateYear" class="credit_card fl ml10">
					<option value="">Year</option>	
									<option value="2014">2014</option>
									<option value="2015">2015</option>
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
									<option value="2020">2020</option>
									<option value="2021">2021</option>
									<option value="2022">2022</option>
									<option value="2023">2023</option>
									<option value="2024">2024</option>
					
			</select>

					</div>  
				<span style="color:red;" id="expirydate_error" class="signup_error"></span>                                                        
				</li>


                            <li>
                                <input type="button" name="signup" class="btn btn-inverse" id="signup" value="Sign Up" title="Sign Up" onclick="return validate_signup();" />
                            </li>
								<p id="customer_error" style="display:none"><img alt="ajax-loader" src="<?php echo URL_BASE; ?>/public/common/css/img/ajax-loaders/ajax-loader-1.gif" /> Please wait we are verifying your details</p>
                            </li></ul>
                    </form>
                </div>

            </div>
        </div>
        <a id="close_x" class="close sprited" href="javascript:;"  onclick="hide_req2()"title="Close"></a>
    </div>
    <div class="sign_up_bot"></div>
</div>

<!-- Tell to friend form start here-->
<div id="tell_to_friend">
    <div class="login_pop_top"></div>
    <div class="login_pop_mid">
    
              
 <div class="login_title_top">
            <div class="login_ttle">
                <span class="log_image"></span>
                <b>Tell To Friend</b>
            </div>
           

        </div>

    <div class="login_total">      
        <h2></h2>
        <div class="login_form">
            <form name="tell_to_friend" method="post" action="<?php echo URL_BASE; ?>passengers/tell_to_friend">
                <ul>
                    <li>
                        
                        <div class="controls">
                            <div class="input-prepend">                                
                                <input type="text" name="dphone" placeholder="Email" id="temail"><span class="sing-mail"></span>
                                <span style="clear:both;font: normal 12px arial;width:300px;color:#666;margin-bottom:4px;">Send more than one invitation by comma separated</span>
                            </div>
                        </div>                                
                        <span id="temail_error" class="signup_error" style="float:left;"></span>
                    </li>
                    <li>
                                <textarea name="friend_message" id="friend_message" placeholder="Message" readonly />Share your code with a friend and they get 5% off their first ride. When they ride, you get 5% off yours! Code:                                </textarea>
                                <span style="color:red;" class="signup_error" id="friend_message_error"></span>
                            </li>
                    <li>
						<div id="sub_button">
                        <input type="button" style="margin:0;" value="Submit" class="btn btn-inverse" title="Submit" onclick="return validate_tell_to_friend();" />
                        </div>   
                        <span id="ajax_load"></span>
                    </li>
                </ul>                      
            </form> 
        </div>
    </div>
    <a id="close_x" class="close sprited" href="javascript:;" onclick="hide_req_op('tell_to_friend')" ></a>
    </div>
    <div class="login_pop_bot"></div>
</div>
<!-- Tell to friend end here -->
<!-- Login & Register End Here -->
<div id="driver_sign_in">
    <div class="login_pop_top"></div>
    <div class="login_pop_mid">
    
              
 <div class="login_title_top">
            <div class="login_ttle">
                <span class="log_image"></span>
                <b>Log In</b>
            </div>
           

        </div>

    <div class="login_total">      
        <h2></h2>
        <div class="login_form">
            <form name="driverlogin" method="post" onsubmit = "return validatedriverlogin()" action="">
                <ul>
                    <li>
                        
                        <div class="controls">
                            <div class="input-prepend">
                                
                                <input type="text" name="dphone" placeholder="Phone" id="dphone" value=""><span class="sing-phone"></span>
                            </div>
                        </div>                                
                        <span style="color:red;float:right;width:235px;text-transform:none!important;" id="dphone_error" class="signup_error"></span>
                    </li>

                    <li>
                        
                        <div class="controls">
                            <div class="input-prepend">
                               
                                <input type="password" name="password" id="password" placeholder="Password" value="" /> <span class="sing_pasword"></span>
                            </div>
                        </div>								
                        <span style="color:red;width:235px;text-transform:none!important;" id="dpwd_error" class="signup_error"></span>
                    </li>

                    <li>
                        <div class="remenber" style="margin:0;">
                                 <input type="checkbox" name="dremember" id="dremember" />
                                <label>Remember me</label>
                                </div>                       
                     </li>
                    <li>
                        
                        <input type="button" style="margin:0;" value="Login" class="btn btn-inverse" title="Login" onclick="return validatedriverlogin();" />                                        

                           
                            <span id="d_error"></span>
                           
                            
                            
                       
                 
                   
                    <span class="login_link"><a href="<?php echo URL_BASE; ?>driver/forgotpassword" title="Forgot Password?">Forgot Password?</a></span>
                    </li>

                </ul>                      
            </form> 
        </div>
    </div>
    <a id="close_x" class="close sprited" href="javascript:;"></a>
    </div>
    <div class="login_pop_bot"></div>
</div>
<!-- Customer Login Form -->
<div id="customer_sign_in">
    <div class="login_pop_top"></div>
    <div class="login_pop_mid">
    
              
 <div class="login_title_top">
            <div class="login_ttle">
                <span class="log_image"></span>
                <b>Log In</b>
            </div>
           

        </div>

    <div class="login_total">      
        <h2></h2>
        <div class="login_form">
                    <form name="customerlogin" id="customerlogin" method="post" onsubmit="return customer_validatelogin();" >
                        <ul>

                            <li>
                                                        
                                <div class="controls">
                                    <div class="input-prepend">
                                        
                                        <input type="text" name="lphone" id="customer_lphone" placeholder="Phone" value="" ><span class="sing-phone"></span>
                                    </div>
                                    <input type="hidden" name="isbooking" id="customer_isbooking" value="">
                                    <input type="hidden" name="d_id" id="customer_d_id" value="">
                                </div>                    
                                <span style="color:red;float:left;width:235px;" id="customer_lphone_error" class="login_custom_error"></span>
                            </li>

                            <li>
                                <span></span>
                                <div class="controls">
                                    <div class="input-prepend">
                                       
                                        <input type="password" name="password" id="customer_pwd" placeholder="Password" value="" /> <span class="sing_pasword"></span>
                                    </div>
                                </div>                             
                                <span style="color:red;float:left;width:235px;" id="customer_pwd_error" class="login_custom_error"></span>
				<span style="color:red;float:left;width:235px;" id="customer_p_error"></span>
                            </li>


                            <li>
                                <div class="remenber">
                                <input type="checkbox" name="remember" id="customer_remember" />
                                <label>Remember me</label>
                                </div>
                            </li>
                            <li>
                             
                                <input id="btn_login" type="button" value="Login" class="btn btn-inverse" title="Login" onclick="return customer_validatelogin();" />

                           
                            
                            <span class="login_link"><a href="<?php echo URL_BASE; ?>passengers/forgotpassword" title="Forgot Password?">Forgot Password?</a></span>                                                
                        </ul>                            
                    
                </form>
        </div>
    </div>
    <a id="close_x" class="close sprited" href="javascript:;"></a>
    </div>
    <div class="login_pop_bot"></div>
</div>
<!-- Customer Login Form End -->



<!-- Phone Number Popup Form -->
<div id="phone_number" style="display:none;">
    <div class="login_phone_pop_top"></div>
    <div class="login_pop_mid">
    
              
 <div class="login_title_top">
            <!--<div class="login_ttle">
                <span class="log_image"></span>
                <b></b>
            </div>-->
           

        </div>

    <div class="login_total">      
        <h2></h2>
        <div class="login_form">
                    <form name="phone_no" id="phone_no" method="post" action="<?php echo URL_BASE; ?>passengers/phonenumber" onsubmit="return phone_validatelogin();">
                        <ul>

			<input type='hidden' name='lphone' id='popup_lphone' value="">				
                                                      
                            <li>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <input type="text"  name="popup_creditcard_no" id="popup_creditcard_number" value="" placeholder="Credit Card Number" maxlength="16" />
                                         <span class="sing-phone"></span>
                                    </div>
				    	
                                </div>
                                <span style="color:red;" class="signup_error" id="popup_creditcard_number_error"></span>
                            </li>

							 <li>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <input style="width:255px;" type="text"  name="popup_creditcard_cvv" id="popup_creditcard_cvv" value="" placeholder="CVV" maxlength="4" />
                                         <span class="sing-phone"></span>
                                    </div>
				    	
                                </div>
                                <span style="color:red;" class="signup_error" id="popup_customer_creditcard_cvv_error"></span>
                            </li>
                            
                            <li>
					<div class="controls">


				
			<select style="width:120px;"  name="expdatemonth" id="expdatemonth" class="credit_card fl">
					<option value="">Month</option>	
        								<option value="01" >01</option>
									<option value="02" >02</option>
									<option value="03" >03</option>
									<option value="04" >04</option>
									<option value="05" >05</option>
									<option value="06" >06</option>
									<option value="07" >07</option>
									<option value="08" >08</option>
									<option value="09" >09</option>
									<option value="10" >10</option>
									<option value="11" >11</option>
									<option value="12" >12</option>
							</select>
			<select style="width:120px;margin-left:10px;" name="expdateyear" id="expdateyear" class="credit_card fl ml10">
					<option value="">Year</option>	
									<option value="2014">2014</option>
									<option value="2015">2015</option>
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
									<option value="2020">2020</option>
									<option value="2021">2021</option>
									<option value="2022">2022</option>
									<option value="2023">2023</option>
									<option value="2024">2024</option>
									<option value="2025">2025</option>
									<option value="2026">2026</option>
									<option value="2027">2027</option>
									<option value="2028">2028</option>
									<option value="2029">2029</option>
									<option value="2030">2030</option>
									<option value="2031">2031</option>
									<option value="2032">2032</option>
									<option value="2033">2033</option>
									<option value="2034">2034</option>
					
			</select>

					</div>  
				<span style="color:red;" id="popup_customer_expirydate_error" class="signup_error"></span>                                                        
				</li>

                            <li>
                             
                                <input type="button" value="Submit" class="btn btn-inverse" title="Submit" onclick="return phone_validatelogin();" />
                             </ul>                            
							<p id="customer_error_phone" style="display:none"><img alt="ajax-loader" src="<?php echo URL_BASE; ?>/public/common/css/img/ajax-loaders/ajax-loader-1.gif" /> Please wait we are verifying your details</p>
                </form>
        </div>
    </div>
    <!--<a id="close_x" class="close sprited" href="#"></a>-->
    </div>
    <div class="login_pop_bot"></div>
</div>
<!-- Phone number popup Form End -->



<!-- Customer Signup Form -->
<div id="customer_sign_up">
    <div class="login_pop_top"></div>
    <div class="login_pop_mid">
    

              
 <div class="login_title_top">
            <div class="sign_up_ttle">

                <span class="sign_image"></span>
                <b>Sign Up</b>
            </div>

        </div>

    <div class="login_total">      

        <h2></h2>
        <div class="login_form">
                    <form name="customersignup" id="customersignup" method="get" action="<?php echo URL_BASE; ?>passengers/signup">
                        <ul>

                            <li>
                              
                                <div class="controls">

					<select style="width:310px;" name="salutation" id="customer_salutation" >
						<option value="">Select Salutation</option>	
						<option value="Mr">Mr.</option>
						<option value="Ms">Ms.</option>
						<option value="Mrs">Mrs.</option>
					</select>
                                        
                                        <span class="sing_name"></span>
                                    </div>
                                <span style="color:red;margin-right:32px;" class="signup_error" id="customer_salutation_error"></span>
                            </li>


                            <li>
                              
                                <div class="controls">
                                    <div class="input-prepend">
                                        
                                        <input type="text" name="name" placeholder="First Name" id="customer_name" value="" />
                                        <span class="sing_name"></span>
                                    </div>
                                </div> 							     
                                <span style="color:red;" class="signup_error" id="customer_name_error"></span>
                            </li>

                            <li>
                              
                                <div class="controls">
                                    <div class="input-prepend">
                                        
                                        <input type="text" name="lname" placeholder="Last Name" id="customer_lname" value="" />
                                        <span class="sing_name"></span>
                                    </div>
                                </div> 							     
                                <span style="color:red;" class="signup_error" id="customer_lname_error"></span>
                            </li>

                            <li>
                                                           
                                <div class="controls">
                                    <div class="input-prepend">
                                       
                                        <input type="text"  name="email" placeholder="Email" id="customer_email" value="" />
                                         <span class="sing-mail"></span>
                                    </div>
                                </div>	    
                                <span style="color:red;" class="signup_error" id="customer_email_error"></span>
                            </li>

                            <li>
                                <div class="controls">
				    <span style="font: normal 12px arial;width:300px;color:#666;margin-bottom:4px;">Your phone number will be your login ID.</span>	
                                    <div class="input-prepend">
                                        <input type="text"  name="phone" id="customer_phone" value="" placeholder="Phone Number" />
                                         <span class="sing-phone"></span>
                                    </div>
				    	
                                </div>
                                <span style="color:red;" class="signup_error" id="customer_phone_error"></span>
                            </li>
							                            <li>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <input type="text"  name="creditcard_no" id="customer_creditcard_no" value="" placeholder="Credit Card Number" maxlength="16" />
                                         <span class="sing_pasword"></span>
                                    </div>
				    	
                                </div>
                                <span style="color:red;" class="signup_error" id="customer_creditcard_no_error"></span>
                            </li>

                            <li>
                                <div class="controls">
                                    <div class="input-prepend">
                                        <input style="width:150px;" type="text"  name="creditcard_cvv" id="customer_creditcard_cvv" value="" placeholder="CVV" maxlength="4" />
                                         <span class="sing_pasword"></span>
                                    </div>
				    	
                                </div>
                                <span style="color:red;" class="signup_error" id="customer_creditcard_cvv_error"></span>
                            </li>



                                <li>
					<div class="controls">


				
			<select style="width:120px;"  name="expdatemonth" id="customer_expDateMonth" class="credit_card fl">
					<option value="">Month</option>	
        								<option value="01" >01</option>
									<option value="02" >02</option>
									<option value="03" >03</option>
									<option value="04" >04</option>
									<option value="05" >05</option>
									<option value="06" >06</option>
									<option value="07" >07</option>
									<option value="08" >08</option>
									<option value="09" >09</option>
									<option value="10" >10</option>
									<option value="11" >11</option>
									<option value="12" >12</option>
							</select>
			<select style="width:120px;margin-left:10px;" name="expdateyear" id="customer_expDateYear" class="credit_card fl ml10">
					<option value="">Year</option>	
									<option value="2014">2014</option>
									<option value="2015">2015</option>
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
									<option value="2020">2020</option>
									<option value="2021">2021</option>
									<option value="2022">2022</option>
									<option value="2023">2023</option>
									<option value="2024">2024</option>
									<option value="2025">2025</option>
									<option value="2026">2026</option>
									<option value="2027">2027</option>
									<option value="2028">2028</option>
									<option value="2029">2029</option>
									<option value="2030">2030</option>
									<option value="2031">2031</option>
									<option value="2032">2032</option>
									<option value="2033">2033</option>
									<option value="2034">2034</option>
					
			</select>

					</div>  
				<span style="color:red;" id="customer_expirydate_error" class="signup_error"></span>                                                        
				</li>


                            <li>
                                <input type="button" name="signup" class="btn btn-inverse "  style="margin-left:100px;" id="signup" value="Sign Up" title="Sign Up" onclick="return validate_customersignup();" />
                                
                            </li>
                            <p id="customer_error1" style="display:none"><img alt="ajax-loader" src="<?php echo URL_BASE; ?>/public/common/css/img/ajax-loaders/ajax-loader-1.gif" /> Please wait we are verifying your details</p>
                            </li></ul>
            </form> 
        </div>
    </div>
    <a id="close_x" class="close sprited" href="javascript:;" onclick="hide_req2()" ></a>
    </div>
    <div class="login_pop_bot"></div>
</div>
<!-- Customer Signup Form End -->

<!-- Book Now Section -->
<div id="booknow">
    <h2>BOOK YOUR TAXI NOW!</h2>
    <div class="login_total">                                               
        <div class="login_form">
            <form name="bookinglogin" method="post" action="">
                <ul>
                    <li><span>Pick up Location:</span><span id="c_location"></span></li>
                    <li><span>Drop Location:</span><span id="d_location"></span></li>
                    <li><span>Driver Name</span><span id="driver_name"></span></li>
                    <li><span>No of Passengers</span><span id="n_passengers"></span></li>
                    <li><span>Pick up Time</span>
                        <span id="pickuptime"></span>
                        <input type="hidden" name="driver_id" id="driver_id" value="">
                        <input type="hidden" name="driver_away_in_km" id="driver_away_in_km" value="">
                        <span style="color:red;" class="signup_error" id="timepicker1_error"></span>
                    </li>
                    <li><span>Round Trip?</span><input type="checkbox" name="pickupdrops" id="pickupdrops" onclick="chk()" />
                        <input type="hidden" name="pickupdrop_val" id="pickupdrop_val" value="0">
                        <input type="hidden" name="taxi_id" id="taxi_id" value="" >

                    </li>
                                         <!--<li><span></span>
                     <select name="waitingtime">
                                                             <option>-- Select --</option>
	
                     </select> -->
                    <!-- <div class="input-append bootstrap-timepicker">
                            <input id="waitingtime" type="text" class="input-small">
                            <span class="add-on"><i class="icon-time"></i></span>
                    </div> -->

                    <span style="color:red;" class="signup_error" id="timepicker1_error"></span>
                    </li>
                    <li><span></span>
                        <input type="button" class="btn btn-inverse" value="Confirm" title="Confirm" onclick="return validatebooking();" />                                        
                    </li>

                </ul> 
            </form>
        </div>

        <span id="book_result"></span>  
    </div>
    <a id="close_x" class="close sprited" href="javascript:;">close</a>
</div>
<!-- GET FREE TRAIL POPUP -->
<div id="get_free_trial" class="demo_contact_popup_outer">    
    <a id="close_x" class="close sprited demo_contact_popup_close" href="javascript:;"></a>   
    <div class="demo_contact_popup">      
        <h3 class="demo_frm_title">Please enter the details below</h3>
        <div class="login_form">
            <form name="gettrialform" id="gettrialform" method="post" action="">
                <div class="demo_popup_frm">
                    <div class="demo_popup_frm_left">
                        <div class="demo_popup_row">      
                            <div class="popup_label"><p>Name*</p></div>
                            <div class="popup_txtbox">                                                                            
                                <input type="text" name="g_name" placeholder="Name" id="g_name" value="" />
                                 <span class="sing_name"></span>                                                                    							     
                                <span style="color:red;" class="signup_error" id="g_name_error"></span>
                            </div>
                        </div>
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Email*</p></div>
                              <div class="popup_txtbox">  


                                      <input type="text" name="g_email" placeholder="Email" id="g_email" value="" />
                                      <span class="sing-mail"></span>
                                  <span style="color:red;" class="signup_error" id="g_email_error"></span>
                              </div> 							     

                          </div>
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Phone Number*</p></div>                          
                           <div class="popup_txtbox">  

                                   <input type="text"  name="g_phone" id="g_phone" value="" placeholder="Phone Number" />
                                    <span class="sing-phone"></span>

                           <span style="color:red;" class="signup_error" id="g_phone_error"></span>    	
                           </div>

                       </div>
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Company Name*</p></div>
                              <div class="popup_txtbox">


                                      <input type="text" name="company_name" placeholder="Company Name" id="company_name" value="" />
                                      <span class="error"></span>
                                  <span style="color:red;" class="signup_error" id="company_name_error"></span>
                              </div> 							     
                      </div>
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Number of Taxi*</p></div>
                            <div class="popup_txtbox">

                                    <input type="text"  name="no_of_taxi" id="no_of_taxi" value="" placeholder="Number of Taxi" maxlength="16" />

                            <span style="color:red;" class="signup_error" id="no_of_taxi_error"></span>    	
                            </div>

                        </div>
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Country Name*</p></div>
                            <div class="popup_txtbox">                                    
                                                                             <!--<select id="g_country"  name="g_country" onchange="change_city()" style="color:rgb(163, 159, 159) ! important;">
                                                                                     <option value=''>Select Country</option>
                                                                            
                                    </select>--->
                                     <input type="text" name="g_country" id="g_country" placeholder="Country Name" />

                            <span style="color:red;" class="signup_error" id="g_country_error"></span>    	
                            </div>

                        </div>											
                    </div>
                    <div class="demo_popup_frm_left">											
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>City Name*</p></div>
                            <div class="popup_txtbox">
                                <div class="" id="city_list" >
                                    <!--<select name="g_city" id="g_city" style="color:rgb(163, 159, 159) ! important;">
                                                                                    <option value=''>Select City</option>
                                    </select>-->
                                    <input type="text" name="g_city" id="g_city"  placeholder="City Name" />
                                </div>
                                <span style="color:red;" class="signup_error" id="g_city_error"></span>    	
                            </div>                                    
                        </div>  
						<div class="demo_popup_row">
                            <div class="popup_label"><p>Budget*</p></div>
                            <div class="popup_txtbox">                                    
								<select name="budget" id="budget" class="company_budget">
									<option value="">Choose budget</option>
									<option value="Less than $10,000">Less than $10,000</option>
									<option value="$10,001 to $15,000">$10,001 to $15,000</option>
									<option value="$15,001 to $20,000">$15,001 to $20,000</option>
									<option value="$20,001 to $25,000">$20,001 to $25,000</option>
									<option value="More than $25,001">More than $25,001</option>
								</select>
                            <span style="color:red;" class="signup_error" id="budget_error"></span>    	
                            </div>
                        </div>						
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Message*</p></div>
                            <div class="popup_txtbox">
                                <textarea name="message" id="message" placeholder="Message" /></textarea>
                                <span style="color:red;" class="error" id="message_error"></span>
                            </div>
                        </div>                                
                        <div class="demo_popup_row">
                            <div class="popup_label"><p>Security Code*</p></div>
                            <div class="popup_txtbox shownn_label">
                                <div id="code">
                                    <div class="fl">
                                        <div class="name2">
                                            <b>&nbsp;</b>
                                        </div>
                                        <div class="chgcapchafont"><p id="chgcapcha" style="text-transform:lowercase;" >mkusc                                            </p><img src="<?php echo URL_BASE; ?>public/common/images/reload.png" width="28px" height="28px" alt="" id="refresh" 
                                                     onclick="javascript:change_captchasad('<?php echo URL_BASE; ?>')" />
                                        </div>
                                        <input type="text"  title="Enter the security code" placeholder="Security Code" name="security_code" />
                                        <label class="control-group err"> <span style="cursor:text;" id="security_code_error"> </span></label>
                                        <input type="hidden" name="org_captcha" id="org_captcha" value="mkusc" />									
                                    </div>
                                </div>
                            </div>
                        </div>                                                                                    
                    </div>
					<div class="popup_submit_block">
						<div class="demo_popup_row">                            
                            <div class="popup_txtbox">
                                <input type="button" name="get_trial" class="yellow_but"   id="get_trial" value="Start Free Trial" title="Start Free Trial" onclick="validate_gettrial()" />
                            </div>
                        </div>
                        <div class="demo_popup_row">

                            <p id="customer_error1" class="customer_error1" style="display:none"><img alt="ajax-loader" src="<?php echo URL_BASE; ?>/public/common/css/img/ajax-loaders/ajax-loader-1.gif" /> Please wait we are verifying your details</p>
                        </div>
					</div>
                </div>
            </form> 
        </div>
    </div>    
</div>


<!-- Book Now End -->
<script type="text/javascript">

    $(function () {
 


			/*var todayDate = new Date();
			var hours = todayDate.getHours();
			var mins = todayDate.getMinutes()+5;
			var secs = todayDate.getSeconds();
			*/
			/*
                            if(hours >= 12)
                            {
                                hours = hours - 12;
                                var stas = ' PM';
                            }
                            else
                            {
                                var stas = ' AM';
                            }
                            if(hours < 10)
                            {
                                hours = '0'+hours;
                            }		
                            if(mins < 10)
                            {
                                mins = '0'+mins;
                            }
                            if(secs < 10)
                            {
                                secs = '0'+secs;
                            }		
                            var current_time = hours+':'+mins+':'+secs+stas;
                            $("#current_time").html(current_time);

			*/

			/*if(mins >= 60)
			{
			mins = mins - 60;
			if(hours < 24)
			{
			hours = hours + 1;
			} 
			else
			{
			hours =  1;
			}
			}
			var amPM = hours > 11 ? "PM" : "AM";
			hours = hours % 12;
			hours = hours ? hours : 12; // zero = 12				
			mins = mins > 9 ? mins : "0" + mins;
			secs = secs > 9 ? secs : "0" + secs;
			hours = hours > 9 ? hours: "0" + hours;	

			var current_time = hours + ":" + mins + ":" + secs + " " + amPM;
			*/
	 			
                        $("#current_time").html('03:15 AM'); 

		
                        $("#country_id").selectbox();
                        $("#country_id2").selectbox();
                
                    					$('#timepicker1').timepicker({
                            showMeridian:true,
                            showSeconds: false,
                            minuteStep:5,
                            secondStep:30,
                            showInputs:false,
                            defaultTime: $('#current_time').html(),
                        });
						

		                
                    });
    
    
                    <!-- Login & Register Box -->
                    function showpopup(driver_id,isbooking,driver_name,taxi_id)
                    {			
                        console.log(isbooking);
                        $('#isbooking').val(isbooking);
                        $('#d_id').val(driver_id);
                        $('#driver_name').text(driver_name);
                        $("#taxi_id").val(taxi_id);
                        $("#sign_up").lightbox_me({centered: true, onLoad: function() {
                                $("#sign_up").find("input:first").focus();
                            }});					
                        event.preventDefault();
                    }
                    $("#phone").keypress(function(event) { return checkisNumber(event) });

		    function showpopup_signin(driver_id,isbooking,driver_name,taxi_id)
		    {
                        console.log(isbooking);
                        $('#isbooking').val(isbooking);
                        $('#d_id').val(driver_id);
                        $('#driver_name').text(driver_name);
                        $("#taxi_id").val(taxi_id);
                        $("#customer_sign_in").lightbox_me({centered: true, onLoad: function() {
                                $("#customer_sign_in").find("input:first").focus();
                            }});	
                        //document.forms['customerlogin'].submit();				
                        event.preventDefault();
   		    } 	
   		    
   		        function showpopup_signup(driver_id,isbooking,driver_name,taxi_id)
		    {
                        console.log(isbooking)   ;
                        $('#isbooking').val(isbooking);
                        $('#d_id').val(driver_id);
                        $('#driver_name').text(driver_name);
                        $("#taxi_id").val(taxi_id);
                        $("#customer_sign_up").lightbox_me({centered: true, onLoad: function() {
                                $("#customer_sign_up").find("input:first").focus();
                            }});					
                        event.preventDefault();
   		    } 	
   		    
   		    /* function showpopup_signin(driver_id,isbooking,driver_name,taxi_id)
		    {
                        console.log(isbooking);
                        $('#isbooking').val(isbooking);
                        $('#d_id').val(driver_id);
                        $('#driver_name').text(driver_name);
                        $("#taxi_id").val(taxi_id);
                        $("#customer_sign_in").lightbox_me({centered: true, onLoad: function() {
                                $("#customer_sign_in").find("input:first").focus();
                            }});					
                        event.preventDefault();
   		    } */	

		    function  phone_popup()
		    {
                        $("#phone_number").css('display','block');
                        $("#phone_number").lightbox_me({centered: true,closeClick: false,closeEsc: false, onLoad: function() {
							 
                               // $("#customer_sign_up").find("input:first").focus();
                            }});					
                        event.preventDefault();
   		    } 	

                    function showdriverpopup()
                    {
                        $("#driver_sign_in").lightbox_me({centered: true, onLoad: function() {
                                $("#driver_sign_in").find("input:first").focus();
                            }});					
                        event.preventDefault();
                    }
                    
                    function teltofriendpopup()
                    {
                        $("#tell_to_friend").lightbox_me({centered: true, onLoad: function() {
                                $("#tell_to_friend").find("input:first").focus();
                            }});					
                        event.preventDefault();
                    }
                    
                     function gettrialpopup()
                    {
                        $("#get_free_trial").lightbox_me({centered: true,onLoad: function() {
                                $("#get_free_trial").find("input:first").focus();
                            }});					
                        event.preventDefault();
                    }

                    function booknow(driver_id,driver_name,taxi_id,driver_away_in_km)
                    {
						//alert('as');
                        $('#book_result').html('');
                        var div_id = '#driver_'+driver_id;
                        var current_location = $('#current_location').val();
                        var drop_location = $('#drop_location').val();
                        var no_passengers = $('#no_passengers').val();
                        var pickuptime = $('#timepicker1').val();

                        if(current_location == '' || drop_location == '')
                        {
                            valid = true;
                            if(current_location == ''){
                                $('#current_location_error').html("Please enter the current location");
                                $('#current_location').focus();
                                return false;
                            }
                            else if(drop_location == '')
                            {
                                $('#current_location_error').hide();
                                $('#drop_location_error').html("Please enter the drop location");
                                $('#drop_location').focus();
                                return false;
                            }		
                            /*else if(no_passengers == '')
                            {
                                $('#drop_location_error').hide();
                                $('#no_passengers_error').html("Please enter the number of passengers");
                                $('#no_passengers').focus();
                                return false;
                            }*/
                        }
                        else
                        {
                            $('#no_passengers_error').hide();
                            $("#c_location").text(current_location);
                            $("#d_location").text(drop_location);
                            $("#n_passengers").text(no_passengers);
                            $("#driver_id").val(driver_id);
                            $("#taxi_id").val(taxi_id);
                            $("#driver_away_in_km").val(driver_away_in_km);
                            $('#pickuptime').text(pickuptime);
                            $('#driver_name').text(driver_name);
                            $("#booknow").lightbox_me({centered: true, onLoad: function() {
                                    $("#booknow").find("input:first").focus();
                                }});					
                            event.preventDefault();		
                        }	   
                    }

		   
                    /***********Book Now************/

                                        $('#waitingtime').timepicker({
                        showMeridian:false,
                        showSeconds: true,
                        showInputs:false,
                        defaultTime:'00:00:00',
                        //defaultTime:false,
                    });
                                        function validatebooking()
                    {
                        var c_location = $("#c_location").text();
                        var d_location = $("#d_location").text();
                        var n_passengers = $("#n_passengers").text();		
                        var timepicker1 = $("#timepicker1").val();		   
                        var waitingtime =  $('select[name="waitingtime"]').val();
                        var driver_id = $('#driver_id').val();
                        var taxi_id = $('#taxi_id').val();
                        var pickupdrop = $("#pickupdrop_val").val();
                        var pass_logid = $("#pass_logid").val();
                        var driver_away_in_km = $("#driver_away_in_km").val();
                        var notes = $("#notes").val();
                        console.log(notes);
                        //alert(driver_away_in_km);
                        if(timepicker1 == '')
                        {
                            valid = true;
                            if(timepicker1 == '')
                            {
                                $('#timepicker1_error').html("Please enter time");
                                $('#timepicker1').focus();
                                return false;
                            }
                        }
                        else
                        {
                            $('#timepicker1_error').hide();
                            $('#book_result').html('<img alt="process" src="'+SrcPath+'/public/common/images/process.gif" />');					
                            //alert(pickupdrop);
                            var url= '/passengers/savebooking/?c_location='+c_location+'&d_location='+d_location+'&n_passengers='+n_passengers+'&timepicker1='+timepicker1+'&driver_id='+driver_id+'&pickupdrop='+pickupdrop+'&waitingtime='+waitingtime+'&taxi_id='+taxi_id+'&pass_logid='+pass_logid+'&driver_away_in_km='+driver_away_in_km+'&notes='+notes;
                            $.post(url,function(check){				
                                $('#book_result').html(check);
                                //$('#current_location').val('');
                                //$('#drop_location').val('');
                                //$('#no_passengers').val('');
                                //setTimeout("location.reload(true);",2000);
                                //location.reload(true);
                            });
                        }
                    }
                    function chk()
                    {
                        var pickupdrop;
                        if($('#pickupdrops').is(':checked')){ 
                            pickupdrop = $("#pickupdrop_val").val('1');
                        }else{pickupdrop = $("#pickupdrop_val").val('0');}
                    }
                    /***********************************************************/
                    //Numeric Function
                    function checkisNumber(evt) {
						//alert();
                        evt = (evt) ? evt : window.event;
                        var charCode = (evt.which) ? evt.which : evt.keyCode;
                        //alert(charCode);
                        if (charCode > 31 && (charCode < 48 || charCode > 57) && (charCode != 46)) {
                            return false;
                        }
                        return true;
                    } 
                    <!-- Login & Register Box End -->

</script>
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/jquery.selectbox-0.2.js"></script>
<script type="text/javascript">
    /*$(document).ready(function() {
                           $("#booknow").lightbox_me({centered: true, onLoad: function() {
                           $("#booknow").find("input:first").focus();
                           }});					
                                event.preventDefault();	
                        });*/


function driver_logout()
{

                var dataS = '';

                        var response;
                        $.ajax
                        ({ 			
                                type: "POST",
                                url: SrcPath+"driver/get_current_driverstatus", 
                                data: dataS, 
                                cache: false, 
                                dataType: 'html',
                                success: function(response) 
                                { 	
                                        var reval = response.replace(/\s/g, "");

                                        if(reval == 'IN')
                                        {	
                                                window.location= SrcPath+'driver/logout';
                                                //alert("Kindly ShiftOut and then logout your session.");
                                                //window.location= SrcPath+'driver/dashboard';
                                                //return false;
                                        }
                                        else
                                        {

                                                window.location= SrcPath+'driver/logout';
                                        }

                                 }		 
                        });	


}


$('#customerlogin').submit(function() {
customer_validatelogin
});

 function change_captchasad(path){
        var url=path+"users/get_trialcaptch";
        var x, c = '#';
        var i = 3;
        while (i--) {
            x = (Math.random()*256|0).toString(16).toUpperCase();
            c += (x.length < 2? '0' : '') + x;
        }
        $.post(url, {}, function(response){$('#code').html(response);$('#chgcapcha').css("color",c);
        });

   }


   function validate_gettrial()
{
        //var form_values = $('#gettrialform').serialize();alert(form_values);
    if($('#gettrialform').valid()) {
        $('.customer_error1').show();
           var form_values = $('#gettrialform').serialize();
                  $.ajax({
                        url:"<?php echo URL_BASE; ?>company/getfreetrial",
                        type:"POST",
                        async: true,
                        data:form_values,
                        success:function(data){
                                if(data == 1) {
                                        $('.customer_error1').hide();				
                                        window.location.href = '/free-trial-thank-you.html';
                                        //location.reload();
                                }
                        }
                });
        }
}
/*$.validator.addMethod("phonevalidation", function(phone_number, element) {
    phone_number = phone_number.replace(/\s+/g, ""); 
        return this.optional(element) || (phone_number.length > 5) &&
                phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
}, $.validator.format('my message'));
*/
$("#gettrialform").validate({
           errorPlacement: function ($error, $element) {
        var name = $element.attr("name");
        $('#'+name+'_error').append($error);
    },
    errorClass:'error',
                rules: {
                                g_name:{
                                        required:true,
                                },
                                company_name:{
                                        required:true,
                                },
                                g_email:{
                                        required:true,
                                        email:true
                                },
                                g_country:{
                                        required:true,
                                },
                                g_city:{
                                        required:true,
                                },
                                budget:{
                                        required:true,
                                },
                                g_phone:{
                                        required:true,
                                        //phonevalidation:true,
                                        number:true,
                                        minlength:5,
                                        maxlength:15,
                                },
                                no_of_taxi: {
                                required:true,
                                number:true,
                                },
                                security_code: {
                                required:true,
                                equalTo:"#org_captcha",
                                },
                                message: {
                                required:true,
                                },

                },
                messages: {	
                            g_name:{
                                        required:"Name is required",
                                },					
                                company_name:{
                                        required:"Company name is required",
                                },
                                g_email:{
                                        required:"Email Id is required",
                                        email:"Enter Valid email id"
                                },
                                budget:{
                                        required:"Choose your budget",
                                },
                                g_phone:{
                                        required:"Mobile Number is required",
                                        number:"Mobile Number must be a number",
                                        //phonevalidation:"Please enter a valid phone number",
                                        minlength:"Please enter a valid phone number",
                                        maxlength:"Please enter a valid phone number",
                                },
                                no_of_taxi: {
                                required:"Number of Taxi is required",
                                number:"Number of Taxi must be a number",
                                },
                                security_code: {
                                required:"Enter the Captcha",
                                 equalTo:"Captcha does not match",
                                },
                                message: {
                                required:"Message is required",
                                },
                                g_country:{
                                        required:"Country is required",
                                },
                                g_city:{
                                        required:"City is required",
                                },
        },
});	

function change_city()
{
                var countryid= $("#country").val();

                  $.ajax({
                        url:"<?php echo URL_BASE; ?>company/getcitylist",
                        type:"get",
                        data:"country_id="+countryid,
                        success:function(data){
                        $('#city_list').html();
                        $('#city_list').html(data);
                        },
                        error:function(data)
                        {
                                //alert("failed");
                        }
                });	

}
$(".close").click(function(){
        $(".lb_overlay").hide();
        $(".demo_contact_popup_outer").hide();
        $("#customer_sign_up").hide();
        $("#customer_sign_in").hide();
        $("#driver_sign_in").hide();
        $("#booknow").hide();
        $("#citylist_popup").hide();
        $("#sign_up").hide();
});
function hide_req2()
{
        //$('#c_info').hide();
        $('div#fade').remove();
}

function hide_req_op(gm)
{ 
    $('#'+gm).hide();    
    $('div#fade').remove();
}


/*menu left*/

(function($) {
                                $(document).ready(function() {
                                        $.slidebars();
                                });
                        }) (jQuery);


/*** Header Animation ***/
$(window).scroll(function() {

    if ($(this).scrollTop() > 1){ 

        $('.header_outer_cont').addClass("sticky");
    }
    else{
        $('.header_outer_cont').removeClass("sticky");
    }
});
/** animation End ***/


$(document).ready(function() {
        $('.key_list,.key_block_right,.home_subtitle,.support_info,.gsteps_btm_block,.fc_image,.fc_info,.feature_artical_inner,.service_icon,.faq_block,.list,.about_text,.about_title,.global_locations ').addClass("").viewportChecker({
            classToAdd: 'visible animated fadeInDown', // Class to add to the elements when they are visible
            offset: 100    
           }); 

 $('.header_outer_cont').removeClass("sticky");	   
});

</script>

 
<script type="text/javascript" src="<?php echo URL_BASE; ?>public/common/js/script.js"></script>  
<script type="text/javascript">
    var __lc = {};
    __lc.license = 5324991;

    (function() {
        var lc = document.createElement('script'); lc.type = 'text/javascript'; lc.async = true;
        lc.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'cdn.livechatinc.com/tracking.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(lc, s);
    })();
</script>

</body>
</html>
