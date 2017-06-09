<?php defined('SYSPATH') OR die("No direct access allowed.");?>

<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle">    
           <div class="driverinfo_common">
               <h2 class="tab_sub_tit"><?php echo ucfirst(__('personalinform')); ?></h2>
            
               <ul>
                   <li><label><?php echo __('firstname'); ?></label>
                       <p><?php if (isset($user_details[0]['name'])) {
    echo $user_details[0]['name'];
} else {
    echo '';
} ?></p>
                   </li> 	
                
                   <li><label><?php echo __('lastname'); ?></label>
                       <p> <?php if (isset($user_details[0]['lastname'])) {
    echo $user_details[0]['lastname'];
} ?></p>	
                   </li> 
                       
                   <li><label><?php echo __('email'); ?></label>
                       <p><?php if (isset($user_details[0]['email'])) {
                       echo $user_details[0]['email'];
                   } ?></p>
                   </li> 
                       
                   <li><label><?php echo __('mobile'); ?></label>
                       <p><?php if (isset($user_details[0]['phone'])) {
                   echo $user_details[0]['phone'];
               } ?></p>	
                   </li>                       		   
                       
                   <li><label><?php echo __('address'); ?></label>
                       <p><?php if (isset($user_details[0]['address'])) {
                   echo $user_details[0]['address'];
               } ?></p>		
                   </li>  
               </ul>
<?php if ($user_details[0]['user_type'] != 'N') { ?>   
               
                   <h2 class="tab_sub_tit"><?php echo ucfirst(__('companyinformation')); ?></h2>
                   <ul>
                       <li><label><?php echo __('companyname'); ?></label>
                           <p><?php if (isset($user_details[0]['company_name'])) {
        echo $user_details[0]['company_name'];
    } ?></p>			
                       </li>  

                       <li><label><?php echo __('companyaddress'); ?></label>
                           <p><?php if (isset($user_details[0]['company_address'])) {
                       echo $user_details[0]['company_address'];
                   } ?></p>				
                       </li>  	          
                           
                       <li><label><?php echo __('country_label'); ?></label>
                           <p><?php if (isset($user_details[0]['country_name'])) {
                       echo $user_details[0]['country_name'];
                   } ?></p>				
                       </li>
                           
                       <li><label><?php echo __('state_label'); ?></label>
                           <p><?php if (isset($user_details[0]['state_name'])) {
                       echo $user_details[0]['state_name'];
                   } ?></p>				
                       </li>
                           
                       <li><label><?php echo __('city_label'); ?></label>
                           <p> <?php if (isset($user_details[0]['city_name'])) {
                       echo ucfirst($user_details[0]['city_name']);
                   } ?>	</p>			
                       </li>
<?php } ?>
               </ul>
           </div>
      
<input type="hidden" name="manager_id" id="manager_id" value="<?php echo $id; ?>">
<input type="hidden" name="company_id" id="company_id" value="<?php echo $user_details[0]['company_id']; ?>">

<!-- Manager Driver -->
<div class="over_all">
<div id="manager_driver"></div>
</div>
<!-- Manager Driver -->
<!-- Manager Taxi -->
<div class="over_all">
<div id="manager_taxi"></div>
</div>
<!-- Manager Taxi -->
 </div>
    </div>  
<script>
$(document).ready(function(){
change_driverinfo();
change_taxiinfo();

});


function change_driverinfo()
{
      		var company_id = $("#company_id").val();

		var page_no = '1';
		  $.ajax({
			url:"<?php echo URL_BASE;?>manage/getmanagerdriverlist",
			type:"get",
			data:"manager_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#manager_driver').html();
			$('#manager_driver').html(data);
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
			url:"<?php echo URL_BASE;?>manage/getmanagerdriverlist",
			type:"get",
			data:"manager_id="+company_id+"&page="+page_no,
			success:function(data){
			$('#manager_driver').html();
			$('#manager_driver').html(data);
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
			$('#manager_taxi').html();
			$('#manager_taxi').html(data);
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
			$('#manager_taxi').html();
			$('#manager_taxi').html(data);
			},
			error:function(data)
			{
				//alert(cid);
			}
		});	
    
}

</script>
