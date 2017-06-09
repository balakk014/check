<?php defined('SYSPATH') OR die("No direct access allowed."); ?>
<?php
	if($_SESSION['user_type'] !='M' && $_SESSION['user_type'] !='S')
	{
		if($_SESSION['user_type'] =='C')
		{
			$company_currency = findcompany_currency($_SESSION['company_id']);
			$company_currency = $company_currency[0]['currency_symbol'];
		}
		else
		{
			$company_currency = CURRENCY;
		}
	} //echo $company_currency;exit;
?>
<div class="container_content fl clr">
    <div class="cont_container mt15 mt10">
       <div class="content_middle"> 
         <form name="editmotor_form" class="form" id="editmotor_form" action="" method="post" enctype="multipart/form-data">
	<div class="driverinfo_common">
	<ul>
	<li><label><?php echo __('motorcompanyname'); ?></label>
	<?php $field_type =''; $field_type =  isset($model_details[0]['motor_id']) ? trim($model_details[0]['motor_id']):''; ?>
	<p class="formRight">            
              <?php
              if($_SESSION['user_type'] == 'C')
		{
			echo $model_name[0]['motor_name'];
		}
		else
		{
            foreach($motor_details as $listings) { ?>
                   <?php if($field_type == $listings['motor_id'] ) {  echo $listings['motor_name'];break; } ?> 
              <?php }
              
           } ?>

              </p>

	</li>
	
	<li><label><?php echo __('model_name'); ?></label>   
	<p>
		<?php
		if($_SESSION['user_type'] == 'C')
		{
			echo $model_name[0]['model_name'];
		}
		else
		{
			echo isset($model_details[0]['model_name'])? trim($model_details[0]['model_name']):''; 
		}
?>
	</p>   	
	</li>
        </ul>
	<!--Fare Details start-->
        <h2 class="tab_sub_tit"> <?php echo ucfirst(__('fare_details')); ?></h2>
         
           
           <ul>
           <li><label><?php echo __('base_fare'); ?>(<?php echo $company_currency; ?>)</label>        
	       <p>	
	       <?php echo isset($model_details[0]['base_fare'])? trim($model_details[0]['base_fare']):''; ?>		   

           </p>   	
           </li>
           
           <li><label><?php echo __('taxi_min_km'); ?></label>       
	       <p>	
	       <?php echo isset($model_details[0]['min_km'])? trim($model_details[0]['min_km']):''; ?>		   

           </p>   	
           </li>   
           
            <li><label><?php echo __('min_fare'); ?>(<?php echo $company_currency; ?>)</label>       
	       <p>
			   <?php echo isset($model_details[0]['min_fare']) ? trim($model_details[0]['min_fare']):''; ?>
		  
           </p>   	
           </li>  
           
           
           <li><label><?php echo __('cancellation_fare'); ?>(<?php echo $company_currency; ?>)</label>      
	       <p>
			   <?php echo isset($model_details[0]['cancellation_fare'])? trim($model_details[0]['cancellation_fare']):''; ?>

           </p>   	
           </li>
           
           <li><label><?php echo __('below_and_above_km'); ?></label>        
	       <p>
			   <?php echo isset($model_details[0]['below_above_km'])? trim($model_details[0]['below_above_km']):''; ?>

           </p>   	
           </li>
           
           <li><?php isset($model_details[0]['below_above_km'])?$adv_blow_km=$model_details[0]['below_above_km']:$adv_blow_km=" ";
		$below=str_replace("%s",$adv_blow_km,__('below_km'));
		$above=str_replace("%s",$adv_blow_km,__('above_km'));
		 ?>
           <label><?php echo $below; ?></label>      
	       <p>
			   <?php echo isset($model_details[0]['below_km'])? trim($model_details[0]['below_km']):''; ?>
		   
           </p>   	
           </li>   
           
           <li><label><?php echo $above; ?></label>  
	       <p>
			   <?php echo isset($model_details[0]['above_km'])? trim($model_details[0]['above_km']):''; ?>
		 
           </p>   	
           </li>  
           
           <li><label><?php echo __('waiting_charge_ph'); ?>(<?php echo $company_currency; ?>)</label>       
	   <p>
		   <?php echo isset($model_details[0]['waiting_time'])? trim($model_details[0]['waiting_time']):''; ?>
	   </p>   	
           </li>  
        <li><label><?php echo __('fare_per_minute'); ?>(<?php echo $company_currency; ?>)</label>       
		   <p>
			   <?php echo isset($model_details[0]['minutes_fare'])? trim($model_details[0]['minutes_fare']):''; ?>
		   </p>   	
        </li> 
        
        <li><label><?php echo __('taxi_min_speed'); ?></label>        
		   <p>
			   <?php echo isset($model_details[0]['taxi_min_speed'])? trim($model_details[0]['taxi_min_speed']):''; ?>
		   </p>   	
        </li> 
            <?php 
            $nc = $model_details[0]['night_charge']; 
           if($nc==1){ ?>
           <li>
           <label><?php echo __('night_charge'); ?></label>       
	       <p>
			   <?php $nfield_type =''; $nfield_type =  isset($model_details[0]['night_charge']) ? trim($model_details[0]['night_charge']):''; ?>
            <?php if($nfield_type == '1') { echo 'Yes'; } ?>
            <?php if($nfield_type == '0') { echo 'No'; } ?>

           </p>   	
           </li> 
          
           <li><label><?php echo __('night_timing_from'); ?></label>      
			   <p>
					<?php echo isset($model_details[0]['night_timing_from'])? trim($model_details[0]['night_timing_from']):''; ?>
			   </p>   	
           </li>
           
           <li>
			  <label><?php echo __('night_timing_to'); ?></label>     
			   <p>
					<?php echo isset($model_details[0]['night_timing_to'])? trim($model_details[0]['night_timing_to']):''; ?>
			   </p>   	
           </li>
           
           <li>
			  <label><?php echo __('night_fare'); ?></label>       
			   <p>
					<?php echo isset($model_details[0]['night_fare'])? trim($model_details[0]['night_fare']):''; ?>
			   </p>   	
           </li>
           <?php } ?>
           <?php 
            $ec = $model_details[0]['evening_charge']; 
           if($ec==1){ ?>
            <li>
           <label><?php echo __('evening_charge'); ?></label>       
	       <p>
			   <?php $efield_type =''; $efield_type =  isset($model_details[0]['evening_charge']) ? trim($model_details[0]['evening_charge']):''; ?>
            <?php if($efield_type == '1') { echo 'Yes'; } ?>
            <?php if($efield_type == '0') { echo 'No'; } ?>

           </p>   	
           </li> 
           
           <li>
			   <label><?php echo __('evening_timing_from'); ?></label>        
			   <p>
					<?php echo isset($model_details[0]['evening_timing_from'])? trim($model_details[0]['evening_timing_from']):''; ?>
			   </p>   	
           </li>
           
           <li>
			 <label><?php echo __('evening_timing_to'); ?></label>       
			   <p>
					<?php echo isset($model_details[0]['evening_timing_to'])? trim($model_details[0]['evening_timing_to']):''; ?>
			   </p>   	
           </li>
           
           <li>
			   <label><?php echo __('evening_fare'); ?></label>       
			   <p>
					<?php echo isset($model_details[0]['evening_fare'])? trim($model_details[0]['evening_fare']):''; ?>
			   </p>   	
           </li>
           <?php } ?>
	
			<li>
				<label><?php echo __('description'); ?></label>       
				<p>
					<?php echo isset($model_details[0]['description'])? trim($model_details[0]['description']):''; ?>
				</p>   	
			</li>
	  <!--Fare Details end-->
           </ul>
        </form>
        </div>
    </div>
</div>
<?php
	$url = $_SERVER['REQUEST_URI'];
	$spliturl = explode('/',$url);
	$toggleurl = $spliturl[2];
	if($toggleurl == 'fareinfo')
	{ ?>
		<script type="text/javascript">
		$(document).ready(function(){
		 $("#companyname").focus(); 
			
		});
		</script>
<?php
	}
	else
	{
 ?>
<script type="text/javascript">
$(document).ready(function(){
 $("#companyname").focus(); 
});
</script>
<?php } ?>
