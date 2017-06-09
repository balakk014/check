<?php defined('SYSPATH') OR die("No direct access allowed."); //print_r($user_details[0]); ?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
         
	<?php /*
	<td class="titlebold" colspan="2" style="text-transform:uppercase;" ><?php echo ucfirst($user_details[0]['company_name']).'  '.ucfirst(__(' companyinformation')); ?></td>
	*/ ?>
	<div class="driverinfo_common">
	<h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2>
           <ul>
           <li><label><?php echo __('firstname'); ?></label>
		   <?php if(isset($user_details[0]['name'])) { echo $user_details[0]['name']; } else { echo ''; } ?>
           </li>
				   
           <li><label><?php echo __('lastname'); ?></label>
		   <?php if(isset($user_details[0]['lastname'])) { echo $user_details[0]['lastname']; } ?>
		   </li>
           
           <li><label><?php echo __('email'); ?></label>
            <?php if(isset($user_details[0]['email'])) { echo $user_details[0]['email']; } ?>
            </li>
           
           <li><label><?php echo __('mobile'); ?></label>
		   <?php if(isset($user_details[0]['phone'])) { echo $user_details[0]['phone']; } ?>
		   </li>
		   
           <li><label><?php echo __('address'); ?></label>
		   <?php if(isset($user_details[0]['address'])) { echo $user_details[0]['address']; } ?>
		   </li>
	</ul>
	
       <?php if($user_details[0]['user_type'] != 'N') { ?>   
                  
	   <h2 class="tab_sub_tit"><?php echo ucfirst(__('companyinformation')); ?></h2>
       <?php //print_r($user_details); ?>
       
 
           <ul>
           <li><label><?php echo __('companyname'); ?></label>
		   <?php if(isset($user_details[0]['company_name'])) { echo $user_details[0]['company_name']; } ?>			
		   </li>  
                      		   
         <?php /* <tr>
           <td valign="top" width="20%"><label><?php echo __('companyaddress'); ?></label></td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['company_address'])) { echo $user_details[0]['company_address']; } ?>				
		   </div>
	   </td>   	
           </tr> */ ?>

	<li><label><?php echo __('country_label'); ?></label>
		   <?php if(isset($user_details[0]['country_name'])) { echo $user_details[0]['country_name']; } ?>				
		   </li>

	<li><label><?php echo __('state_label'); ?></label>
		   <?php if(isset($user_details[0]['state_name'])) { echo $user_details[0]['state_name']; } ?>				
		   </li>
	
	<li><label><?php echo __('city_label'); ?></label>
		   <?php if(isset($user_details[0]['city_name'])) { echo $user_details[0]['city_name']; } ?>				
		   </li>
	
	<?php /*<tr>
	<td valign="top" width="20%"><label><?php echo __('company_bal_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php echo $company_info[0]['company_currency']; ?><?php if(isset($user_details[0]['account_balance'])) { echo $user_details[0]['account_balance']; } ?>				
		   </div>
	</td>   	
	</tr> */ ?>

	<?php if(!empty($company_info))
	{
		?>

	<?php /* <tr>
	<td valign="top" width="20%"><label><?php echo __('company_domain'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_domain'])) { echo $company_info[0]['company_domain']; } ?><?php echo '.'.DOMAIN_NAME; ?>				
		   </div>
	</td>   	
	</tr> */ ?>
<?php /*
	<tr>
	<td valign="top" width="20%"><label><?php echo __('company_api_key'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_api_key'])) { echo $company_info[0]['company_api_key']; } ?>			
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('customer_app_url'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['customer_app_url'])) { echo $company_info[0]['customer_app_url']; } ?>			
		   </div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('driver_app_url'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['driver_app_url'])) { echo $company_info[0]['driver_app_url']; } ?>			
		   </div>
	</td>   	
	</tr>


		
	<tr>
	<td valign="top" width="20%"><label><?php echo __('app_name_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_app_name'])) { echo $company_info[0]['company_app_name']; } ?>			
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('app_description_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_app_description'])) { echo $company_info[0]['company_app_description']; } ?>			
		   </div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('comapany_tag_line'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_tagline'])) { echo $company_info[0]['company_tagline']; } ?>			
		   </div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('contact_email_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_email_id'])) { echo $company_info[0]['company_email_id']; } ?>			
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('contact_phone_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_phone_number'])) { echo $company_info[0]['company_phone_number']; } ?>			
		   </div>
	</td>   	
	</tr>
	
	<?php /*
	<tr>
		<td valign="top" width="20%"><label><?php echo __('notification_settings_label'); ?></label></td>        
		<td>
			   <div class="new_input_field">
			   <?php if(isset($company_info[0]['company_notification_settings'])) { echo $company_info[0]['company_notification_settings']; } ?>			
			   </div>
		</td>   	
	</tr>	
		

	<tr>
	<td valign="top" width="20%"><label><?php echo __('meta_key_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_meta_keyword'])) { echo $company_info[0]['company_meta_keyword']; } ?>			
		   </div>
	</td>   	
	</tr>	

	<tr>
	<td valign="top" width="20%"><label><?php echo __('meta_desc_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_meta_description'])) { echo $company_info[0]['company_meta_description']; } ?>			
		   </div>
	</td>   	
	</tr>	
				



	<tr>
	<td valign="top" width="20%"><label><?php echo __('sms_enable'); ?></label></td>        
	<td>
		   <div class="new_input_field">
			   
			   <?php $sms_chk=$company_info[0]['company_sms_enable']; ?>
		   <?php if($sms_chk == '1') { echo __('yes'); } if($sms_chk == '0') { echo __('no'); } ?>			
		   </div>
	</td>   	
	</tr>	*/ ?>


	<li><label><?php echo __('company_currency'); ?></label>
		   <?php if(isset($company_info[0]['company_currency'])) { echo $company_info[0]['company_currency']; } ?>			
		   </li>	

	<?php /*<tr>
	<td valign="top" width="20%"><label><?php echo __('site_copyrights_label'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_copyrights'])) { echo $company_info[0]['company_copyrights']; } ?>			
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('company_logo_label'); ?></label></td>        
	<td>

		   	   <?php if(!empty($company_info[0]['company_logo'])&&file_exists(DOCROOT.SITE_LOGO_IMGPATH.$company_info[0]['company_logo'])){ ?>
				<div class="site_logo" style="width:160px;">
				<img src="<?php echo URL_BASE.SITE_LOGO_IMGPATH.'/'.$company_info[0]['company_logo'];?>" width="160">
				</div>
				<?php } ?>		

	</td>   	
	</tr>	
	

	<tr>
	<td valign="top" width="20%"><label><?php echo __('company_favicon_label'); ?></label></td>        
	<td>

		   		<?php if(!empty($company_info[0]['company_favicon'])&&file_exists(DOCROOT.SITE_FAVICON_IMGPATH.$company_info[0]['company_favicon'])){ ?>
				<div class="site_logo" style="width:220px;"> 
				<img src="<?php echo URL_BASE.SITE_FAVICON_IMGPATH.$company_info[0]['company_favicon'];?>">
				</div>
				<?php } ?>	

	</td>   	
	</tr>			

	<tr>
	<td valign="top" width="20%"><label><?php echo __('facebook_key'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_facebook_key'])) { echo $company_info[0]['company_facebook_key']; } ?>			
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('facebook_secretkey'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_facebook_secretkey'])) { echo $company_info[0]['company_facebook_secretkey']; } ?>			
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('facebook_share'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_facebook_share'])) { echo $company_info[0]['company_facebook_share']; } ?>			
		   </div>
	</td>   	
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('twitter_share'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_twitter_share'])) { echo $company_info[0]['company_twitter_share']; } ?>			
		   </div>
	</td>   	
	</tr>	
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('google_share'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_google_share'])) { echo $company_info[0]['company_google_share']; } ?>			
		   </div>
	</td>   	
	</tr>	
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('linkedin_share'); ?></label></td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($company_info[0]['company_linkedin_share'])) { echo $company_info[0]['company_linkedin_share']; } ?>			
		   </div>
	</td>   	
	</tr>	*/ ?>
	<?php } ?>							
	 <!--Package Details start-->
	   <?php /* if(count($package_details) > 0) { ?>				
		<tr>
		<td class="titlebold"><?php echo ucfirst(__('current_package_detail')); ?></td>
		<td></td>	          
	   </tr>
	   
		<tr>
		<td valign="top" width="20%"><label><?php echo __('package_name'); ?></label></td>        
		<td>
			   <div class="new_input_field">
			   <?php if(isset($package_details[0]['package_name'])) { echo $package_details[0]['package_name']; } ?>				
			   </div>
		</td>   	
		</tr>

		<tr>
		<td valign="top" width="20%"><label><?php echo __('package_type'); ?></label></td>        
		<td>
			   <div class="new_input_field">
				<?php if($package_details[0]['package_type'] == 'T' ) { echo __('transaction_based_commission'); } else if($package_details[0]['package_type'] == 'P' ) { echo __('package_based_commission'); } else if($package_details[0]['package_type'] == 'N' ) { echo __('package_based_no_commission'); } ?>

			   </div>
		</td>   	
		</tr>
		
		<tr>
		<td valign="top" width="20%"><label><?php echo __('no_of_taxi'); ?></label></td>        
		<td>
			   <div class="new_input_field">
			   <?php if(isset($package_details[0]['total_taxi'])) { echo $package_details[0]['total_taxi']; } ?>				
			   </div>
		</td>   	
		</tr>
		
		<tr>
		<td valign="top" width="20%"><label><?php echo __('no_of_driver'); ?></label></td>        
		<td>
			   <div class="new_input_field">
			   <?php if(isset($package_details[0]['total_driver'])) { echo $package_details[0]['total_driver']; } ?>				
			   </div>
		</td>   	
		</tr>
		
		<tr>
		<td valign="top" width="20%"><label><?php echo __('expiry_date'); ?></label></td>        
		<td>
			   <div class="new_input_field">
			   <?php if(isset($package_details[0]['upgrade_expirydate'])) { echo Commonfunction::getDateTimeFormat($package_details[0]['upgrade_expirydate'],1); } ?>
			   </div>
		</td>   	
		</tr>

	  <?php } */
		/*else
		{ ?>
		<tr>
     
		<td>
			   <div class="new_input_field">
			   <?php echo __('no_package_available');  ?>				
			   </div>
		</td>   	
		</tr>
	<?php }  */ ?>	

	  <!--Package Details end-->
	
	<?php } ?>
	
         </ul>
        </div>
       
<input type="hidden" name="company_id" id="company_id" value="<?php echo $user_details[0]['company_id']; ?>">
<!-- Company Manager -->
<div class="over_all">
<div id="company_manager"></div>
</div>
<!-- Company Manager -->
<!-- Company Driver -->
<div class="over_all">
<div id="company_driver"></div>
</div>
<!-- Company Driver -->
<!-- Company Taxi -->
<div class="over_all">
<div id="company_taxi"></div>
</div>
<!-- Company Taxi -->
<!-- Transaction Details -->
<div class="over_all">
<div id="trans_details"></div>
</div>
<!-- Transaction Details -->

 </div>
        
    </div>
</div>  

<script>
$(document).ready(function(){

var user_type = "<?php echo $_SESSION['user_type']; ?>";
if(user_type == 'A')
{

}
else if(user_type =='C')
{

}
else if(user_type =='M')
{

}
change_managerinfo();
change_driverinfo();
change_taxiinfo();
//change_transinfo();

});

function change_managerinfo()
{
      		var company_id = $("#company_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getcompanymanagerlist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#company_manager').html();
			$('#company_manager').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function pagin_managerinfo(page_no)
{
		var company_id = $("#company_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getcompanymanagerlist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#company_manager').html();
			$('#company_manager').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function change_driverinfo()
{
      		var company_id = $("#company_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getcompanydriverlist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#company_driver').html();
			$('#company_driver').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function pagin_driverinfo(page_no)
{
		var company_id = $("#company_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getcompanydriverlist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#company_driver').html();
			$('#company_driver').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function change_taxiinfo()
{
      		var company_id = $("#company_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getcompanytaxilist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#company_taxi').html();
			$('#company_taxi').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function pagin_taxiinfo(page_no)
{
		var company_id = $("#company_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getcompanytaxilist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#company_taxi').html();
			$('#company_taxi').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function change_transinfo()
{
      		var company_id = $("#company_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/get_translist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#trans_details').html();
			$('#trans_details').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

function pagin_transinfo(page_no)
{
		var company_id = $("#company_id").val();

		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/get_translist",
			type:"get",
			data:"company_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#trans_details').html();
			$('#trans_details').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

</script>
