<?php defined('SYSPATH') OR die("No direct access allowed."); ?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
          <table border="0" cellpadding="5" cellspacing="0" width="100%">
	<tr>
	<td class="titlebold"><?php echo ucfirst(__('personalinform')); ?></td>
	<td></td>	          
	</tr>
           <tr>
           <td valign="top" width="20%"><label><?php echo __('firstname'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['name'])) { echo $user_details[0]['name']; } else { echo ''; } ?>
		   </div>
	   </td>   	
           </tr> 	
				   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('lastname'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['lastname'])) { echo $user_details[0]['lastname']; } ?>
		   </div>
	   </td>   	
           </tr> 
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('email'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">	
		   <?php if(isset($user_details[0]['email'])) { echo $user_details[0]['email']; } ?>
		   </div>
	   </td>   	
           </tr> 
           
           <tr>
           <td valign="top" width="20%"><label><?php echo __('mobile'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['phone'])) { echo $user_details[0]['phone']; } ?>
		   </div>
	   </td>   	
           </tr>                       		   
		   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('address'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['address'])) { echo $user_details[0]['address']; } ?>
		   </div>
	   </td>   	
           </tr>
       <?php if($user_details[0]['user_type'] != 'N' && $user_details[0]['user_type'] != 'S') { ?>        
           <tr>
           <td valign="top" width="20%"><label><?php echo __('date_of_birth'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['dob'])) { echo $user_details[0]['dob']; } ?>
		   </div>
	   </td>   	
	</tr> 
	<?php } ?>
       <?php if($user_details[0]['user_type'] != 'N' && $user_details[0]['user_type'] != 'S') { ?>   
                  
	   <tr>
		<td><?php echo ucfirst(__('companyinformation')); ?></td>
		<td></td>	          
	   </tr>       
       
       
 
           <tr>
           <td valign="top" width="20%"><label><?php echo __('companyname'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['company_name'])) { echo $user_details[0]['company_name']; } ?>
		   </div>
	   </td>   	
           </tr>  
                      		   
           <tr>
           <td valign="top" width="20%"><label><?php echo __('companyaddress'); ?></label>:</td>        
	   <td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['company_address'])) { echo $user_details[0]['company_address']; } ?>
		   </div>
	   </td>   	
           </tr>  	          
	<?php } ?>

	<tr>

	<td valign="top" width="20%"><label><?php echo __('country_label'); ?></label>:</td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['country_name'])) { echo $user_details[0]['country_name']; } ?>
		   </div>
	</td>   	
	</tr>

	<tr>
	<td valign="top" width="20%"><label><?php echo __('state_label'); ?></label>:</td>
	<td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['state_name'])) { echo $user_details[0]['state_name']; } ?>
		   </div>
	</td>      
	</tr>
	
	<tr>
	<td valign="top" width="20%"><label><?php echo __('city_label'); ?></label>:</td>        
	<td>
		   <div class="new_input_field">
		   <?php if(isset($user_details[0]['city_name'])) { echo $user_details[0]['city_name']; } ?>
		   </div>
	</td>   	
	</tr>

	
         </table>

        </div>
        <div class="content_bottom"><div class="bot_left"></div><div class="bot_center"></div><div class="bot_rgt"></div></div>
    </div>
</div>  
